@extends('site.layout')

@section('title', 'DarNa | Dashboard')
@section('meta_description', 'Dashboard de gestion DarNa.')

@section('content')
<div class="page-shell dashboard-shell">
    <div class="container">
        <section class="hero-card dashboard-hero mb-4">
            <div class="dashboard-hero-grid">
                <div>
                    <div class="section-kicker mb-3">Dashboard</div>
                    <h1 class="section-title mb-3">Pilote ton activite <em>depuis une seule vue</em></h1>
                    <p class="soft-copy mb-0">Bonjour {{ auth()->user()->name }}. Voici l etat de la plateforme, des reservations et des revenus cumules.</p>
                </div>
                <div class="dashboard-welcome-card">
                    <div class="tiny-tag mb-2">{{ ucfirst(auth()->user()->role) }}</div>
                    <div class="font-display h2 mb-2">{{ auth()->user()->email }}</div>
                    <div class="soft-copy mb-0">{{ auth()->user()->phone ?: 'Telephone non renseigne' }}</div>
                </div>
            </div>
        </section>

        <section class="dashboard-stats-grid mb-4">
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-label">Logements</div>
                <div class="dashboard-stat-value">{{ $stats['properties'] }}</div>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-label">Reservations</div>
                <div class="dashboard-stat-value">{{ $stats['reservations'] }}</div>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-label">En attente</div>
                <div class="dashboard-stat-value">{{ $stats['pending'] }}</div>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-label">Revenus</div>
                <div class="dashboard-stat-value">{{ number_format($stats['revenue'], 0, ',', ' ') }} MAD</div>
            </article>
        </section>

        <section class="hero-card mb-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <div class="section-kicker mb-2">Comptes</div>
                    <h2 class="font-display h1 mb-2">Gere les administrateurs et les membres</h2>
                    <p class="soft-copy mb-0">Modifie les profils, change les roles admin ou membre, et supprime les comptes que tu ne veux plus garder sur le projet.</p>
                </div>
                <a class="primary-pill" href="{{ route('admin.users.index') }}">Ouvrir la gestion des comptes</a>
            </div>

            <div class="dashboard-stats-grid dashboard-stats-grid-compact mt-4">
                <article class="dashboard-stat-card">
                    <div class="dashboard-stat-label">Comptes total</div>
                    <div class="dashboard-stat-value">{{ $stats['users'] }}</div>
                </article>
                <article class="dashboard-stat-card">
                    <div class="dashboard-stat-label">Admins</div>
                    <div class="dashboard-stat-value">{{ $stats['admins'] }}</div>
                </article>
                <article class="dashboard-stat-card">
                    <div class="dashboard-stat-label">Membres</div>
                    <div class="dashboard-stat-value">{{ $stats['members'] }}</div>
                </article>
            </div>
        </section>

        <section class="hero-card mb-4">
            <form method="GET" action="{{ route('dashboard') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="field-label" for="property_slug">Logement</label>
                    <select class="soft-select" id="property_slug" name="property_slug">
                        <option value="">Tous les logements</option>
                        @foreach ($properties as $property)
                            <option value="{{ $property->slug }}" @selected($filters['property_slug'] === $property->slug)>{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="field-label" for="status">Statut</label>
                    <select class="soft-select" id="status" name="status">
                        <option value="">Tous</option>
                        <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                        <option value="confirmed" @selected($filters['status'] === 'confirmed')>Confirmed</option>
                        <option value="cancelled" @selected($filters['status'] === 'cancelled')>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="primary-pill w-100" type="submit">Filtrer</button>
                </div>
            </form>
        </section>

        <section class="hero-card">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <div class="section-kicker mb-2">Reservations</div>
                    <h2 class="font-display h1 mb-0">Dernieres demandes</h2>
                </div>
                <a class="ghost-pill" href="{{ route('site.home') }}">Voir le site public</a>
            </div>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Logement</th>
                            <th>Client</th>
                            <th>Periode</th>
                            <th>Paiement</th>
                            <th>Voyageurs</th>
                            <th>Statut</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reservations as $reservation)
                            <tr>
                                <td>#{{ $reservation->id }}</td>
                                <td>
                                    <strong>{{ $reservation->property_name }}</strong>
                                    <div class="table-muted">{{ $reservation->property_slug }}</div>
                                </td>
                                <td>
                                    <strong>{{ $reservation->first_name }} {{ $reservation->last_name }}</strong>
                                    <div class="table-muted">{{ $reservation->email }}</div>
                                </td>
                                <td>{{ $reservation->arrival_date->format('d/m/Y') }} - {{ $reservation->departure_date->format('d/m/Y') }}</td>
                                <td>
                                    <strong>{{ $reservation->paymentMethodLabel() }}</strong>
                                    <div class="table-muted">{{ $reservation->paymentStatusLabel() }}</div>
                                    @if ($reservation->payment_method === \App\Models\Reservation::PAYMENT_METHOD_CASH && $reservation->cashMeetingSummary())
                                        <div class="table-muted">{{ $reservation->cashMeetingSummary() }}</div>
                                    @else
                                        <div class="table-muted">{{ $reservation->payment_reference }}</div>
                                    @endif
                                </td>
                                <td>{{ $reservation->guests_count }}</td>
                                <td><span class="tiny-tag">{{ $reservation->status }}</span></td>
                                <td>{{ number_format($reservation->total_amount, 0, ',', ' ') }} MAD</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Aucune reservation pour ce filtre.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
