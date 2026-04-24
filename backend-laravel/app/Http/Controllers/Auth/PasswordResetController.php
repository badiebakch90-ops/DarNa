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
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function createForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function storeForgotPassword(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink(
            $request->safe()->only('email')
        );

        if (! in_array($status, [Password::RESET_LINK_SENT, Password::INVALID_USER], true)) {
            return back()
                ->withInput()
                ->withErrors(['email' => __($status)]);
        }

        return back()->with('status', 'Si un compte correspond a cet email, un lien de reinitialisation vient d etre envoye.');
    }

    public function createResetPassword(string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request('email', ''),
        ]);
    }

    public function storeResetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->safe()->only(['email', 'password', 'password_confirmation', 'token']),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => $request->string('password')->toString(),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => __($status)]);
        }

        return redirect()->route('login')
            ->with('status', 'Mot de passe mis a jour. Tu peux maintenant te connecter.');
    }
}
