@extends('site.layout')

@section('title', 'DarNa | Sejours design au Maroc')
@section('meta_description', 'Home redesign DarNa avec Laravel, Bootstrap et JS.')
@section('body_class', 'home-page')

@section('content')
<section class="mobile-home-shell">
    <div class="mobile-home-top-shell">
        <div class="container">
            <div class="mobile-home-top">
                <button class="mobile-search-trigger" id="mobileSearchTrigger" type="button" aria-haspopup="dialog" aria-expanded="false">
                    <span class="mobile-search-trigger-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle cx="11" cy="11" r="6.5"></circle>
                            <path d="M16 16L21 21"></path>
                        </svg>
                    </span>
                    <span class="mobile-search-trigger-copy">
                        <strong id="mobileSearchTriggerTitle">Rechercher</strong>
                        <span id="mobileSearchTriggerSummary">Destination, type de bien et voyageurs</span>
                    </span>
                </button>

                <div class="mobile-search-overlay" id="mobileSearchOverlay" hidden>
                    <button class="mobile-search-overlay-backdrop" type="button" data-mobile-search-close aria-label="Fermer la recherche"></button>
                    <div class="mobile-search-dialog" role="dialog" aria-modal="true" aria-labelledby="mobileSearchDialogTitle">
                        <section class="mobile-search-panel" aria-label="Recherche mobile">
                            <div class="mobile-search-sheet">
                                <div class="mobile-search-sheet-head">
                                    <button class="mobile-search-close" id="mobileSearchClose" type="button" aria-label="Retour">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M19 12H5"></path>
                                            <path d="M11 6L5 12L11 18"></path>
                                        </svg>
                                    </button>
                                    <h1 class="mobile-search-title" id="mobileSearchDialogTitle">Ou allez-vous ?</h1>
                                </div>

                                <form class="mobile-search-form" id="mobileSearchForm">
                                    <label class="visually-hidden" for="mobileDestinationInput">Recherche mobile</label>
                                    <div class="mobile-search-field">
                                        <span class="mobile-search-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <circle cx="11" cy="11" r="6.5"></circle>
                                                <path d="M16 16L21 21"></path>
                                            </svg>
                                        </span>
                                        <input class="mobile-search-input" id="mobileDestinationInput" type="text" placeholder="Rechercher une destination">
                                    </div>
                                </form>

                                <div class="mobile-search-suggestions">
                                    <div class="mobile-search-label">Destinations suggerees</div>
                                    <div class="mobile-destination-list" id="mobileDestinationSuggestions">
                                        <div class="mobile-destination-empty">Chargement des suggestions...</div>
                                    </div>
                                </div>

                                <div class="mobile-search-types">
                                    <div class="mobile-search-label">Types de biens</div>
                                    <div class="mobile-type-grid" id="mobileTypeSuggestions">
                                        <div class="mobile-destination-empty">Chargement des types...</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mobile-search-meta">
                                <div class="mobile-meta-row">
                                    <span>Quand</span>
                                    <strong id="mobileDatesSummary">Ajouter des dates</strong>
                                </div>

                                <div class="mobile-meta-row">
                                    <span>Voyageurs</span>
                                    <strong id="mobileGuestsSummary">2 voyageurs</strong>
                                </div>
                            </div>

                            <div class="mobile-search-actions">
                                <button class="mobile-clear-action" id="mobileClearSearch" type="button">Tout effacer</button>
                                <button class="mobile-submit-action" type="submit" form="mobileSearchForm">
                                    <span class="mobile-submit-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <circle cx="11" cy="11" r="6.5"></circle>
                                            <path d="M16 16L21 21"></path>
                                        </svg>
                                    </span>
                                    <span>Rechercher</span>
                                </button>
                            </div>
                        </section>
                    </div>
                </div>

                <nav class="mobile-home-tabs" aria-label="Sections principales">
                    <a class="mobile-home-tab is-active" href="#mobile-homes" data-mobile-tab>Sejours</a>
                    <a class="mobile-home-tab" href="#mobile-experiences" data-mobile-tab>Experiences</a>
                    <a class="mobile-home-tab" href="#mobile-services" data-mobile-tab>Services</a>
                </nav>
            </div>
        </div>
    </div>

    <div class="mobile-home-stack">
        <section class="mobile-browse-block" id="mobile-homes">
            <div id="mobileCitySections">
                <div class="container">
                    <div class="loading-state">Chargement des sejours...</div>
                </div>
            </div>
        </section>

        <section class="mobile-browse-block" id="mobile-experiences">
            <div class="container">
                <div class="mobile-section-head">
                    <div>
                        <h2 class="mobile-section-title">Inspirations DarNa</h2>
                        <p class="mobile-section-copy">Des adresses plus editoriales pour naviguer vite sur mobile.</p>
                    </div>
                    <button class="mobile-section-arrow" type="button" data-rail-next="mobileCollectionsRail" aria-label="Faire defiler les inspirations">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M5 12H19"></path>
                            <path d="M13 6L19 12L13 18"></path>
                        </svg>
                    </button>
                </div>

                <div class="mobile-scroll-rail" id="mobileCollectionsRail">
                    <div class="mobile-empty-card">Chargement des inspirations...</div>
                </div>
            </div>
        </section>

        <section class="mobile-browse-block mobile-services-block" id="mobile-services">
            <div class="container">
                <div class="mobile-section-head">
                    <div>
                        <h2 class="mobile-section-title">Services DarNa</h2>
                        <p class="mobile-section-copy">Compte, publication et accompagnement dans un parcours plus simple sur mobile.</p>
                    </div>
                </div>

                <div class="mobile-service-grid">
                    <a class="mobile-service-card" href="#mobile-homes">
                        <span class="mobile-service-kicker">Explorer</span>
                        <strong>Voir les logements</strong>
                        <span>Recherche rapide, cartes horizontales et acces direct aux fiches.</span>
                    </a>

                    @auth
                        @if (auth()->user()?->isAdmin())
                            <a class="mobile-service-card" href="{{ route('hosting.create') }}">
                                <span class="mobile-service-kicker">Publication</span>
                                <strong>Publier un bien</strong>
                                <span>Ajoute un logement, ses photos et ses informations depuis ton espace hote.</span>
                            </a>

                            <a class="mobile-service-card" href="{{ route('dashboard') }}">
                                <span class="mobile-service-kicker">Gestion</span>
                                <strong>Ouvrir le dashboard</strong>
                                <span>Retrouve les reservations, les comptes et les statistiques du projet.</span>
                            </a>
                        @else
                            <a class="mobile-service-card" href="#experience">
                                <span class="mobile-service-kicker">Parcours</span>
                                <strong>Comprendre DarNa</strong>
                                <span>Reservation, securite et experience mobile clarifiee en quelques etapes.</span>
                            </a>

                            <form class="mobile-service-card mobile-service-form" method="POST" action="{{ route('logout') }}">
                                @csrf
                                <span class="mobile-service-kicker">Compte</span>
                                <strong>Se deconnecter</strong>
                                <span>Ferme la session actuelle sans quitter la page d accueil.</span>
                                <button class="mobile-service-button" type="submit">Deconnexion</button>
                            </form>
                        @endif
                    @else
                        <a class="mobile-service-card" href="{{ route('login') }}">
                            <span class="mobile-service-kicker">Compte</span>
                            <strong>Se connecter</strong>
                            <span>Accede a ton espace et retrouve les fonctions d administration disponibles.</span>
                        </a>

                        <a
                            class="mobile-service-card"
                            href="{{ route('login') }}"
                            data-auth-prompt
                            data-auth-title="Connexion administrateur requise"
                            data-auth-copy="La publication d un bien est reservee aux administrateurs. Ouvre la connexion sans quitter la page mobile."
                            data-auth-login-url="{{ route('login') }}"
                            @if (Route::has('register'))
                                data-auth-register-url="{{ route('register') }}"
                            @endif
                        >
                            <span class="mobile-service-kicker">Publication</span>
                            <strong>Publier un bien</strong>
                            <span>Connecte toi pour acceder a l espace hote et ajouter un logement.</span>
                        </a>
                    @endauth
                </div>
            </div>
        </section>
    </div>

    <nav class="mobile-bottom-nav" aria-label="Navigation mobile">
        <a class="mobile-bottom-link is-active" href="{{ route('site.home') }}">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="11" cy="11" r="6.5"></circle>
                <path d="M16 16L21 21"></path>
            </svg>
            <span>Explorer</span>
        </a>
        <a class="mobile-bottom-link" href="#mobile-experiences">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 4L14.7 9.3L20.5 10.1L16.2 14.2L17.2 20L12 17.2L6.8 20L7.8 14.2L3.5 10.1L9.3 9.3L12 4Z"></path>
            </svg>
            <span>Inspirations</span>
        </a>
        @auth
            <a class="mobile-bottom-link" href="#mobile-services">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="8" r="3.5"></circle>
                    <path d="M5 20C5.8 16.9 8.5 15 12 15C15.5 15 18.2 16.9 19 20"></path>
                </svg>
                <span>Compte</span>
            </a>
        @else
            <a class="mobile-bottom-link" href="{{ route('login') }}">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="8" r="3.5"></circle>
                    <path d="M5 20C5.8 16.9 8.5 15 12 15C15.5 15 18.2 16.9 19 20"></path>
                </svg>
                <span>Connexion</span>
            </a>
        @endauth
    </nav>
