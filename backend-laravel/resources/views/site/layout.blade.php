<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DarNa')</title>
    <meta name="description" content="@yield('meta_description', 'Sejours design au Maroc avec DarNa.')">
    <script>
        (function () {
            try {
                var savedTheme = localStorage.getItem('darna-theme');
                if (savedTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (error) {}
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('darna-site.css') }}?v={{ filemtime(public_path('darna-site.css')) }}" rel="stylesheet">
    @stack('head')
</head>
<body class="@yield('body_class')">
<div class="site-shell">
    <nav class="navbar navbar-expand-lg navbar-shell">
        <div class="container py-2">
            <a class="navbar-brand brand-mark mb-0" href="{{ route('site.home') }}">Dar<em>Na</em></a>
            <button class="navbar-toggler nav-pill px-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                Menu
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2 my-3 my-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#collections">Collections</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#stays">Sejours</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#hosting">Proprietaires</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#experience">Experience</a></li>
                    @auth
                        @if (auth()->user()?->isAdmin())
                            @php($unreadNotifications = auth()->user()->unreadNotifications()->count())
                            <li class="nav-item">
                                <a class="nav-link nav-link-badge" href="{{ route('hosting.index') }}">
                                    <span>Mon espace hote</span>
                                    @if ($unreadNotifications > 0)
                                        <span class="nav-notification-badge">{{ $unreadNotifications }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">Comptes</a></li>
                        @endif
                    @endauth
                    <li class="nav-item ms-lg-2">
                        <button type="button" class="icon-button" data-theme-toggle aria-label="Activer le mode nuit" title="Activer le mode nuit">&#9784;</button>
                    </li>
                    @auth
                        <li class="nav-item">
                            <span class="nav-user-pill">{{ auth()->user()->name }}</span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="ghost-pill nav-auth-btn" type="submit">Deconnexion</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="ghost-pill nav-auth-btn" href="{{ route('login') }}">Connexion</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @if (session('status'))
            <div class="container pt-4">
                <div class="soft-alert">{{ session('status') }}</div>
            </div>
        @endif
        @if (session('danger'))
            <div class="container pt-4">
                <div class="danger-alert">{{ session('danger') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer-shell">
        <div class="container footer-grid">
            <div>
                <a class="brand-mark d-inline-block mb-3" href="{{ route('site.home') }}">Dar<em>Na</em></a>
                <p class="soft-copy mb-0">Une plateforme plus premium pour reserver, gerer et valoriser des sejours de colocation et de location au Maroc.</p>
            </div>
            <div>
                <div class="footer-title">Explorer</div>
                <div class="footer-links">
                    <a href="{{ route('site.home') }}">Accueil</a>
                    <a href="{{ route('site.home') }}#stays">Sejours</a>
                    <a href="{{ route('site.home') }}#experience">Experience</a>
                </div>
            </div>
            <div>
                <div class="footer-title">Compte</div>
                <div class="footer-links">
                    @auth
                        @if (auth()->user()?->isAdmin())
                            <a href="{{ route('hosting.index') }}">Mon espace hote</a>
                            <a href="{{ route('hosting.create') }}">Publier un bien</a>
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                            <a href="{{ route('admin.users.index') }}">Comptes</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}">Connexion</a>
                    @endauth
                    <a href="{{ route('password.request') }}">Mot de passe oublie</a>
                </div>
            </div>
            <div>
                <div class="footer-title">Contact</div>
                <div class="footer-links">
                    <span>Casablanca, Marrakech, Tanger</span>
                    <span>hello@darna.test</span>
                    <span>+212 5 00 00 00 00</span>
                </div>
            </div>
        </div>
    </footer>
</div>

@guest
    <div class="modal fade" id="guestAuthModal" tabindex="-1" aria-labelledby="guestAuthModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-panel-shell">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <div class="section-kicker mb-2">Compte DarNa</div>
                        <h2 class="font-display h1 mb-0" id="guestAuthModalLabel">Creer ton compte ou te connecter</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body pt-3">
                    <p class="soft-copy mb-0" id="guestAuthModalCopy">
                        Ouvre l inscription ou la connexion dans une autre fenetre sans perdre la page actuelle.
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex flex-wrap justify-content-start gap-2">
                    @if (Route::has('register'))
                        <button
                            type="button"
                            class="primary-pill"
                            data-auth-open="register"
                            data-auth-url="{{ route('register') }}"
                        >
                            Creer un compte
                        </button>
                    @endif
                    <button
                        type="button"
                        class="ghost-pill"
                        data-auth-open="login"
                        data-auth-url="{{ route('login') }}"
                    >
                        Se connecter
                    </button>
                </div>
            </div>
        </div>
    </div>
@endguest

<script>
    window.darnaConfig = {
        apiBase: '{{ url('/api') }}'
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('darna-site.js') }}"></script>
@stack('scripts')
</body>
</html>
