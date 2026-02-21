@extends('layouts.app')
@section('title', 'Create Account')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-16 bg-[#F8F8F8]">

    <div class="w-full max-w-md bg-white border border-gray-100 p-8 sm:p-12 shadow-sm">
        <div class="text-center mb-10">
            <h1 class="playfair text-3xl font-bold text-[#1A1A1A] mb-3 uppercase tracking-wider">Register</h1>
            <p class="text-xs text-gray-400 tracking-widest uppercase">Join the members club</p>
        </div>

        @if($errors->any())
            <div class="border border-red-200 bg-red-50 px-4 py-3 mb-8">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="text-[10px] font-bold uppercase tracking-widest text-red-600">• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <div>
                <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-2 block">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" placeholder="Your name">
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-2 block">Email Address *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" placeholder="your@email.com">
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-2 block">Phone Number</label>
                <input type="tel" name="phone" value="{{ old('phone') }}"
                       class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" placeholder="01XXXXXXXXX">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-2 block text-gray-400">Date of Birth <span class="text-[9px] lowercase italic">(for 100 pt bonus)</span></label>
                    <input type="date" name="dob" value="{{ old('dob') }}"
                           class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300 text-gray-500">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-2 block text-gray-400">Referral ID <span class="normal-case tracking-normal normal">(optional)</span></label>
                    <input type="number" name="referred_by_id" value="{{ old('referred_by_id', request('ref')) }}"
                           class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" placeholder="Friend's ID">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-2 block">Password *</label>
                    <input type="password" name="password" required
                           class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" placeholder="••••••••">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-2 block">Confirm *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn-primary w-full py-4 mt-6 bg-[#1A1A1A] hover:bg-black text-[11px]">
                Create Account
            </button>
        </form>

        <p class="text-center text-[10px] uppercase tracking-widest text-gray-400 mt-8 pt-8 border-t border-gray-100">
            Already have an account? <br>
            <a href="{{ route('login') }}" class="text-[#1A1A1A] font-bold hover:text-[#C9A84C] transition-colors mt-2 inline-block">Sign In</a>
        </p>

    </div>
</div>
@endsection