</section>

<div class="desktop-home-flow">
<section class="section-space">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-card hero-card-accent">
                <div class="section-kicker mb-3" id="heroKicker">Sejours au Maroc</div>
                <h1 class="section-title" id="heroTitle">Sejourner au Maroc avec une <em>experience plus claire</em></h1>
                <p class="soft-copy mb-4" id="heroCopy">
                    Cette nouvelle version garde ton backend Laravel et refond l interface avec une mise en page plus premium,
                    plus lisible sur mobile, et totalement reliee a l API des logements.
                </p>

                <div class="search-panel mb-4">
                    <form class="row g-3" id="searchForm">
                        <div class="col-md-5">
                            <label class="field-label" for="destinationInput">Recherche</label>
                            <input class="soft-input" id="destinationInput" type="text" placeholder="Ville, logement, tag, equipement...">
                        </div>
                        <div class="col-md-3">
                            <label class="field-label" for="typeInput">Type</label>
                            <select class="soft-select" id="typeInput">
                                <option value="">Tous</option>
                                <option value="riad">Riad</option>
                                <option value="villa">Villa</option>
                                <option value="appartement">Appartement</option>
                                <option value="maison">Maison</option>
                                <option value="camp">Camp</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="field-label" for="guestsInput">Voyageurs</label>
                            <input class="soft-input" id="guestsInput" type="number" min="1" value="2">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="primary-pill w-100" type="submit">Rechercher</button>
                        </div>
                    </form>
                </div>

                <div class="stat-strip" id="statsGrid">
                    <article class="glass-panel stat-card">
                        <div class="stat-value" id="statProperties">0<span>+</span></div>
                        <div class="soft-copy mb-0">Logements verifies</div>
                    </article>
                    <article class="glass-panel stat-card">
                        <div class="stat-value" id="statCities">0<span>+</span></div>
                        <div class="soft-copy mb-0">Villes couvertes</div>
                    </article>
                    <article class="glass-panel stat-card">
                        <div class="stat-value" id="statSatisfaction">0<span>%</span></div>
                        <div class="soft-copy mb-0">Satisfaction annoncee</div>
                    </article>
                </div>
            </div>

            <aside class="hero-card spotlight-card">
                <div class="spotlight-media mb-3" id="spotlightMedia"></div>
                <div class="d-flex flex-wrap gap-2 mb-3" id="spotlightTags"></div>
                <div class="section-kicker mb-2" id="spotlightKicker">Spotlight</div>
                <h2 class="font-display display-5 mb-2" id="spotlightName">Chargement...</h2>
                <p class="soft-copy mb-4" id="spotlightCopy">Le logement mis en avant apparait ici avec ses vraies donnees API.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="primary-pill" href="#" id="spotlightLink">Voir le logement</a>
                    <span class="nav-pill" id="spotlightLocation">Maroc</span>
                </div>
            </aside>
        </div>
    </div>
