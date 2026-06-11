<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        app()->setLocale(config('chomin.locales.default', 'th'));

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $redirect = $this->safeRedirectPath($request->input('redirect'));

        return redirect()->intended($redirect ?? route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/'.config('chomin.locales.default', 'th'));
    }

    private function safeRedirectPath(?string $redirect): ?string
    {
        if (! $redirect) {
            return null;
        }

        $path = parse_url($redirect, PHP_URL_PATH);
        if (! is_string($path) || ! str_starts_with($path, '/')) {
            return null;
        }

        $query = parse_url($redirect, PHP_URL_QUERY);

        return $path.($query ? '?'.$query : '');
    }
}
