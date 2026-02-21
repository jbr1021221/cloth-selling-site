<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'store_name', 'value' => 'ClothStore', 'group' => 'general'],
            ['key' => 'store_tagline', 'value' => 'Premium Fashion Bangladesh', 'group' => 'general'],
            ['key' => 'store_logo', 'value' => '', 'group' => 'general'],
            ['key' => 'store_favicon', 'value' => '', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'support@clothstore.com', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => '01700000000', 'group' => 'general'],
            ['key' => 'whatsapp_number', 'value' => '01700000000', 'group' => 'general'],
            ['key' => 'store_address', 'value' => 'Dhaka, Bangladesh', 'group' => 'general'],
            ['key' => 'copyright_text', 'value' => '© 2025 ClothStore. All rights reserved', 'group' => 'general'],

            // SMS & Notifications
            ['key' => 'alpha_sms_api_key', 'value' => '', 'group' => 'sms'],
            ['key' => 'sms_sender_id', 'value' => '', 'group' => 'sms'],
            ['key' => 'admin_phone_number', 'value' => '', 'group' => 'sms'],
            ['key' => 'sms_on_new_order_customer', 'value' => '1', 'group' => 'sms'],
            ['key' => 'sms_on_order_status_customer', 'value' => '1', 'group' => 'sms'],
            ['key' => 'sms_on_new_order_admin', 'value' => '0', 'group' => 'sms'],
            ['key' => 'email_on_new_order_admin', 'value' => '0', 'group' => 'sms'],
            ['key' => 'admin_notification_email', 'value' => 'admin@clothstore.com', 'group' => 'sms'],
            ['key' => 'email_from_name', 'value' => 'ClothStore', 'group' => 'sms'],
            ['key' => 'email_from_address', 'value' => 'no-reply@clothstore.com', 'group' => 'sms'],

            // Payment
            ['key' => 'cod_enabled', 'value' => '1', 'group' => 'payment'],
            ['key' => 'sslcommerz_enabled', 'value' => '1', 'group' => 'payment'],
            ['key' => 'sslcommerz_store_id', 'value' => '', 'group' => 'payment'],
            ['key' => 'sslcommerz_store_password', 'value' => '', 'group' => 'payment'],
            ['key' => 'sslcommerz_mode', 'value' => 'sandbox', 'group' => 'payment'],
            ['key' => 'cod_message', 'value' => 'Pay when your order arrives', 'group' => 'payment'],
            ['key' => 'cod_badge_text', 'value' => '✅ Cash on Delivery Available', 'group' => 'payment'],
            ['key' => 'cod_min_order', 'value' => '0', 'group' => 'payment'],

            // Shipping
            ['key' => 'free_shipping_min_order', 'value' => '999', 'group' => 'shipping'],
            ['key' => 'shipping_charge_dhaka', 'value' => '60', 'group' => 'shipping'],
            ['key' => 'shipping_charge_outside', 'value' => '120', 'group' => 'shipping'],
            ['key' => 'estimated_delivery_dhaka', 'value' => '1-2 days', 'group' => 'shipping'],
            ['key' => 'estimated_delivery_outside', 'value' => '3-5 days', 'group' => 'shipping'],
            ['key' => 'free_shipping_enabled', 'value' => '1', 'group' => 'shipping'],
            ['key' => 'show_delivery_estimate', 'value' => '1', 'group' => 'shipping'],

            // Social Media
            ['key' => 'social_facebook', 'value' => '', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => '', 'group' => 'social'],
            ['key' => 'social_youtube', 'value' => '', 'group' => 'social'],
            ['key' => 'social_tiktok', 'value' => '', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => '', 'group' => 'social'],

            // SEO
            ['key' => 'seo_meta_title', 'value' => 'ClothStore — Premium Clothing Bangladesh', 'group' => 'seo'],
            ['key' => 'seo_meta_description', 'value' => 'Discover premium clothing at ClothStore — your destination for modern fashion in Bangladesh.', 'group' => 'seo'],
            ['key' => 'seo_meta_keywords', 'value' => 'fashion, clothing, bangladesh, clothstore', 'group' => 'seo'],
            ['key' => 'google_analytics_id', 'value' => '', 'group' => 'seo'],
            ['key' => 'facebook_pixel_id', 'value' => '', 'group' => 'seo'],
            ['key' => 'seo_og_image', 'value' => '', 'group' => 'seo'],
        ];

        foreach ($settings as $setting) {
            Setting::set($setting['key'], $setting['value'], $setting['group']);
        }
    }
}
