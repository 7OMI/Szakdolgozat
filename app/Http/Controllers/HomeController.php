<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        if (!is_null(auth()->user())) {
            return redirect()->route('interface');
        }

        return view('page.home', ['data'=>["mode"=>"login", "page"=>"index"]]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authenticate($request);
        $request->session()->regenerate();
        return redirect()->route('interface');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    private function authenticate($request): void
    {
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
    }

    private function throttleKey($request): string
    {
        return Str::transliterate(Str::lower($request->email).'|'.$request->ip());
    }
}
