<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return auth()->check() ? redirect()->route('home') : view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (auth()->user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('home'))->with('success', 'Welcome back, ' . auth()->user()->name . '!');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
    }

    public function showRegister()
    {
        return auth()->check() ? redirect()->route('home') : view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'phone'          => 'nullable|string|max:20',
            'dob'            => 'nullable|date',
            'referred_by_id' => 'nullable|exists:users,id',
            'password'       => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'dob'            => $request->dob,
            'referred_by_id' => $request->referred_by_id,
            'password'       => Hash::make($request->password),
            'role'           => 'customer',
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Welcome to ClothStore, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }
}
