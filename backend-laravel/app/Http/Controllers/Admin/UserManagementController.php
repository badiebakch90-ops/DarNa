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


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateManagedUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->string('search')->toString());
        $role = $request->string('role')->toString();

        $users = User::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($role, fn ($query) => $query->where('role', $role))
            ->orderByRaw('CASE WHEN role = ? THEN 0 ELSE 1 END', [User::ROLE_ADMIN])
            ->orderBy('name')
            ->limit(50)
            ->get();

        return view('dashboard.users.index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'role' => $role,
            ],
            'stats' => [
                'users' => User::query()->count(),
                'admins' => User::query()->where('role', User::ROLE_ADMIN)->count(),
                'members' => User::query()->where('role', User::ROLE_MEMBER)->count(),
            ],
        ]);
    }

    public function edit(Request $request, User $user)
    {
        return view('dashboard.users.edit', [
            'managedUser' => $user,
            'isCurrentUser' => $request->user()?->is($user) ?? false,
        ]);
    }

    public function update(UpdateManagedUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        $actingUser = $request->user();
        $nextRole = $validated['role'];

        if ($actingUser?->is($user) && $nextRole !== User::ROLE_ADMIN) {
            return back()
                ->withInput()
                ->withErrors([
                    'role' => 'Tu ne peux pas retirer tes propres droits administrateur depuis ce compte.',
                ]);
        }

        if (
            $user->isAdmin()
            && $nextRole !== User::ROLE_ADMIN
            && User::query()->where('role', User::ROLE_ADMIN)->count() <= 1
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'role' => 'Au moins un administrateur doit rester actif sur le projet.',
                ]);
        }

        $user->fill(Arr::only($validated, ['name', 'email', 'phone', 'role']));

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'Le compte a ete mis a jour avec succes.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->is($user)) {
            return redirect()
                ->route('admin.users.edit', $user)
                ->with('danger', 'Tu ne peux pas supprimer le compte actuellement connecte.');
        }

        if ($user->isAdmin() && User::query()->where('role', User::ROLE_ADMIN)->count() <= 1) {
            return redirect()
                ->route('admin.users.index')
                ->with('danger', 'Au moins un administrateur doit rester actif sur le projet.');
        }

        $deletedUserName = $user->name;

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Le compte de {$deletedUserName} a ete supprime.");
    }
}
