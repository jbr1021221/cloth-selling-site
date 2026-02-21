@extends('layouts.admin')
@section('title', 'Global Settings')
@section('page-title', 'Global Settings')

@section('content')
<div class="max-w-5xl mx-auto pb-12" x-data="settingsApp()">

    {{-- Header --}}
    <div class="bg-white border border-gray-100 shadow-sm p-6 mb-6 flex justify-between items-center relative overflow-hidden">
        <div class="absolute right-0 top-0 w-32 h-32 bg-gray-50 rounded-bl-full -mr-16 -mt-16 z-0"></div>
        <div class="relative z-10">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3">App Configuration</h2>
            <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest ml-3">Manage all global store variables dynamically</p>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex items-center overflow-x-auto border-b border-gray-200 mb-8 scrollbar-hide">
        <template x-for="t in tabs" :key="t.id">
            <button @click="tab = t.id" class="px-6 py-4 text-[10px] font-bold uppercase tracking-[0.2em] transition-colors relative whitespace-nowrap" :class="tab === t.id ? 'text-[#1A1A1A]' : 'text-gray-400 hover:text-[#1A1A1A]'">
                <span x-text="t.name"></span>
                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-[#C9A84C] transition-transform duration-300" x-show="tab === t.id" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-x-0" x-transition:enter-end="scale-x-100"></div>
            </button>
        </template>
    </div>

    {{-- Success Toast --}}
    <div x-show="toastShow" x-transition x-cloak class="fixed bottom-6 right-6 bg-[#1A1A1A] text-white px-6 py-4 flex items-center gap-3 shadow-xl z-50">
        <div class="w-6 h-6 rounded-full bg-green-500/20 text-green-400 flex items-center justify-center">✓</div>
        <p class="text-[10px] font-bold tracking-widest uppercase">Settings saved successfully!</p>
    </div>

    <div class="bg-white border border-gray-100 shadow-sm p-8">
        {{-- 1. GENERAL TAB --}}
        <div x-show="tab === 'general'" x-cloak>
            <form @submit.prevent="saveSettings('general', $event)">
                <div class="grid lg:grid-cols-2 gap-8 mb-8">
                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Store Name</label>
                            <input type="text" name="store_name" value="{{ $settings['store_name'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Store Tagline</label>
                            <input type="text" name="store_tagline" value="{{ $settings['store_tagline'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Announcement Bar Text</label>
                            <input type="text" name="announcement_text" value="{{ $settings['announcement_text'] ?? '' }}" placeholder="Separate items with | like: Free Delivery | COD Available" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Store Logo (Replaces Text)</label>
                            @if(isset($settings['store_logo']) && $settings['store_logo'])
                                <img src="{{ $settings['store_logo'] }}" class="h-10 mb-2 border border-gray-100 p-1 bg-gray-50 object-contain">
                            @endif
                            <input type="file" name="store_logo" class="w-full bg-gray-50 border border-gray-200 px-4 py-2 text-xs">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Favicon</label>
                            @if(isset($settings['store_favicon']) && $settings['store_favicon'])
                                <img src="{{ $settings['store_favicon'] }}" class="h-8 mb-2 border border-gray-100 p-1 bg-gray-50 object-contain">
                            @endif
                            <input type="file" name="store_favicon" class="w-full bg-gray-50 border border-gray-200 px-4 py-2 text-xs">
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Contact Email</label>
                            <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Contact Phone</label>
                            <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">WhatsApp Number (Floating Button)</label>
                            <input type="text" name="whatsapp_number" value="{{ $settings['whatsapp_number'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Copyright Text</label>
                            <input type="text" name="copyright_text" value="{{ $settings['copyright_text'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Store Address</label>
                    <textarea name="store_address" rows="3" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C] resize-none">{{ $settings['store_address'] ?? '' }}</textarea>
                </div>

                <div class="text-right border-t border-gray-100 pt-6">
                    <button type="submit" :disabled="saving" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white px-8 py-3 text-[10px] font-bold uppercase tracking-widest shadow-sm disabled:opacity-50 transition-colors">
                        <span x-show="!saving">Save General Settings</span>
                        <span x-show="saving" x-cloak>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- 2. SMS & NOTIFICATIONS --}}
        <div x-show="tab === 'sms'" x-cloak>
            <form @submit.prevent="saveSettings('sms', $event)">
                <div class="grid lg:grid-cols-2 gap-12 mb-8">
                    <div class="space-y-6">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-2 mb-4">API Configurations</h3>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Alpha SMS API Key</label>
                            <input type="password" name="alpha_sms_api_key" value="{{ $settings['alpha_sms_api_key'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">SMS Sender ID</label>
                            <input type="text" name="sms_sender_id" value="{{ $settings['sms_sender_id'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Admin Phone (For Alerts)</label>
                            <input type="text" name="admin_phone_number" value="{{ $settings['admin_phone_number'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                    </div>

                    <div class="space-y-6 lg:border-l lg:border-gray-100 lg:pl-12">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-2 mb-4">Triggers & Toggles</h3>
                        
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="sms_on_new_order_customer" value="1" class="w-4 h-4 text-[#C9A84C] focus:ring-[#C9A84C]" {{ ($settings['sms_on_new_order_customer'] ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="text-[11px] font-bold text-gray-600 uppercase tracking-widest text-sm block">Customer SMS on New Order</span>
                        </label>
                        
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="sms_on_order_status_customer" value="1" class="w-4 h-4 text-[#C9A84C] focus:ring-[#C9A84C]" {{ ($settings['sms_on_order_status_customer'] ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="text-[11px] font-bold text-gray-600 uppercase tracking-widest text-sm block">Customer SMS on Status Change</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="sms_on_new_order_admin" value="1" class="w-4 h-4 text-[#C9A84C] focus:ring-[#C9A84C]" {{ ($settings['sms_on_new_order_admin'] ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-widest text-sm block">Admin SMS on New Order</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="email_on_new_order_admin" value="1" class="w-4 h-4 text-[#C9A84C] focus:ring-[#C9A84C]" {{ ($settings['email_on_new_order_admin'] ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-widest text-sm block">Admin Email on New Order</span>
                        </label>
                    </div>
                </div>

                <div class="grid lg:grid-cols-3 gap-6 pt-6 border-t border-gray-100 mb-8">
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Admin Notification Email</label>
                        <input type="email" name="admin_notification_email" value="{{ $settings['admin_notification_email'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Email From Name</label>
                        <input type="text" name="email_from_name" value="{{ $settings['email_from_name'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Email From Address</label>
                        <input type="email" name="email_from_address" value="{{ $settings['email_from_address'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                    </div>
                </div>

                <div class="text-right border-t border-gray-100 pt-6">
                    <button type="submit" :disabled="saving" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white px-8 py-3 text-[10px] font-bold uppercase tracking-widest shadow-sm disabled:opacity-50 transition-colors">
                        <span x-show="!saving">Save Notifications</span>
                        <span x-show="saving" x-cloak>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- 3. PAYMENT --}}
        <div x-show="tab === 'payment'" x-cloak>
            <form @submit.prevent="saveSettings('payment', $event)">
                <div class="grid lg:grid-cols-2 gap-12 mb-8">
                    
                    {{-- COD --}}
                    <div class="space-y-6">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-2 mb-4">Cash On Delivery</h3>
                        
                        <label class="flex items-center gap-3 cursor-pointer bg-gray-50 border border-gray-200 p-4">
                            <input type="checkbox" name="cod_enabled" value="1" class="w-4 h-4 text-[#C9A84C] focus:ring-[#C9A84C]" {{ ($settings['cod_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="text-[11px] font-bold text-[#1A1A1A] uppercase tracking-widest text-sm block">Enable COD globally</span>
                        </label>

                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-2">Checkout COD Message</label>
                            <input type="text" name="cod_message" value="{{ $settings['cod_message'] ?? '' }}" class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-2">Product Badge Text</label>
                            <input type="text" name="cod_badge_text" value="{{ $settings['cod_badge_text'] ?? '' }}" class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-2">Minimum Order for COD (৳0 to allow all)</label>
                            <input type="number" name="cod_min_order" value="{{ $settings['cod_min_order'] ?? '0' }}" class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                    </div>

                    {{-- SSLCOMMERZ --}}
                    <div class="space-y-6 lg:border-l lg:border-gray-100 lg:pl-12">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-blue-500 pl-2 mb-4">SSLCommerz (bKash/Cards)</h3>
                        
                        <label class="flex items-center gap-3 cursor-pointer bg-blue-50/30 border border-blue-100 p-4">
                            <input type="checkbox" name="sslcommerz_enabled" value="1" class="w-4 h-4 text-blue-500 focus:ring-blue-500" {{ ($settings['sslcommerz_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="text-[11px] font-bold text-blue-900 uppercase tracking-widest text-sm block">Enable SSLCommerz Payment Gateway</span>
                        </label>

                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-2">Store ID</label>
                            <input type="text" name="sslcommerz_store_id" value="{{ $settings['sslcommerz_store_id'] ?? '' }}" class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-2">Store Password</label>
                            <input type="password" name="sslcommerz_store_password" value="{{ $settings['sslcommerz_store_password'] ?? '' }}" class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-2">Gateway Mode</label>
                            <div class="flex gap-6">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="sslcommerz_mode" value="sandbox" class="text-blue-500 focus:ring-blue-500" {{ ($settings['sslcommerz_mode'] ?? 'sandbox') == 'sandbox' ? 'checked' : '' }}>
                                    <span class="text-xs font-bold text-gray-600 uppercase tracking-widest">Sandbox (Test)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="sslcommerz_mode" value="live" class="text-green-500 focus:ring-green-500" {{ ($settings['sslcommerz_mode'] ?? 'sandbox') == 'live' ? 'checked' : '' }}>
                                    <span class="text-xs font-bold text-gray-600 uppercase tracking-widest border-b border-green-500">Live (Production)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right border-t border-gray-100 pt-6">
                    <button type="submit" :disabled="saving" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white px-8 py-3 text-[10px] font-bold uppercase tracking-widest shadow-sm disabled:opacity-50 transition-colors">
                        <span x-show="!saving">Save Payment config</span>
                        <span x-show="saving" x-cloak>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- 4. SHIPPING --}}
        <div x-show="tab === 'shipping'" x-cloak>
            <form @submit.prevent="saveSettings('shipping', $event)">
                <div class="grid lg:grid-cols-2 gap-12 mb-8">
                    <div class="space-y-6">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-2 mb-4">Charges</h3>
                        
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-2">Default Charge - Dhaka</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">৳</span>
                                <input type="number" name="shipping_charge_dhaka" value="{{ $settings['shipping_charge_dhaka'] ?? '60' }}" class="w-full bg-gray-50 border border-gray-200 pl-8 pr-4 py-2.5 text-sm font-bold focus:outline-none focus:border-[#C9A84C]">
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-2">Default Charge - Outside Dhaka</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">৳</span>
                                <input type="number" name="shipping_charge_outside" value="{{ $settings['shipping_charge_outside'] ?? '120' }}" class="w-full bg-gray-50 border border-gray-200 pl-8 pr-4 py-2.5 text-sm font-bold focus:outline-none focus:border-[#C9A84C]">
                            </div>
                        </div>
                        
                        <label class="flex items-center gap-3 cursor-pointer mt-6">
                            <input type="checkbox" name="show_delivery_estimate" value="1" class="w-4 h-4 text-[#C9A84C] focus:ring-[#C9A84C]" {{ ($settings['show_delivery_estimate'] ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="text-[11px] font-bold text-gray-600 uppercase tracking-widest text-sm block">Show delivery estimate on products</span>
                        </label>
                    </div>

                    <div class="space-y-6 lg:border-l lg:border-gray-100 lg:pl-12">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-green-500 pl-2 mb-4">Promotions & Times</h3>
                        
                        <label class="flex items-center gap-3 cursor-pointer bg-green-50/30 border border-green-100 p-4 mb-4">
                            <input type="checkbox" name="free_shipping_enabled" value="1" class="w-4 h-4 text-green-500 focus:ring-green-500" {{ ($settings['free_shipping_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                            <span class="text-[11px] font-bold text-green-700 uppercase tracking-widest text-sm block">Offer Free Shipping globally</span>
                        </label>

                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-2">Free Shipping Min Box Value</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-green-500 font-bold">৳</span>
                                <input type="number" name="free_shipping_min_order" value="{{ $settings['free_shipping_min_order'] ?? '999' }}" class="w-full bg-white border border-green-200 pl-8 pr-4 py-2.5 text-sm font-bold text-green-700 focus:outline-none focus:border-green-500">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-2">Delivery ETA string - Dhaka</label>
                            <input type="text" name="estimated_delivery_dhaka" value="{{ $settings['estimated_delivery_dhaka'] ?? '1-2 days' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-2">Delivery ETA string - Outside</label>
                            <input type="text" name="estimated_delivery_outside" value="{{ $settings['estimated_delivery_outside'] ?? '3-5 days' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                    </div>
                </div>

                <div class="text-right border-t border-gray-100 pt-6">
                    <button type="submit" :disabled="saving" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white px-8 py-3 text-[10px] font-bold uppercase tracking-widest shadow-sm disabled:opacity-50 transition-colors">
                        <span x-show="!saving">Save Logistics</span>
                        <span x-show="saving" x-cloak>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- 5. SOCIAL MEDIA --}}
        <div x-show="tab === 'social'" x-cloak>
            <form @submit.prevent="saveSettings('social', $event)">
                <div class="grid lg:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Facebook Page URL</label>
                        <input type="text" name="social_facebook" value="{{ $settings['social_facebook'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Instagram Profile URL</label>
                        <input type="text" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">YouTube Channel URL</label>
                        <input type="text" name="social_youtube" value="{{ $settings['social_youtube'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">TikTok Profile URL</label>
                        <input type="text" name="social_tiktok" value="{{ $settings['social_tiktok'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Twitter / X URL</label>
                        <input type="text" name="social_twitter" value="{{ $settings['social_twitter'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                    </div>
                </div>

                <div class="text-right border-t border-gray-100 pt-6">
                    <button type="submit" :disabled="saving" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white px-8 py-3 text-[10px] font-bold uppercase tracking-widest shadow-sm disabled:opacity-50 transition-colors">
                        <span x-show="!saving">Save Social Links</span>
                        <span x-show="saving" x-cloak>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- 6. SEO --}}
        <div x-show="tab === 'seo'" x-cloak>
            <form @submit.prevent="saveSettings('seo', $event)">
                <div class="grid lg:grid-cols-2 gap-12 mb-8">
                    <div class="space-y-6">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-2 mb-4">Metadata</h3>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Meta Title (Brand)</label>
                            <input type="text" name="seo_meta_title" value="{{ $settings['seo_meta_title'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div x-data="{ desc: '{{ addslashes($settings['seo_meta_description'] ?? '') }}' }">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] flex justify-between mb-2">
                                <span>Meta Description</span>
                                <span class="text-gray-400 font-normal normal-case"><span x-text="desc.length"></span>/160</span>
                            </label>
                            <textarea name="seo_meta_description" x-model="desc" maxlength="160" rows="3" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C] resize-none"></textarea>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Meta Keywords (Comma separated)</label>
                            <input type="text" name="seo_meta_keywords" value="{{ $settings['seo_meta_keywords'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-[#C9A84C]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] block mb-2">Open Graph Image (Default Shared Logo)</label>
                            @if(isset($settings['seo_og_image']) && $settings['seo_og_image'])
                                <img src="{{ $settings['seo_og_image'] }}" class="h-20 mb-2 border border-gray-100 object-cover">
                            @endif
                            <input type="file" name="seo_og_image" class="w-full bg-gray-50 border border-gray-200 px-4 py-2 text-xs">
                        </div>
                    </div>

                    <div class="space-y-6 lg:border-l lg:border-gray-100 lg:pl-12">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-indigo-500 pl-2 mb-4">Tracking Scripts</h3>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-600 block mb-2 text-[#1A1A1A]">Google Analytics ID</label>
                            <p class="text-[9px] text-gray-400 mb-2 font-mono">e.g. G-XXXXXXXXX</p>
                            <input type="text" name="google_analytics_id" value="{{ $settings['google_analytics_id'] ?? '' }}" class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 font-mono">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-gray-600 block mb-2 text-[#1A1A1A]">Facebook Pixel ID</label>
                            <input type="text" name="facebook_pixel_id" value="{{ $settings['facebook_pixel_id'] ?? '' }}" class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 font-mono">
                        </div>
                    </div>
                </div>

                <div class="text-right border-t border-gray-100 pt-6">
                    <button type="submit" :disabled="saving" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white px-8 py-3 text-[10px] font-bold uppercase tracking-widest shadow-sm disabled:opacity-50 transition-colors">
                        <span x-show="!saving">Save SEO</span>
                        <span x-show="saving" x-cloak>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
function settingsApp() {
    return {
        tab: 'general',
        saving: false,
        toastShow: false,
        tabs: [
            { id: 'general', name: 'General' },
            { id: 'sms', name: 'SMS & Notifications' },
            { id: 'payment', name: 'Payment' },
            { id: 'shipping', name: 'Shipping' },
            { id: 'social', name: 'Social Media' },
            { id: 'seo', name: 'SEO' },
        ],
        async saveSettings(group, event) {
            this.saving = true;
            try {
                let formData = new FormData(event.target);
                formData.append('_token', '{{ csrf_token() }}');
                
                const res = await fetch(`/admin/settings/${group}`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                
                if (res.ok) {
                    this.toastShow = true;
                    setTimeout(() => { this.toastShow = false; }, 3000);
                } else {
                    alert('Error saving settings');
                }
            } catch (e) {
                console.error(e);
                alert('Connection failure');
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>
@endsection
