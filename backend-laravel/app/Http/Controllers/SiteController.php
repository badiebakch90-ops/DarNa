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


namespace App\Http\Controllers;

class SiteController extends Controller
{
    public function home()
    {
        return view('site.home');
    }

    public function property(string $slug)
    {
        return view('site.property', [
            'slug' => $slug,
        ]);
    }

    public function reservation(string $slug)
    {
        return view('site.reservation', [
            'slug' => $slug,
        ]);
    }

    public function reservations()
    {
        return redirect()->route('dashboard');
    }
}
