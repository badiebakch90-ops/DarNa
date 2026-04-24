@extends('site.layout')

@section('title', 'DarNa | Inscription')
@section('meta_description', 'Creer ton compte DarNa.')
@section('body_class', 'auth-page')

@section('content')
<section class="auth-shell">
    <div class="container">
        <div class="auth-grid">
            <div class="auth-copy-panel">
                <div class="section-kicker mb-3">Inscription</div>
                <h1 class="section-title mb-3">Cree un compte <em>pour simplifier tes demandes</em></h1>
                <p class="soft-copy mb-4">Le compte DarNa te permet de centraliser tes informations et de preparer les prochaines evolutions de la plateforme, sans ouvrir l'acces au backoffice interne.</p>
                <div class="auth-feature-list">
                    <div class="auth-feature-card">
                        <strong>Profil complet</strong>
                        <span>Nom, email et telephone pour te joindre facilement.</span>
                    </div>
                    <div class="auth-feature-card">
                        <strong>Compte client</strong>
                        <span>Une fois inscrit, tu reviens sur le site public avec une connexion active.</span>
                    </div>
                </div>
            </div>

            <div class="auth-form-panel">
                <div class="auth-panel-head">
                    <div class="section-kicker mb-2">Nouveau compte</div>
                    <h2 class="font-display auth-title">Bienvenue sur DarNa</h2>
                </div>

                <form method="POST" action="{{ route('register.store') }}" class="d-grid gap-3">
                    @csrf
                    <div>
                        <label class="field-label" for="name">Nom complet</label>
                        <input class="soft-input" id="name" type="text" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="field-label" for="email">Email</label>
                        <input class="soft-input" id="email" type="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="field-label" for="phone">Telephone</label>
                        <input class="soft-input" id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="+212 6 00 00 00 00">
                        @error('phone')
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
                    <div>
                        <label class="field-label" for="password_confirmation">Confirmation</label>
                        <input class="soft-input" id="password_confirmation" type="password" name="password_confirmation" required>
                    </div>

                    <button class="primary-pill w-100" type="submit">Creer mon compte</button>
                </form>

                <div class="auth-meta-links">
                    <a href="{{ route('login') }}">J'ai deja un compte</a>
                    <a href="{{ route('password.request') }}">Besoin d'aide pour ton mot de passe ?</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
