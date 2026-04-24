@extends('site.layout')

@section('title', 'DarNa | Gestion des comptes')
@section('meta_description', 'Gestion des comptes administrateurs et membres DarNa.')

@section('content')
<div class="page-shell dashboard-shell">
    <div class="container">
        <section class="hero-card dashboard-hero mb-4">
            <div class="dashboard-hero-grid">
                <div>
                    <div class="section-kicker mb-3">Backoffice</div>
                    <h1 class="section-title mb-3">Gestion des <em>comptes utilisateurs</em></h1>
                    <p class="soft-copy mb-0">Cette page te permet de modifier les informations, promouvoir un compte en admin ou supprimer un utilisateur devenu inutile.</p>
                </div>
                <div class="dashboard-welcome-card">
                    <div class="tiny-tag mb-2">Admin actif</div>
                    <div class="font-display h2 mb-2">{{ auth()->user()->name }}</div>
                    <div class="soft-copy mb-1">{{ auth()->user()->email }}</div>
                    <div class="soft-copy mb-0">{{ auth()->user()->phone ?: 'Telephone non renseigne' }}</div>
                </div>
            </div>
        </section>

        <section class="dashboard-stats-grid dashboard-stats-grid-compact mb-4">
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-label">Comptes total</div>
                <div class="dashboard-stat-value">{{ $stats['users'] }}</div>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-label">Administrateurs</div>
                <div class="dashboard-stat-value">{{ $stats['admins'] }}</div>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-label">Membres</div>
                <div class="dashboard-stat-value">{{ $stats['members'] }}</div>
            </article>
        </section>

        <section class="hero-card mb-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-lg-6">
                    <label class="field-label" for="search">Recherche</label>
                    <input
                        class="soft-input"
                        id="search"
                        type="text"
                        name="search"
                        value="{{ $filters['search'] }}"
                        placeholder="Nom, email ou telephone"
                    >
                </div>
                <div class="col-lg-3">
                    <label class="field-label" for="role">Role</label>
                    <select class="soft-select" id="role" name="role">
                        <option value="">Tous les roles</option>
                        <option value="admin" @selected($filters['role'] === 'admin')>Admin</option>
                        <option value="member" @selected($filters['role'] === 'member')>Membre</option>
                    </select>
                </div>
                <div class="col-lg-3 d-flex align-items-end gap-2">
                    <button class="primary-pill flex-grow-1" type="submit">Filtrer</button>
                    <a class="ghost-pill" href="{{ route('admin.users.index') }}">Reset</a>
                </div>
            </form>
        </section>

        <section class="hero-card">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                <div>
                    <div class="section-kicker mb-2">Utilisateurs</div>
                    <h2 class="font-display h1 mb-0">Comptes du projet</h2>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="ghost-pill" href="{{ route('dashboard') }}">Retour dashboard</a>
                    <a class="ghost-pill" href="{{ route('site.home') }}">Voir le site public</a>
                </div>
            </div>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th>Cree le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $managedUser)
                            <tr>
                                <td>
                                    <strong>{{ $managedUser->name }}</strong>
                                    @if (auth()->id() === $managedUser->id)
                                        <div class="table-muted">Compte connecte</div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $managedUser->email }}</div>
                                    <div class="table-muted">{{ $managedUser->phone ?: 'Telephone non renseigne' }}</div>
                                </td>
                                <td>
                                    <span class="tiny-tag">{{ ucfirst($managedUser->role) }}</span>
                                </td>
                                <td>{{ $managedUser->created_at?->format('d/m/Y') ?: '-' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a class="ghost-pill" href="{{ route('admin.users.edit', $managedUser) }}">Modifier</a>
                                        @if (auth()->id() !== $managedUser->id)
                                            <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" onsubmit="return confirm('Supprimer ce compte ? Cette action est definitive.');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="danger-pill" type="submit">Supprimer</button>
                                            </form>
                                        @else
                                            <span class="tiny-tag">Protection active</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Aucun compte ne correspond a ce filtre.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
