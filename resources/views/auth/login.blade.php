@extends('layouts.app')
@section('title', 'Sign In')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-16 bg-[#F8F8F8]">

    <div class="w-full max-w-md bg-white border border-gray-100 p-8 sm:p-12 shadow-sm">
        <div class="text-center mb-10">
            <h1 class="playfair text-3xl font-bold text-[#1A1A1A] mb-3 uppercase tracking-wider">Welcome Back</h1>
            <p class="text-xs text-gray-400 tracking-widest uppercase">Sign in to your account</p>
        </div>

        @if($errors->any())
            <div class="border border-red-200 bg-red-50 text-red-600 text-[11px] uppercase tracking-widest font-bold px-4 py-3 mb-8">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-2 block">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" placeholder="your@email.com">
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#1A1A1A] mb-2 block">Password</label>
                <input type="password" name="password" required
                       class="w-full border-b border-gray-300 py-2 focus:outline-none focus:border-[#C9A84C] text-[#1A1A1A] text-sm bg-transparent placeholder-gray-300" placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between pt-2">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" name="remember" class="w-4 h-4 accent-[#1A1A1A]">
                    <span class="text-[10px] uppercase tracking-widest text-gray-500 group-hover:text-[#1A1A1A] font-semibold transition-colors">Remember me</span>
                </label>
                {{-- To be added if there was a reset password route:
                <a href="#" class="text-[10px] uppercase tracking-widest text-gray-500 hover:text-[#1A1A1A] font-semibold border-b border-transparent hover:border-[#1A1A1A] transition-all">Forgot?</a> --}}
            </div>

            <button type="submit" class="btn-primary w-full py-4 mt-4 bg-[#1A1A1A] hover:bg-black text-[11px]">
                Sign In
            </button>
        </form>

        <p class="text-center text-[10px] uppercase tracking-widest text-gray-400 mt-8 pt-8 border-t border-gray-100">
            Don't have an account? <br>
            <a href="{{ route('register') }}" class="text-[#1A1A1A] font-bold hover:text-[#C9A84C] transition-colors mt-2 inline-block">Create One Free</a>
        </p>
    </div>
</div>
@endsection