</section>

<section class="section-space pt-0" id="collections">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
            <div>
                <div class="section-kicker mb-3" id="collectionsKicker">Collections</div>
                <h2 class="section-title mb-2" id="collectionsTitle">Des capsules de voyage <em>plus editoriales</em></h2>
                <p class="soft-copy mb-0" id="collectionsCopy">Chaque collection reprend une famille de sejours et t emmene directement vers les fiches detail.</p>
            </div>
        </div>
        <div class="row g-4" id="collectionsGrid">
            <div class="col-12">
                <div class="loading-state">Chargement des collections...</div>
            </div>
        </div>
    </div>
</section>

<section class="section-space pt-0">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
            <div>
                <div class="section-kicker mb-3" id="categoriesKicker">Categories</div>
                <h2 class="section-title mb-2" id="categoriesTitle">Explorer par <em>type de bien</em></h2>
            </div>
        </div>
        <div class="row g-4" id="categoriesGrid">
            <div class="col-12">
                <div class="loading-state">Chargement des categories...</div>
            </div>
        </div>
    </div>
</section>

<section class="section-space pt-0" id="stays">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
            <div>
                <div class="section-kicker mb-3" id="staysKicker">Sejours disponibles</div>
                <h2 class="section-title mb-2" id="staysTitle">Decouvre des logements <em>adaptes a ton sejour</em></h2>
                <p class="soft-copy mb-0" id="listingFeedback">Affinez la recherche par destination, type de bien et nombre de voyageurs.</p>
            </div>
        </div>
        <div class="row g-4" id="staysGrid">
            <div class="col-12">
                <div class="loading-state">Chargement des sejours...</div>
            </div>
        </div>
    </div>
