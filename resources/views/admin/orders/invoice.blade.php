<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice - #{{ $order->order_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; background: #fff; color: #1a1a1a; }
        .playfair { font-family: 'Playfair Display', serif; }
        @media print {
            .no-print { display: none !important; }
            @page { margin: 0; size: auto; }
            body { margin: 1.6cm; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Top Action Bar (Hides on Print) --}}
    <div class="no-print bg-[#1A1A1A] text-white py-4 px-8 flex justify-between items-center shadow-lg sticky top-0 z-50">
        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-white transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Order
        </a>
        <div class="flex gap-4 items-center">
            <span class="text-[10px] uppercase tracking-widest text-[#C9A84C] font-bold">Press Ctrl+P to Print</span>
            <button onclick="window.print()" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white text-[10px] font-bold uppercase tracking-widest px-6 py-2 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Invoice
            </button>
        </div>
    </div>

    {{-- A4 Invoice Container --}}
    <div class="max-w-4xl mx-auto bg-white p-12 sm:p-20 shadow-sm print:shadow-none print:p-0 my-10 print:my-0 border border-gray-100 print:border-none">
        
        {{-- Header Section --}}
        <div class="flex justify-between items-start border-b-2 border-[#1A1A1A] pb-8 mb-10">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 flex items-center justify-center bg-[#1A1A1A] text-[#C9A84C] font-bold text-2xl playfair shrink-0">C</div>
                    <h1 class="font-bold text-[#1A1A1A] tracking-[0.2em] uppercase text-xl">ClothStore</h1>
                </div>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-2">123 Fashion Street, Block B</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest">Dhaka 1212, Bangladesh</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest">+880 1234 567890</p>
                <p class="text-[10px] text-[#C9A84C] uppercase tracking-widest font-bold mt-1">support@clothstore.com</p>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-extrabold text-[#C9A84C] uppercase tracking-widest mb-2">Invoice</h2>
                <p class="text-[11px] font-bold text-[#1A1A1A] uppercase tracking-widest block"><span class="text-gray-400">#</span>{{ $order->order_number }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest block mt-1">Date: {{ $order->created_at->format('d M, Y') }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest block mt-1">Status: <span class="font-bold text-[#1A1A1A]">{{ strtoupper($order->payment_status) }}</span></p>
            </div>
        </div>

        {{-- Bill To Section --}}
        @php $address = $order->delivery_address ?? []; @endphp
        <div class="mb-12">
            <h3 class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#C9A84C] mb-4 border-l-2 border-[#C9A84C] pl-2">Bill To</h3>
            <p class="text-sm font-bold text-[#1A1A1A] uppercase tracking-widest">{{ $address['name'] ?? 'N/A' }}</p>
            <p class="text-[11px] text-gray-500 uppercase tracking-widest mt-1">{{ $address['address'] ?? 'N/A' }}</p>
            <p class="text-[11px] text-gray-500 uppercase tracking-widest mt-0.5">{{ $address['district'] ?? 'N/A' }}</p>
            <p class="text-[11px] text-[#1A1A1A] font-bold tracking-widest mt-2 border-t border-gray-100 pt-2 inline-block">{{ $address['phone'] ?? 'N/A' }}</p>
        </div>

        {{-- Data Table --}}
        <table class="w-full text-left border-collapse mb-10">
            <thead>
                <tr>
                    <th class="py-3 px-4 text-[9px] font-bold uppercase tracking-widest text-white bg-[#1A1A1A] border-r border-[#333] w-12 text-center">#</th>
                    <th class="py-3 px-4 text-[9px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#FAFAF8] border-b-2 border-[#1A1A1A]">Item Description</th>
                    <th class="py-3 px-4 text-[9px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#FAFAF8] border-b-2 border-[#1A1A1A] text-center w-20">Qty</th>
                    <th class="py-3 px-4 text-[9px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#FAFAF8] border-b-2 border-[#1A1A1A] text-right w-28">Unit Price</th>
                    <th class="py-3 px-4 text-[9px] font-bold uppercase tracking-widest text-[#1A1A1A] bg-[#FAFAF8] border-b-2 border-[#1A1A1A] text-right w-32">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($order->items as $idx => $item)
                    <tr>
                        <td class="py-4 px-4 text-[10px] font-bold text-gray-400 text-center">{{ $idx + 1 }}</td>
                        <td class="py-4 px-4">
                            <p class="text-[11px] font-bold text-[#1A1A1A] uppercase tracking-widest">{{ $item->name }}</p>
                            @if($item->size || $item->color)
                                <p class="text-[9px] text-gray-400 uppercase tracking-widest mt-1">
                                    @if($item->size) Size: {{ $item->size }} @endif
                                    @if($item->size && $item->color) | @endif
                                    @if($item->color) Color: {{ $item->color }} @endif
                                </p>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-[11px] font-bold text-gray-600 text-center">{{ $item->quantity }}</td>
                        <td class="py-4 px-4 text-[11px] font-bold text-gray-600 text-right">৳{{ number_format($item->price) }}</td>
                        <td class="py-4 px-4 text-[11px] font-bold text-[#C9A84C] text-right">৳{{ number_format($item->price * $item->quantity) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals Area --}}
        <div class="flex justify-end border-t border-[#1A1A1A] pt-8">
            <div class="w-80">
                <div class="flex justify-between py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                    <span>Subtotal</span>
                    <span class="text-[#1A1A1A]">৳{{ number_format($order->total_amount) }}</span>
                </div>
                
                <div class="flex justify-between py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                    <span>Shipping Handling</span>
                    <span class="text-[#1A1A1A]">৳{{ number_format($order->shipping_charge) }}</span>
                </div>

                @if($order->discount > 0)
                <div class="flex justify-between py-2 text-[10px] font-bold uppercase tracking-widest text-red-500">
                    <span>Special Discount</span>
                    <span>-৳{{ number_format($order->discount) }}</span>
                </div>
                @endif
                
                @if($order->coupon_discount > 0)
                <div class="flex justify-between py-2 text-[10px] font-bold uppercase tracking-widest text-red-500">
                    <span>Coupon ({{ $order->coupon_code }})</span>
                    <span>-৳{{ number_format($order->coupon_discount) }}</span>
                </div>
                @endif

                <div class="flex justify-between py-4 mt-2 border-t border-gray-100 bg-[#FAFAF8]">
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] ml-2">Total Paid</span>
                    <span class="text-xl font-extrabold text-[#C9A84C] mr-2">৳{{ number_format($order->final_amount) }}</span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-20 pt-8 border-t border-gray-100 text-center">
            <p class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2">Thank you for your business!</p>
            <p class="text-[9px] uppercase tracking-widest text-gray-400">If you have any questions about this invoice, please contact support.</p>
        </div>

    </div>

</body>
</html>
