@extends('site.layout')

@section('title', 'DarNa | Reservation')
@section('meta_description', 'Reservation connectee a l API Laravel.')

@php($paymentConfig = config('payment'))

@section('content')
<div class="page-shell">
    <div class="container">
        <div class="mb-4 d-flex flex-wrap gap-2 align-items-center">
            <a class="ghost-pill" href="{{ route('site.home') }}">Retour a l accueil</a>
            <a class="ghost-pill" href="{{ route('site.property', ['slug' => $slug]) }}">Voir la fiche</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <section class="form-card">
                    <div class="section-kicker mb-3">Reservation</div>
                    <h1 class="section-title mb-3">Envoyer une demande <em>avec paiement clair</em></h1>
                    <p class="soft-copy mb-4" id="reservationLead">Le formulaire enregistre la demande dans MySQL, puis guide le client entre paiement en especes avec rendez-vous ou virement bancaire.</p>

                    @guest
                        <div class="account-prompt-card mb-4">
                            <div class="section-kicker mb-2">Compte client</div>
                            <h2 class="font-display h2 mb-2">Tu n as pas encore de compte ?</h2>
                            <p class="soft-copy mb-3">Ouvre l inscription ou la connexion dans une autre fenetre pour garder cette page ouverte pendant que tu te connectes.</p>
                            <div class="d-flex flex-wrap gap-2">
                                <a
                                    class="primary-pill"
                                    href="{{ route('login') }}"
                                    data-auth-prompt
                                    data-auth-title="Creer ton compte avant ou apres la demande"
                                    data-auth-copy="Tu peux ouvrir l inscription ou la connexion dans une autre fenetre, puis revenir ici sans perdre le formulaire."
                                    data-auth-login-url="{{ route('login') }}"
                                    @if (Route::has('register'))
                                        data-auth-register-url="{{ route('register') }}"
                                    @endif
                                >
                                    Creer un compte ou se connecter
                                </a>
                            </div>
                        </div>
                    @endguest

                    <div class="soft-alert d-none mb-4" id="successBox"></div>
                    <div class="danger-alert d-none mb-4" id="errorBox"></div>
                    <div class="soft-alert d-none mb-4" id="availabilityBox"></div>

                    <form id="reservationForm" class="row g-3">
                        <div class="col-md-6">
                            <label class="field-label" for="arrivalDate">Arrivee</label>
                            <input class="soft-input" id="arrivalDate" type="date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label" for="departureDate">Depart</label>
                            <input class="soft-input" id="departureDate" type="date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label" for="adultsCount">Adultes</label>
                            <input class="soft-input" id="adultsCount" type="number" min="1" value="2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label" for="childrenCount">Enfants</label>
                            <input class="soft-input" id="childrenCount" type="number" min="0" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="field-label" for="firstName">Prenom</label>
                            <input class="soft-input" id="firstName" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label" for="lastName">Nom</label>
                            <input class="soft-input" id="lastName" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label" for="emailAddress">Email</label>
                            <input class="soft-input" id="emailAddress" type="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label" for="phoneNumber">Telephone</label>
                            <input class="soft-input" id="phoneNumber" type="text" required>
                        </div>

                        <div class="col-12">
                            <div class="section-kicker mb-3">Paiement</div>
                            <div class="payment-choice-grid">
                                <label class="payment-choice-card active" data-payment-card="cash">
                                    <input type="radio" name="payment_method" value="cash" checked>
                                    <strong>Especes</strong>
                                    <span>Rendez-vous avant la remise des cles pour regler l acompte.</span>
                                </label>
                                <label class="payment-choice-card" data-payment-card="bank_transfer">
                                    <input type="radio" name="payment_method" value="bank_transfer">
                                    <strong>Virement bancaire</strong>
                                    <span>Le client recoit le compte bancaire et la reference de virement.</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-12" id="cashPaymentPanel">
                            <div class="payment-instruction-card">
                                <div class="section-kicker mb-2">Rendez-vous especes</div>
                                <p class="soft-copy mb-3">{{ $paymentConfig['cash']['meeting_note'] }}</p>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="field-label" for="cashMeetingDate">Date du rendez-vous</label>
                                        <input class="soft-input" id="cashMeetingDate" type="date">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="field-label" for="cashMeetingTime">Heure</label>
                                        <input class="soft-input" id="cashMeetingTime" type="time" value="14:00">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="field-label" for="cashMeetingPlace">Lieu</label>
                                        <input class="soft-input" id="cashMeetingPlace" type="text" value="{{ $paymentConfig['cash']['meeting_location'] }}">
                                    </div>
                                </div>
                                <div class="table-muted mt-3">
                                    Acompte estime a regler avant remise des cles:
                                    <strong id="cashDepositAmount">0 MAD</strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-none" id="bankTransferPanel">
                            <div class="payment-instruction-card bank-transfer-card">
                                <div class="section-kicker mb-2">Virement bancaire</div>
                                <p class="soft-copy mb-3">{{ $paymentConfig['bank_transfer']['note'] }}</p>
                                <div class="payment-bank-grid">
                                    <div>
                                        <div class="field-label">Beneficiaire</div>
                                        <div class="payment-bank-value">{{ $paymentConfig['bank_transfer']['beneficiary_name'] }}</div>
                                    </div>
                                    <div>
                                        <div class="field-label">Banque</div>
                                        <div class="payment-bank-value">{{ $paymentConfig['bank_transfer']['bank_name'] }}</div>
                                    </div>
                                    <div>
                                        <div class="field-label">Compte</div>
                                        <div class="payment-bank-value">{{ $paymentConfig['bank_transfer']['account_number'] }}</div>
                                    </div>
                                    <div>
                                        <div class="field-label">IBAN</div>
                                        <div class="payment-bank-value">{{ $paymentConfig['bank_transfer']['iban'] }}</div>
                                    </div>
                                    <div>
                                        <div class="field-label">SWIFT</div>
                                        <div class="payment-bank-value">{{ $paymentConfig['bank_transfer']['swift'] }}</div>
                                    </div>
                                    <div>
                                        <div class="field-label">Montant d acompte</div>
                                        <div class="payment-bank-value" id="bankTransferAmount">0 MAD</div>
                                    </div>
                                </div>
                                <div class="table-muted mt-3">La reference de virement sera donnee apres validation de la demande.</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="field-label" for="notes">Message</label>
                            <textarea class="soft-textarea" id="notes" rows="5" placeholder="Precisions, heure d'arrivee, besoin special..."></textarea>
                        </div>
                        <div class="col-12">
                            <button class="primary-pill" type="submit" id="submitButton">Envoyer la demande</button>
                        </div>
                    </form>
                </section>
            </div>

            <div class="col-lg-5">
                <div class="sticky-side d-grid gap-4">
                    <aside class="side-card">
                        <div class="summary-photo mb-3" id="reservationPhoto" style="min-height: 15rem;"></div>
                        <div class="tiny-tag mb-2" id="reservationType">Chargement</div>
                        <h2 class="font-display display-6 mb-2" id="reservationName">Chargement...</h2>
                        <p class="soft-copy mb-4" id="reservationLocation">Lieu</p>
                        <div class="d-flex flex-wrap gap-2 mb-4" id="reservationTags"></div>
                        <div class="summary-line d-flex justify-content-between"><span>Prix nuit</span><strong id="summaryNightly">0 MAD</strong></div>
                        <div class="summary-line d-flex justify-content-between"><span>Nuits</span><strong id="summaryNights">1</strong></div>
                        <div class="summary-line d-flex justify-content-between"><span>Frais service</span><strong id="summaryService">0 MAD</strong></div>
                        <div class="summary-line d-flex justify-content-between"><span>Taxes ville</span><strong id="summaryTax">0 MAD</strong></div>
                        <div class="summary-line d-flex justify-content-between fw-bold"><span>Total estime</span><strong id="summaryTotal">0 MAD</strong></div>
                        <div class="summary-line d-flex justify-content-between"><span>Acompte 30%</span><strong id="summaryDeposit">0 MAD</strong></div>
                        <div class="summary-line d-flex justify-content-between"><span>Mode de paiement</span><strong id="summaryPaymentMethod">Especes</strong></div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const slug = @json($slug);
    const paymentConfig = @json($paymentConfig);
    const { apiGet, apiPost, escapeHtml, formatMoney, ratingStars } = window.DarNaSite;

    const arrivalDate = document.getElementById('arrivalDate');
    const departureDate = document.getElementById('departureDate');
    const adultsCount = document.getElementById('adultsCount');
    const childrenCount = document.getElementById('childrenCount');
    const reservationForm = document.getElementById('reservationForm');
    const successBox = document.getElementById('successBox');
    const errorBox = document.getElementById('errorBox');
    const availabilityBox = document.getElementById('availabilityBox');
    const submitButton = document.getElementById('submitButton');
    const cashPaymentPanel = document.getElementById('cashPaymentPanel');
    const bankTransferPanel = document.getElementById('bankTransferPanel');
    const cashMeetingDate = document.getElementById('cashMeetingDate');
    const cashMeetingTime = document.getElementById('cashMeetingTime');
    const cashMeetingPlace = document.getElementById('cashMeetingPlace');
    const paymentMethodInputs = Array.from(document.querySelectorAll('input[name="payment_method"]'));
    const paymentCards = Array.from(document.querySelectorAll('[data-payment-card]'));

    let property = null;
    let currentAvailability = null;

    function selectedPaymentMethod() {
        return paymentMethodInputs.find(input => input.checked)?.value || 'cash';
    }

    function nightsCount() {
        if (!arrivalDate.value || !departureDate.value) return 1;
        const start = new Date(`${arrivalDate.value}T12:00:00`);
        const end = new Date(`${departureDate.value}T12:00:00`);
        return Math.max(1, Math.round((end - start) / 86400000));
    }

    function guestsCount() {
        return Number(adultsCount.value || 0) + Number(childrenCount.value || 0);
    }

    function syncDates() {
        if (!arrivalDate.value) return;

        departureDate.min = arrivalDate.value;
        cashMeetingDate.min = new Date().toISOString().slice(0, 10);
        cashMeetingDate.max = arrivalDate.value;

        if (departureDate.value && departureDate.value <= arrivalDate.value) {
            const nextDay = new Date(`${arrivalDate.value}T12:00:00`);
            nextDay.setDate(nextDay.getDate() + 1);
            departureDate.value = nextDay.toISOString().slice(0, 10);
        }

        if (!cashMeetingDate.value || cashMeetingDate.value > arrivalDate.value) {
            cashMeetingDate.value = arrivalDate.value;
        }
    }

    function updatePaymentPanels() {
        const method = selectedPaymentMethod();

        cashPaymentPanel.classList.toggle('d-none', method !== 'cash');
        bankTransferPanel.classList.toggle('d-none', method !== 'bank_transfer');
        document.getElementById('summaryPaymentMethod').textContent = method === 'cash' ? 'Especes' : 'Virement bancaire';

        paymentCards.forEach(card => {
            card.classList.toggle('active', card.dataset.paymentCard === method);
        });
    }

    function updateSummary() {
        if (!property) return;

        const nights = nightsCount();
        const guests = guestsCount();
        const subtotal = Number(property.nightly_rate || 0) * nights;
        const service = Math.round(subtotal * 0.12);
        const cityTax = nights * guests * 25;
        const total = subtotal + service + cityTax;
        const deposit = Math.round(total * 0.3);

        document.getElementById('summaryNightly').textContent = formatMoney(property.nightly_rate);
        document.getElementById('summaryNights').textContent = String(nights);
        document.getElementById('summaryService').textContent = formatMoney(service);
        document.getElementById('summaryTax').textContent = formatMoney(cityTax);
        document.getElementById('summaryTotal').textContent = formatMoney(total);
        document.getElementById('summaryDeposit').textContent = formatMoney(deposit);
        document.getElementById('cashDepositAmount').textContent = formatMoney(deposit);
        document.getElementById('bankTransferAmount').textContent = formatMoney(deposit);
    }

    function hideAvailability() {
        availabilityBox.classList.add('d-none');
        availabilityBox.textContent = '';
    }

    async function checkAvailability() {
        if (!property || !arrivalDate.value || !departureDate.value) {
            currentAvailability = null;
            hideAvailability();
            return;
        }

        try {
            const payload = await apiGet(`/properties/${property.slug}/availability`, {
                arrival_date: arrivalDate.value,
                departure_date: departureDate.value
            });

            currentAvailability = payload.data || null;
            availabilityBox.classList.remove('d-none');

            if (currentAvailability?.available) {
                availabilityBox.textContent = 'Ces dates sont actuellement disponibles. La demande peut etre envoyee.';
                return;
            }

            const firstBlockedRange = currentAvailability?.blocked_ranges?.[0];
            availabilityBox.textContent = firstBlockedRange
                ? `Cette periode chevauche deja une reservation du ${firstBlockedRange.arrival_date} au ${firstBlockedRange.departure_date}.`
                : 'Cette periode n est pas disponible.';
        } catch (error) {
            currentAvailability = null;
            hideAvailability();
        }
    }

    function setDefaultDates() {
        const today = new Date();
        const arrival = new Date(today);
        arrival.setDate(arrival.getDate() + 7);
        const departure = new Date(arrival);
        departure.setDate(departure.getDate() + 3);

        arrivalDate.min = today.toISOString().slice(0, 10);
        arrivalDate.value = arrival.toISOString().slice(0, 10);
        departureDate.value = departure.toISOString().slice(0, 10);
        syncDates();
        cashMeetingTime.value = '14:00';
        cashMeetingPlace.value = paymentConfig.cash.meeting_location || '';
    }

    function setPaymentMethod(method) {
        paymentMethodInputs.forEach(input => {
            input.checked = input.value === method;
        });

        updatePaymentPanels();
    }

    function bankTransferLines(payment) {
        const bank = payment?.bank_transfer || paymentConfig.bank_transfer || {};

        return [
            `Beneficiaire: <strong>${escapeHtml(bank.beneficiary_name || '')}</strong>`,
            `Banque: <strong>${escapeHtml(bank.bank_name || '')}</strong>`,
            `Compte: <strong>${escapeHtml(bank.account_number || '')}</strong>`,
            `IBAN: <strong>${escapeHtml(bank.iban || '')}</strong>`,
            `SWIFT: <strong>${escapeHtml(bank.swift || '')}</strong>`,
            `Reference de virement: <strong>${escapeHtml(payment?.reference || '')}</strong>`
        ].join('<br>');
    }

    function successMessage(responseData) {
        const payment = responseData.payment || {};
        const amounts = responseData.amounts || {};
        const base = [
            '<strong>Reservation enregistree</strong>',
            `Reference #${escapeHtml(responseData.id)} - statut ${escapeHtml(responseData.status)}.`,
            `Total confirme: <strong>${escapeHtml(formatMoney(amounts.total_amount || 0))}</strong>.`,
            `Acompte a regler: <strong>${escapeHtml(formatMoney(amounts.deposit_amount || 0))}</strong>.`
        ];

        if (payment.method === 'cash') {
            base.push(`Rendez-vous especes prevu le <strong>${escapeHtml(payment.cash_meeting?.date || '')}</strong> a <strong>${escapeHtml(payment.cash_meeting?.time || '')}</strong>.`);
            base.push(`Lieu: <strong>${escapeHtml(payment.cash_meeting?.place || '')}</strong>.`);
            base.push('Le paiement en especes doit etre fait avant la remise des cles.');
            return base.join('<br>');
        }

        base.push('Le client doit maintenant effectuer le virement bancaire de l acompte.');
        base.push(bankTransferLines(payment));
        return base.join('<br>');
    }

    try {
        const payload = await apiGet(`/properties/${slug}`);
        property = payload.data;

        document.title = `${property.name} | Reservation DarNa`;
        document.getElementById('reservationType').textContent = property.type;
        document.getElementById('reservationName').textContent = property.name;
        document.getElementById('reservationLocation').textContent = property.location || property.city || '';
        document.getElementById('reservationLead').textContent = property.summary || document.getElementById('reservationLead').textContent;
        document.getElementById('reservationPhoto').style.backgroundImage =
            `linear-gradient(180deg, rgba(14,10,8,0.15), rgba(14,10,8,0.45)), url('${property.thumbnail_image || property.photos?.[0]?.image || ''}')`;
        document.getElementById('reservationTags').innerHTML = [
            `${ratingStars(property.rating)} ${property.rating}`,
            property.reviews_label,
            `${property.max_guests} voyageurs`
        ].map(item => `<span class="tiny-tag">${escapeHtml(item)}</span>`).join('');

        setDefaultDates();
        setPaymentMethod('cash');
        updateSummary();
        await checkAvailability();
    } catch (error) {
        errorBox.classList.remove('d-none');
        errorBox.textContent = 'Impossible de charger ce logement pour la reservation.';
    }

    [arrivalDate, departureDate, adultsCount, childrenCount].forEach(input => {
        input.addEventListener('change', () => {
            syncDates();
            updateSummary();
            checkAvailability();
        });
    });

    paymentMethodInputs.forEach(input => {
        input.addEventListener('change', () => {
            updatePaymentPanels();
            updateSummary();
        });
    });

    reservationForm.addEventListener('submit', async event => {
        event.preventDefault();
        errorBox.classList.add('d-none');
        successBox.classList.add('d-none');

        if (!property) return;

        submitButton.disabled = true;
        submitButton.textContent = 'Envoi...';

        try {
            await checkAvailability();

            if (currentAvailability && currentAvailability.available === false) {
                throw new Error('Ces dates ne sont plus disponibles. Choisis une autre periode.');
            }

            const paymentMethod = selectedPaymentMethod();
            const payload = {
                property_slug: property.slug,
                arrival_date: arrivalDate.value,
                departure_date: departureDate.value,
                adults_count: Number(adultsCount.value || 0),
                children_count: Number(childrenCount.value || 0),
                first_name: document.getElementById('firstName').value,
                last_name: document.getElementById('lastName').value,
                email: document.getElementById('emailAddress').value,
                phone: document.getElementById('phoneNumber').value,
                notes: document.getElementById('notes').value,
                payment_method: paymentMethod
            };

            if (paymentMethod === 'cash') {
                payload.cash_meeting_date = cashMeetingDate.value;
                payload.cash_meeting_time = cashMeetingTime.value;
                payload.cash_meeting_place = cashMeetingPlace.value;
            }

            const response = await apiPost('/reservations', payload);

            successBox.classList.remove('d-none');
            successBox.innerHTML = successMessage(response.data);
            reservationForm.reset();
            adultsCount.value = 2;
            childrenCount.value = 0;
            setDefaultDates();
            setPaymentMethod('cash');
            updateSummary();
            await checkAvailability();
        } catch (error) {
            errorBox.classList.remove('d-none');
            errorBox.textContent = error.message || 'La reservation a echoue.';
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Envoyer la demande';
        }
    });
});
</script>
@endpush
