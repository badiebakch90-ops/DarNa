@extends('site.layout')

@section('title', 'DarNa | Espace hote')
@section('meta_description', 'Publie tes logements et recois les notifications de reservation.')

@section('content')
<div class="page-shell dashboard-shell">
    <div class="container">
        <section class="hero-card dashboard-hero mb-4">
            <div class="dashboard-hero-grid">
                <div>
                    <div class="section-kicker mb-3">Espace hote</div>
                    <h1 class="section-title mb-3">Pilote tes annonces <em>comme un hote moderne</em></h1>
                    <p class="soft-copy mb-0">Ajoute un bien, suis les reservations recues et garde un oeil sur les notifications importantes depuis une seule page.</p>
                </div>
                <div class="dashboard-welcome-card">
                    <div class="tiny-tag mb-2">Compte connecte</div>
                    <div class="font-display h2 mb-2">{{ auth()->user()->name }}</div>
                    <div class="soft-copy mb-1">{{ auth()->user()->email }}</div>
                    <div class="soft-copy mb-0">{{ auth()->user()->phone ?: 'Telephone non renseigne' }}</div>
                </div>
            </div>
        </section>

        <section class="dashboard-stats-grid dashboard-stats-grid-compact mb-4">
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-label">Mes logements</div>
                <div class="dashboard-stat-value">{{ $stats['properties'] }}</div>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-label">Demandes en attente</div>
                <div class="dashboard-stat-value">{{ $stats['pending'] }}</div>
            </article>
            <article class="dashboard-stat-card">
                <div class="dashboard-stat-label">Notifications nouvelles</div>
                <div class="dashboard-stat-value">{{ $stats['unread_notifications'] }}</div>
            </article>
        </section>

        <section class="hero-card mb-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <div class="section-kicker mb-2">Publication</div>
                    <h2 class="font-display h1 mb-2">Ajoute un nouveau logement</h2>
                    <p class="soft-copy mb-0">Publie une maison, une villa, un appartement, un riad ou un camp avec sa carte, ses images et sa description.</p>
                </div>
                <a class="primary-pill" href="{{ route('hosting.create') }}">Ajouter un bien</a>
            </div>
        </section>

        <div class="row g-4 mb-4">
            <div class="col-lg-5">
                <section class="hero-card h-100">
                    <div class="section-kicker mb-3">Notifications</div>
                    <h2 class="font-display h1 mb-4">Ce qui vient d arriver</h2>
                    <div class="notification-list">
                        @forelse ($notifications as $notification)
                            @php($data = $notification->data)
                            <article class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                    <div class="tiny-tag">{{ is_null($notification->read_at) ? 'Nouvelle' : 'Archivee' }}</div>
                                    <div class="table-muted">{{ $notification->created_at?->diffForHumans() }}</div>
                                </div>
                                <h3 class="font-display h3 mb-2">{{ $data['title'] ?? 'Notification' }}</h3>
                                <p class="soft-copy mb-3">{{ $data['message'] ?? 'Une nouvelle activite est disponible.' }}</p>
                                <div class="table-muted mb-1">{{ $data['property_name'] ?? 'Logement' }}</div>
                                <div class="table-muted mb-3">{{ $data['arrival_date'] ?? '-' }} - {{ $data['departure_date'] ?? '-' }}</div>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="tiny-tag">{{ $data['payment_method_label'] ?? 'Paiement' }}</span>
                                    <span class="tiny-tag">{{ $data['payment_status_label'] ?? 'En attente' }}</span>
                                </div>
                                @if (! empty($data['cash_meeting_label']))
                                    <div class="table-muted mb-3">{{ $data['cash_meeting_label'] }}</div>
                                @elseif (! empty($data['payment_reference']))
                                    <div class="table-muted mb-3">Reference {{ $data['payment_reference'] }}</div>
                                @endif
                                <a class="ghost-pill" href="{{ $data['action_url'] ?? route('hosting.index') }}">Voir mon espace</a>
                            </article>
                        @empty
                            <div class="empty-card p-4 rounded-4">
                                <div class="soft-copy mb-0">Aucune notification pour le moment. Les nouvelles reservations apparaitront ici.</div>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="col-lg-7">
                <section class="hero-card h-100">
                    <div class="section-kicker mb-2">Mes annonces</div>
                    <h2 class="font-display h1 mb-4">Logements publies</h2>

                    <div class="host-property-grid">
                        @forelse ($properties as $property)
                            <article class="host-property-card">
                                <div
                                    class="host-property-media"
                                    style="background-image: linear-gradient(180deg, rgba(14,10,8,0.15), rgba(14,10,8,0.45)), url('{{ e($property->thumbnail_image ?: 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80') }}')"
                                ></div>
                                <div class="host-property-body">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                        <div>
                                            <div class="soft-copy small mb-1">{{ $property->location }}</div>
                                            <h3 class="font-display h2 mb-0">{{ $property->name }}</h3>
                                        </div>
                                        <span class="tiny-tag">{{ $property->type }}</span>
                                    </div>
                                    <p class="soft-copy mb-3">{{ $property->summary }}</p>
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="data-pill">{{ number_format($property->nightly_rate, 0, ',', ' ') }} MAD / nuit</span>
                                        <span class="data-pill">{{ $property->max_guests }} voyageurs</span>
                                        <span class="data-pill">{{ $property->pending_reservations_count }} en attente</span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a class="primary-pill" href="{{ route('site.property', $property->slug) }}">Voir l annonce</a>
                                        <a class="ghost-pill" href="{{ route('site.reservation', $property->slug) }}">Voir la reservation</a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="empty-card p-4 rounded-4">
                                <div class="soft-copy mb-3">Tu n as pas encore publie de logement.</div>
                                <a class="primary-pill" href="{{ route('hosting.create') }}">Ajouter mon premier bien</a>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>

        <section class="hero-card">
            <div class="section-kicker mb-2">Reservations</div>
            <h2 class="font-display h1 mb-4">Dernieres demandes recues</h2>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Logement</th>
                            <th>Client</th>
                            <th>Periode</th>
                            <th>Paiement</th>
                            <th>Statut</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestReservations as $reservation)
                            <tr>
                                <td>#{{ $reservation->id }}</td>
                                <td>{{ $reservation->property_name }}</td>
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
                                <td><span class="tiny-tag">{{ $reservation->status }}</span></td>
                                <td>{{ number_format($reservation->total_amount, 0, ',', ' ') }} MAD</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Aucune reservation recue pour l instant.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
