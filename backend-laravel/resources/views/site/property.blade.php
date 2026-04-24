@extends('site.layout')

@section('title', 'DarNa | Fiche logement')
@section('meta_description', 'Fiche detaillee d un logement DarNa.')

@section('content')
<div class="page-shell">
    <div class="container">
        <div class="mb-4 d-flex flex-wrap gap-2 align-items-center">
            <a class="ghost-pill" href="{{ route('site.home') }}">Retour a l accueil</a>
            <span class="tiny-tag" id="propertyTypeChip">Chargement</span>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <section class="hero-card mb-4">
                    <div class="section-kicker mb-3" id="propertyEyebrow">Fiche logement</div>
                    <h1 class="section-title mb-3" id="propertyName">Chargement...</h1>
                    <div class="d-flex flex-wrap gap-2 mb-3" id="propertyMeta"></div>
                    <p class="soft-copy mb-4" id="propertySummary">Nous recuperons la description du logement depuis l API Laravel.</p>
                    <div class="gallery-main mb-3" id="galleryMain" style="min-height: 24rem;"></div>
                    <div class="row g-3" id="galleryThumbs"></div>
                </section>

                <section class="hero-card mb-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="section-kicker mb-3">Description</div>
                            <p class="soft-copy" id="propertyDescription"></p>
                        </div>
                        <div class="col-md-6">
                            <div class="section-kicker mb-3">Histoire du lieu</div>
                            <p class="soft-copy" id="propertyStory"></p>
                        </div>
                    </div>
                </section>

                <section class="hero-card mb-4">
                    <div class="section-kicker mb-3">Equipements</div>
                    <div class="d-flex flex-wrap gap-2" id="amenitiesList"></div>
                </section>

                <section class="hero-card">
                    <div class="section-kicker mb-3">Autour du logement</div>
                    <div id="localAreaList" class="d-grid gap-3"></div>
                </section>
            </div>

            <div class="col-lg-4">
                <div class="sticky-side d-grid gap-4">
                    <aside class="side-card">
                        <div class="soft-copy small mb-2">A partir de</div>
                        <div class="price-line mb-3" id="propertyPrice">0 MAD <span>/ nuit</span></div>
                        <div class="d-grid gap-3" id="factsList"></div>
                        <div class="d-grid gap-2 mt-4">
                            <a class="primary-pill" href="#" id="reserveLink">Reserver ce sejour</a>
                            <a class="ghost-pill" href="#" id="mapLink">Ouvrir la carte</a>
                        </div>
                    </aside>

                    <aside class="side-card">
                        <div class="section-kicker mb-3">Localisation</div>
                        <p class="soft-copy mb-3" id="mapCaption">La carte apparaitra ici.</p>
                        <iframe class="map-frame" id="propertyMap" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="propertyMapModal" tabindex="-1" aria-labelledby="propertyMapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-panel-shell">
            <div class="modal-header border-0 pb-0">
                <div>
                    <div class="section-kicker mb-2">Localisation</div>
                    <h2 class="font-display h1 mb-0" id="propertyMapModalLabel">Carte du logement</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="soft-copy mb-3" id="propertyMapModalCaption">La carte detaillee apparaitra ici.</p>
                <iframe class="map-frame modal-map-frame" id="propertyMapModalFrame" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a class="ghost-pill" href="#" id="propertyMapExternal" target="_blank" rel="noopener">Ouvrir dans Google Maps</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const slug = @json($slug);
    const {
        apiGet,
        escapeHtml,
        formatMoney,
        propertyLink,
        ratingStars,
        reservationLink
    } = window.DarNaSite;

    const propertyMeta = document.getElementById('propertyMeta');
    const galleryMain = document.getElementById('galleryMain');
    const galleryThumbs = document.getElementById('galleryThumbs');
    const amenitiesList = document.getElementById('amenitiesList');
    const localAreaList = document.getElementById('localAreaList');
    const factsList = document.getElementById('factsList');
    const mapLink = document.getElementById('mapLink');
    const mapModalElement = document.getElementById('propertyMapModal');
    const mapModalFrame = document.getElementById('propertyMapModalFrame');
    const mapModalCaption = document.getElementById('propertyMapModalCaption');
    const mapExternal = document.getElementById('propertyMapExternal');
    const mapModal = window.bootstrap && mapModalElement ? new window.bootstrap.Modal(mapModalElement) : null;

    let photos = [];
    let activeIndex = 0;
    let property = null;

    function renderGallery() {
        if (!photos.length) {
            galleryMain.style.background = 'linear-gradient(135deg, rgba(184,90,43,0.14), rgba(27,20,16,0.18))';
            galleryThumbs.innerHTML = '';
            return;
        }

        const activePhoto = photos[activeIndex];
        galleryMain.style.backgroundImage = `linear-gradient(180deg, rgba(14,10,8,0.08), rgba(14,10,8,0.3)), url('${activePhoto.image}')`;
        galleryThumbs.innerHTML = photos.map((photo, index) => `
            <div class="col-4">
                <button type="button" class="gallery-thumb ${index === activeIndex ? 'active' : ''}" data-index="${index}" style="background-image: url('${escapeHtml(photo.image)}')"></button>
            </div>
        `).join('');

        galleryThumbs.querySelectorAll('[data-index]').forEach(button => {
            button.addEventListener('click', () => {
                activeIndex = Number(button.dataset.index || 0);
                renderGallery();
            });
        });
    }

    function openMapModal(event) {
        if (event) {
            event.preventDefault();
        }

        if (!property?.map?.google_maps_url) {
            return;
        }

        if (!mapModal || !property.map?.embed_url) {
            window.open(property.map.google_maps_url, '_blank', 'noopener');
            return;
        }

        mapModalCaption.textContent = property.map?.label || property.location || '';
        mapModalFrame.src = property.map.embed_url;
        mapExternal.href = property.map.google_maps_url;
        mapModal.show();
    }

    mapLink.addEventListener('click', openMapModal);

    if (mapModalElement) {
        mapModalElement.addEventListener('hidden.bs.modal', () => {
            mapModalFrame.src = 'about:blank';
        });
    }

    try {
        const payload = await apiGet(`/properties/${slug}`);
        property = payload.data;

        photos = Array.isArray(property.photos) ? property.photos.map(photo => ({
            label: photo.label,
            image: photo.image
        })) : [];

        document.title = `${property.name} | DarNa`;
        document.getElementById('propertyTypeChip').textContent = property.type;
        document.getElementById('propertyEyebrow').textContent = property.eyebrow || 'Fiche detail';
        document.getElementById('propertyName').textContent = property.name;
        document.getElementById('propertySummary').textContent = property.summary || '';
        document.getElementById('propertyDescription').textContent = property.description || '';
        document.getElementById('propertyStory').textContent = property.story || '';
        document.getElementById('propertyPrice').innerHTML = `${escapeHtml(formatMoney(property.nightly_rate))} <span>/ nuit</span>`;
        document.getElementById('reserveLink').href = reservationLink(property.slug);
        mapLink.href = property.map?.google_maps_url || propertyLink(property.slug);
        document.getElementById('mapCaption').textContent = property.map?.label || property.location || '';
        document.getElementById('propertyMap').src = property.map?.embed_url || 'about:blank';
        mapExternal.href = property.map?.google_maps_url || propertyLink(property.slug);

        propertyMeta.innerHTML = [
            property.location,
            `${ratingStars(property.rating)} ${property.rating}`,
            property.reviews_label,
            `${property.max_guests} voyageurs`
        ].filter(Boolean).map(item => `<span class="meta-chip">${escapeHtml(item)}</span>`).join('');

        factsList.innerHTML = (property.facts || []).map(item => `
            <div class="fact-block">
                <div class="soft-copy small mb-1">${escapeHtml(item[0] || '')}</div>
                <div class="fw-bold">${escapeHtml(item[1] || '')}</div>
            </div>
        `).join('');

        amenitiesList.innerHTML = (property.amenities || []).map(item =>
            `<span class="data-pill">${escapeHtml(item)}</span>`
        ).join('');

        localAreaList.innerHTML = (property.local_spots || []).map(item => `
            <div class="local-item">
                <div class="fw-semibold mb-2">${escapeHtml(property.city || 'Maroc')}</div>
                <div class="soft-copy mb-0">${escapeHtml(item)}</div>
            </div>
        `).join('');

        renderGallery();
    } catch (error) {
        document.querySelector('.container').innerHTML = `
            <div class="danger-alert">
                Impossible de charger cette fiche. Verifie le slug ou le serveur Laravel.
            </div>
        `;
    }
});
</script>
@endpush
