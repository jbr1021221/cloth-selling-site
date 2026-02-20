@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-2xl font-bold text-white mx-auto mb-4">C</div>
            <h1 class="text-3xl font-bold text-white">Welcome back</h1>
            <p class="text-gray-500 mt-2">Sign in to your ClothStore account</p>
        </div>

        <div class="card p-8">
            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl p-4 mb-6">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="input" placeholder="your@email.com">
                </div>
                <div>
                    <label class="label">Password</label>
                    <input type="password" name="password" required
                           class="input" placeholder="••••••••">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="accent-indigo-600 w-4 h-4">
                        <span class="text-sm text-gray-400">Remember me</span>
                    </label>
                </div>
                <button type="submit" class="btn-primary w-full py-3.5 text-base">
                    Sign In →
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-indigo-400 hover:underline font-medium">Create one free</a>
            </p>
        </div>
    </div>
</div>
@endsection
