@extends('site.layout')

@section('title', 'DarNa | Backoffice reservations')
@section('meta_description', 'Vue simple pour suivre les reservations DarNa.')

@section('content')
<div class="page-shell">
    <div class="container">
        <div class="mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="section-kicker mb-2">Backoffice</div>
                <h1 class="section-title mb-2">Reservations enregistrees</h1>
                <p class="soft-copy mb-0">Vue simple pour controler les demandes, verifier les periodes et suivre les montants.</p>
            </div>
            <a class="ghost-pill" href="{{ route('site.home') }}">Retour a l accueil</a>
        </div>

        <section class="hero-card mb-4">
            <form class="row g-3" id="filtersForm">
                <div class="col-md-4">
                    <label class="field-label" for="filterProperty">Slug logement</label>
                    <input class="soft-input" id="filterProperty" type="text" placeholder="riad-al-baraka">
                </div>
                <div class="col-md-3">
                    <label class="field-label" for="filterStatus">Statut</label>
                    <select class="soft-select" id="filterStatus">
                        <option value="">Tous</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="field-label" for="filterLimit">Limite</label>
                    <select class="soft-select" id="filterLimit">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="primary-pill w-100" type="submit">Actualiser</button>
                </div>
            </form>
        </section>

        <div class="soft-alert d-none mb-4" id="reservationsInfoBox"></div>
        <div class="danger-alert d-none mb-4" id="reservationsErrorBox"></div>

        <section class="hero-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Logement</th>
                            <th>Client</th>
                            <th>Periode</th>
                            <th>Voyageurs</th>
                            <th>Statut</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="reservationsTableBody">
                        <tr>
                            <td colspan="7" class="text-center py-4">Chargement des reservations...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const { apiGet, escapeHtml, formatMoney } = window.DarNaSite;
    const form = document.getElementById('filtersForm');
    const tableBody = document.getElementById('reservationsTableBody');
    const infoBox = document.getElementById('reservationsInfoBox');
    const errorBox = document.getElementById('reservationsErrorBox');
    const filterProperty = document.getElementById('filterProperty');
    const filterStatus = document.getElementById('filterStatus');
    const filterLimit = document.getElementById('filterLimit');

    function formatDate(value) {
        if (!value) return '-';
        return new Intl.DateTimeFormat('fr-FR', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }).format(new Date(`${value}T12:00:00`));
    }

    function renderRows(items) {
        if (!items.length) {
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Aucune reservation pour ces filtres.</td></tr>';
            return;
        }

        tableBody.innerHTML = items.map(item => `
            <tr>
                <td>#${escapeHtml(String(item.id))}</td>
                <td>
                    <div class="fw-semibold">${escapeHtml(item.property?.name || '')}</div>
                    <div class="small text-muted">${escapeHtml(item.property?.slug || '')}</div>
                </td>
                <td>
                    <div class="fw-semibold">${escapeHtml(`${item.first_name || ''} ${item.last_name || ''}`.trim())}</div>
                    <div class="small text-muted">${escapeHtml(item.email || '')}</div>
                </td>
                <td>${escapeHtml(formatDate(item.arrival_date))} - ${escapeHtml(formatDate(item.departure_date))}</td>
                <td>${escapeHtml(String(item.guests_count || 0))}</td>
                <td><span class="tiny-tag">${escapeHtml(item.status || '')}</span></td>
                <td class="fw-semibold">${escapeHtml(formatMoney(item.amounts?.total_amount || 0))}</td>
            </tr>
        `).join('');
    }

    async function loadReservations() {
        infoBox.classList.add('d-none');
        errorBox.classList.add('d-none');
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Chargement des reservations...</td></tr>';

        try {
            const payload = await apiGet('/reservations', {
                property_slug: filterProperty.value.trim(),
                status: filterStatus.value,
                limit: filterLimit.value
            });

            const items = Array.isArray(payload.data) ? payload.data : [];
            renderRows(items);
            infoBox.textContent = `${items.length} reservation(s) chargee(s).`;
            infoBox.classList.remove('d-none');
        } catch (error) {
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Impossible de charger les reservations.</td></tr>';
            errorBox.textContent = error.message || 'Le backoffice n a pas pu charger les reservations.';
            errorBox.classList.remove('d-none');
        }
    }

    form.addEventListener('submit', event => {
        event.preventDefault();
        loadReservations();
    });

    await loadReservations();
});
</script>
@endpush
