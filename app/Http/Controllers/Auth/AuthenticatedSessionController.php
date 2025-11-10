<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($request->only('email', 'password'), $remember)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        // Enforce domain khusus untuk RESPONDENT saat login
        if ($user->role === 'respondent') {
            $allowed = collect(config('formapp.respondent_allowed_domains', []))
                ->map(fn($d) => '@' . ltrim(strtolower($d), '@'))
                ->some(fn($suffix) => str_ends_with(strtolower($user->email), $suffix));

            if (! $allowed) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => 'Hanya email @' . implode(', @', config('formapp.respondent_allowed_domains', [])) . ' yang diperbolehkan untuk respondent.',
                ]);
            }
        }

        // Redirect by role
        return redirect()->intended(
            $user->isAdmin() ? route('admin.home') : route('dashboard')
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
