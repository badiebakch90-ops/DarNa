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

use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $propertySlug = $request->string('property_slug')->toString();
        $status = $request->string('status')->toString();

        $reservations = Reservation::query()
            ->latest()
            ->when($propertySlug, fn ($query) => $query->where('property_slug', $propertySlug))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->limit(20)
            ->get();

        return view('dashboard.index', [
            'properties' => Property::query()->orderBy('name')->get(['id', 'slug', 'name']),
            'reservations' => $reservations,
            'stats' => [
                'properties' => Property::query()->count(),
                'reservations' => Reservation::query()->count(),
                'pending' => Reservation::query()->where('status', 'pending')->count(),
                'revenue' => Reservation::query()->sum('total_amount'),
                'users' => User::query()->count(),
                'admins' => User::query()->where('role', User::ROLE_ADMIN)->count(),
                'members' => User::query()->where('role', User::ROLE_MEMBER)->count(),
            ],
            'filters' => [
                'property_slug' => $propertySlug,
                'status' => $status,
            ],
        ]);
    }
}
