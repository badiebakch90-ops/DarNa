<?php

/**
 * ─────────────────────────────────────────────────────────────
 *   DarNa — Plateforme de location authentique au Maroc 🇲🇦
 *   Author    : Abdelbadie Abkhich (@badiebakch90-ops)
 *   Original  : https://github.com/badiebakch90-ops/DarNa
 *   Copyright : © 2026 Abdelbadie Abkhich — All rights reserved
 *   License   : See LICENSE file
 * ─────────────────────────────────────────────────────────────
 */


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function createLogin()
    {
        return view('auth.login');
    }

    public function storeLogin(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->safe()->only(['email', 'password']);
        $remember = (bool) $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => 'Les identifiants fournis sont incorrects.',
                ]);
        }

        $request->session()->regenerate();

        if ($request->user()?->isAdmin()) {
            return redirect()->intended(route('dashboard'))
                ->with('status', 'Connexion administrateur reussie.');
        }

        return redirect()->route('site.home')
            ->with('status', 'Connexion reussie. Bienvenue sur DarNa.');
    }

    public function createRegister()
    {
        return view('auth.register');
    }

    public function storeRegister(RegisterRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'phone' => $request->string('phone')->toString() ?: null,
            'password' => $request->string('password')->toString(),
            'role' => User::ROLE_MEMBER,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('site.home')
            ->with('status', 'Compte cree avec succes. Tu peux maintenant envoyer des demandes et te connecter.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('site.home')
            ->with('status', 'Tu as ete deconnecte avec succes.');
    }
}
