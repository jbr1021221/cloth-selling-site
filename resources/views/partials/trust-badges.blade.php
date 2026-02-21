{{-- ============================================================
     Reusable Trust Badges Partial
     Usage:
       @include('partials.trust-badges')               ← standard (row)
       @include('partials.trust-badges', ['compact' => true])  ← compact (col)
     ============================================================ --}}
@php $compact = $compact ?? false; @endphp

<div class="trust-badges {{ $compact ? 'flex flex-col gap-2' : 'grid grid-cols-1 sm:grid-cols-3 gap-3' }} w-full">

    {{-- Badge 1: Cash on Delivery --}}
    <div class="trust-badge group flex items-center gap-3 rounded-xl border border-emerald-500/25 bg-emerald-500/8 px-4 py-3 transition-all duration-300 hover:border-emerald-500/50 hover:bg-emerald-500/15 {{ $compact ? '' : 'justify-center sm:flex-col sm:gap-2 sm:text-center sm:py-4' }}">
        <span class="text-2xl leading-none {{ $compact ? '' : 'sm:text-3xl' }}" aria-hidden="true">✅</span>
        <div class="{{ $compact ? '' : 'sm:text-center' }}">
            <p class="text-sm font-semibold text-emerald-400 leading-tight">Cash on Delivery</p>
            <p class="text-xs text-gray-500 mt-0.5 {{ $compact ? '' : 'hidden sm:block' }}">Pay when you receive</p>
        </div>
    </div>

    {{-- Badge 2: Easy Returns --}}
    <div class="trust-badge group flex items-center gap-3 rounded-xl border border-blue-500/25 bg-blue-500/8 px-4 py-3 transition-all duration-300 hover:border-blue-500/50 hover:bg-blue-500/15 {{ $compact ? '' : 'justify-center sm:flex-col sm:gap-2 sm:text-center sm:py-4' }}">
        <span class="text-2xl leading-none {{ $compact ? '' : 'sm:text-3xl' }}" aria-hidden="true">🔄</span>
        <div class="{{ $compact ? '' : 'sm:text-center' }}">
            <p class="text-sm font-semibold text-blue-400 leading-tight">Easy 7-Day Returns</p>
            <p class="text-xs text-gray-500 mt-0.5 {{ $compact ? '' : 'hidden sm:block' }}">Hassle-free exchange</p>
        </div>
    </div>

    {{-- Badge 3: 100% Authentic --}}
    <div class="trust-badge group flex items-center gap-3 rounded-xl border border-purple-500/25 bg-purple-500/8 px-4 py-3 transition-all duration-300 hover:border-purple-500/50 hover:bg-purple-500/15 {{ $compact ? '' : 'justify-center sm:flex-col sm:gap-2 sm:text-center sm:py-4' }}">
        <span class="text-2xl leading-none {{ $compact ? '' : 'sm:text-3xl' }}" aria-hidden="true">🛡️</span>
        <div class="{{ $compact ? '' : 'sm:text-center' }}">
            <p class="text-sm font-semibold text-purple-400 leading-tight">100% Authentic</p>
            <p class="text-xs text-gray-500 mt-0.5 {{ $compact ? '' : 'hidden sm:block' }}">Genuine products only</p>
        </div>
    </div>

</div>
