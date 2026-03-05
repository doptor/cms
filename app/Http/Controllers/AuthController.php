<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    // ─── VIEWS ────────────────────────────────────────────────────

    public function showLogin(): View        { return view('auth.login'); }
    public function showRegister(): View     { return view('auth.register'); }
    public function showForgotPassword():View{ return view('auth.forgot-password'); }
    public function showResetForm(string $token): View { return view('auth.reset-password', compact('token')); }

    // ─── LOGIN ────────────────────────────────────────────────────

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Rate limit: 5 attempts per minute per IP
        $key = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json(['message' => "Too many attempts. Try again in {$seconds}s."], 429);
        }

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($key, 60);
            return response()->json(['message' => 'Invalid email or password.'], 401);
        }

        RateLimiter::clear($key);

        $user = Auth::user();

        // Block inactive accounts
        if (!$user->is_active) {
            Auth::logout();
            return response()->json(['message' => 'Your account has been suspended.'], 403);
        }

        // Update last login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $request->session()->regenerate();

        return response()->json([
            'success'  => true,
            'redirect' => route('dashboard'),
            'user'     => ['name' => $user->name, 'email' => $user->email],
        ]);
    }

    // ─── REGISTER ─────────────────────────────────────────────────

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'                  => 'required|string|max:100|min:2',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'timezone' => 'UTC',
            'locale'   => 'en',
        ]);

        // Assign default role
        $user->assignRole('user');

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success'  => true,
            'redirect' => route('dashboard'),
            'message'  => 'Welcome to RyaanCMS! 🎉',
        ], 201);
    }

    // ─── LOGOUT ───────────────────────────────────────────────────

    public function logout(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // ─── FORGOT PASSWORD ──────────────────────────────────────────

    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['success' => true, 'message' => 'Reset link sent to your email.'])
            : response()->json(['message' => 'Email not found.'], 422);
    }

    // ─── RESET PASSWORD ───────────────────────────────────────────

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                Auth::login($user);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['success' => true, 'redirect' => route('dashboard')])
            : response()->json(['message' => 'Invalid or expired reset link.'], 422);
    }
}
