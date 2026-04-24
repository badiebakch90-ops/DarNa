@extends('site.layout')

@section('title', 'DarNa | Connexion')
@section('meta_description', 'Connexion securisee DarNa. Le backoffice admin est reserve aux administrateurs autorises.')
@section('body_class', 'auth-page')

@section('content')
<section class="auth-shell">
    <div class="container">
            <div class="auth-grid">
                <div class="auth-copy-panel">
                    <div class="section-kicker mb-3">Connexion securisee</div>
                    <h1 class="section-title mb-3">Acces au <em>backoffice reserve</em></h1>
                    <p class="soft-copy mb-4">Les utilisateurs classiques n ont pas acces a l administration. Seuls les comptes autorises avec le role admin peuvent ouvrir le backoffice DarNa.</p>
                <div class="auth-feature-list">
                    <div class="auth-feature-card">
                        <strong>Backoffice reserve</strong>
                        <span>Reservations, revenus et disponibilites restent visibles uniquement pour l equipe admin autorisee.</span>
                    </div>
                    <div class="auth-feature-card">
                        <strong>Acces securise</strong>
                        <span>Un compte membre ou utilisateur standard ne peut pas afficher les pages d administration.</span>
                    </div>
                </div>
            </div>

            <div class="auth-form-panel">
                <div class="auth-panel-head">
                    <div class="section-kicker mb-2">Connexion</div>
                    <h2 class="font-display auth-title">Bon retour</h2>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="d-grid gap-3">
                    @csrf
                    <div>
                        <label class="field-label" for="email">Email</label>
                        <input class="soft-input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="field-label" for="password">Mot de passe</label>
                        <input class="soft-input" id="password" type="password" name="password" required>
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <label class="remember-row">
                        <input type="checkbox" name="remember" value="1">
                        <span>Se souvenir de moi</span>
                    </label>
                    <button class="primary-pill w-100" type="submit">Se connecter</button>
                </form>

                <div class="auth-meta-links">
                    <a href="{{ route('password.request') }}">Mot de passe oublie ?</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