</section>

<section class="section-space pt-0" id="hosting">
    <div class="container">
        <div class="hero-card hero-card-accent">
            <div class="row g-4 align-items-center">
                <div class="col-lg-8">
                    <div class="section-kicker mb-3" id="hostingKicker">Proprietaires</div>
                    <h2 class="section-title mb-3" id="hostingTitle">Publie ton bien et recois des demandes <em>de reservation</em></h2>
                    <p class="soft-copy mb-0" id="hostingCopy">
                        L espace hote permet de publier un logement, suivre ses annonces
                        et recevoir une notification a chaque nouvelle reservation.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="side-card h-100">
                        <div class="soft-copy mb-3">Fonctions hote</div>
                        <div class="d-flex flex-wrap gap-2" id="hostingTags">
                            <span class="tiny-tag">Ajouter un bien</span>
                            <span class="tiny-tag">Carte dynamique</span>
                            <span class="tiny-tag">Notifications</span>
                            <span class="tiny-tag">Reservations</span>
                            <span class="tiny-tag">Espace hote</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space pt-0" id="experience">
    <div class="container">
        <div class="experience-grid">
            <article class="experience-card">
                <div class="section-kicker mb-3" id="experienceKicker">Pourquoi DarNa</div>
                <h2 class="font-display display-5 mb-3" id="experienceTitle">Un parcours de reservation <em>plus net</em></h2>
                <p class="soft-copy mb-0" id="experienceCopy">Recherche claire, disponibilite verifiee, interface mobile plus propre et navigation fluide entre listing, fiche, reservation et espace compte.</p>
            </article>
            <article class="experience-card">
                <div class="experience-step">01</div>
                <h3 class="font-display h2 mb-3">Choisir</h3>
                <p class="soft-copy mb-0" id="experienceStepOneCopy">Filtre par destination, type et capacite avec des cartes plus editoriales et mieux structurees.</p>
            </article>
            <article class="experience-card">
                <div class="experience-step">02</div>
                <h3 class="font-display h2 mb-3">Verifier</h3>
                <p class="soft-copy mb-0" id="experienceStepTwoCopy">Chaque demande controle la disponibilite et bloque les chevauchements de reservation.</p>
            </article>
            <article class="experience-card">
                <div class="experience-step">03</div>
                <h3 class="font-display h2 mb-3">Gerer</h3>
                <p class="soft-copy mb-0" id="experienceStepThreeCopy">Connexion securisee, mot de passe oublie, espace hote et backoffice admin pour suivre l activite sans exposer les donnees sensibles.</p>
            </article>
        </div>
    </div>
</section>

<section class="section-space pt-0">
    <div class="container">
        <div class="cta-banner">
            <div>
                <div class="section-kicker mb-3" id="ctaKicker">Acces DarNa</div>
                <h2 class="font-display display-5 mb-3" id="ctaTitle">Un site public clair, avec un <em>espace hote et un backoffice admin</em></h2>
                <p class="soft-copy mb-0" id="ctaCopy">Les voyageurs reservent, les hotes publient leurs biens et recoivent les notifications, tandis que l administration reste reservee aux administrateurs autorises.</p>
            </div>
            <div class="d-flex flex-wrap gap-3">
                @auth
                    @if (auth()->user()?->isAdmin())
                        <a class="primary-pill" href="{{ route('hosting.create') }}">Publier mon bien</a>
                        <a class="ghost-pill" href="{{ route('hosting.index') }}">Mon espace hote</a>
                    @else
                        <span class="nav-pill">Publication reservee aux administrateurs</span>
                    @endif
                @else
                    <a
                        class="primary-pill"
                        href="{{ route('login') }}"
                        data-auth-prompt
                        data-auth-title="Connexion administrateur requise"
                        data-auth-copy="La publication d un bien est reservee aux administrateurs. Ouvre la connexion dans une autre fenetre si tu geres le projet."
                        data-auth-login-url="{{ route('login') }}"
                        @if (Route::has('register'))
                            data-auth-register-url="{{ route('register') }}"
                        @endif
                    >
                        Connexion admin pour publier
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('home-dynamic.js') }}?v={{ filemtime(public_path('home-dynamic.js')) }}"></script>
@endpush
