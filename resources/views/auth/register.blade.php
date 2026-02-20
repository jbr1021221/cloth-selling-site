@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-2xl font-bold text-white mx-auto mb-4">C</div>
            <h1 class="text-3xl font-bold text-white">Create Account</h1>
            <p class="text-gray-500 mt-2">Join ClothStore — it's free!</p>
        </div>

        <div class="card p-8">
            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl p-4 mb-6">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="label">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="input" placeholder="Your name">
                </div>
                <div>
                    <label class="label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="input" placeholder="your@email.com">
                </div>
                <div>
                    <label class="label">Phone Number</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           class="input" placeholder="01XXXXXXXXX">
                </div>
                <div>
                    <label class="label">Password</label>
                    <input type="password" name="password" required
                           class="input" placeholder="Min. 8 characters">
                </div>
                <div>
                    <label class="label">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           class="input" placeholder="Repeat password">
                </div>
                <button type="submit" class="btn-primary w-full py-3.5 text-base">
                    Create Account →
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Already have an account?
                <a href="{{ route('login') }}" class="text-indigo-400 hover:underline font-medium">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
