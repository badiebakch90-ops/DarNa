@extends('site.layout')

@section('title', 'DarNa | Modifier un compte')
@section('meta_description', 'Modifier ou supprimer un compte utilisateur DarNa.')

@section('content')
<div class="page-shell dashboard-shell">
    <div class="container">
        <section class="hero-card dashboard-hero mb-4">
            <div class="dashboard-hero-grid">
                <div>
                    <div class="section-kicker mb-3">Edition</div>
                    <h1 class="section-title mb-3">Pilote un <em>compte utilisateur</em></h1>
                    <p class="soft-copy mb-0">Mets a jour les informations du compte, change son role, ou supprime-le si tu n en as plus besoin.</p>
                </div>
                <div class="dashboard-welcome-card">
                    <div class="tiny-tag mb-2">{{ ucfirst($managedUser->role) }}</div>
                    <div class="font-display h2 mb-2">{{ $managedUser->name }}</div>
                    <div class="soft-copy mb-1">{{ $managedUser->email }}</div>
                    <div class="soft-copy mb-0">{{ $managedUser->phone ?: 'Telephone non renseigne' }}</div>
                </div>
            </div>
        </section>

        <section class="hero-card mb-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                <div>
                    <div class="section-kicker mb-2">Modification</div>
                    <h2 class="font-display h1 mb-0">Parametres du compte</h2>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="ghost-pill" href="{{ route('admin.users.index') }}">Retour aux comptes</a>
                    <a class="ghost-pill" href="{{ route('dashboard') }}">Dashboard</a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="row g-4">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label class="field-label" for="name">Nom complet</label>
                    <input class="soft-input" id="name" type="text" name="name" value="{{ old('name', $managedUser->name) }}" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="field-label" for="email">Email</label>
                    <input class="soft-input" id="email" type="email" name="email" value="{{ old('email', $managedUser->email) }}" required>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="field-label" for="phone">Telephone</label>
                    <input class="soft-input" id="phone" type="text" name="phone" value="{{ old('phone', $managedUser->phone) }}" placeholder="+212 6 00 00 00 00">
                    @error('phone')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="field-label" for="role">Role</label>
                    @if ($isCurrentUser)
                        <input type="hidden" name="role" value="{{ old('role', $managedUser->role) }}">
                        <input class="soft-input" id="role" type="text" value="{{ ucfirst(old('role', $managedUser->role)) }}" disabled>
                        <div class="table-muted mt-2">Le role du compte connecte reste protege pour eviter de perdre l acces admin.</div>
                    @else
                        <select class="soft-select" id="role" name="role">
                            <option value="admin" @selected(old('role', $managedUser->role) === 'admin')>Admin</option>
                            <option value="member" @selected(old('role', $managedUser->role) === 'member')>Membre</option>
                        </select>
                    @endif
                    @error('role')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="field-label" for="password">Nouveau mot de passe</label>
                    <input class="soft-input" id="password" type="password" name="password" autocomplete="new-password">
                    <div class="table-muted mt-2">Laisse vide pour garder le mot de passe actuel.</div>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="field-label" for="password_confirmation">Confirmation du mot de passe</label>
                    <input class="soft-input" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
                </div>

                <div class="col-12 d-flex flex-wrap gap-2">
                    <button class="primary-pill" type="submit">Enregistrer les changements</button>
                    <a class="ghost-pill" href="{{ route('admin.users.index') }}">Annuler</a>
                </div>
            </form>
        </section>

        <section class="hero-card">
            <div class="section-kicker mb-2">Suppression</div>
            <h2 class="font-display h1 mb-3">Zone sensible</h2>
            @if ($isCurrentUser)
                <div class="danger-alert">
                    Le compte connecte ne peut pas etre supprime depuis cette interface. Modifie un autre administrateur si tu veux lui transferer la gestion.
                </div>
            @else
                <p class="soft-copy mb-4">Cette action retire definitivement le compte du projet. Utilise-la seulement si tu es certain de ne plus en avoir besoin.</p>
                <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" onsubmit="return confirm('Supprimer definitivement ce compte ?');">
                    @csrf
                    @method('DELETE')
                    <button class="danger-pill" type="submit">Supprimer ce compte</button>
                </form>
            @endif
        </section>
    </div>
</div>
@endsection
