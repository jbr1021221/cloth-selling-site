<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settingsRaw = Setting::all();
        $settings = [];
        foreach ($settingsRaw as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request, $group)
    {
        $data = $request->except(['_token']);

        // Handle file uploads specially
        $fileKeys = ['store_logo', 'store_favicon', 'seo_og_image'];
        foreach ($fileKeys as $key) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $filename = time() . '_' . $key . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/settings'), $filename);
                $data[$key] = '/uploads/settings/' . $filename;
            }
        }

        // Handle checkboxes (toggles) that might be unchecked
        $toggleKeys = [
            'sms_on_new_order_customer', 
            'sms_on_order_status_customer', 
            'sms_on_new_order_admin', 
            'email_on_new_order_admin',
            'cod_enabled', 
            'sslcommerz_enabled',
            'free_shipping_enabled', 
            'show_delivery_estimate'
        ];
        
        foreach ($toggleKeys as $toggleKey) {
            if ($this->isKeyInGroup($toggleKey, $group)) {
                $data[$toggleKey] = $request->has($toggleKey) ? '1' : '0';
            }
        }

        foreach ($data as $key => $value) {
            if (in_array($key, $fileKeys) && !$request->hasFile($key)) {
                continue;
            }
            if (is_null($value) && !in_array($key, $fileKeys)) {
                $value = '';
            }
            Setting::set($key, $value, $group);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Settings saved successfully!']);
        }

        return back()->with('success', 'Settings saved successfully!');
    }

    private function isKeyInGroup($key, $group)
    {
        $map = [
            'sms' => ['sms_on_new_order_customer', 'sms_on_order_status_customer', 'sms_on_new_order_admin', 'email_on_new_order_admin'],
            'payment' => ['cod_enabled', 'sslcommerz_enabled'],
            'shipping' => ['free_shipping_enabled', 'show_delivery_estimate']
        ];
        
        return isset($map[$group]) && in_array($key, $map[$group]);
    }
}
