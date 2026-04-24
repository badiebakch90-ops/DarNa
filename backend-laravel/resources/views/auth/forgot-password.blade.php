@extends('site.layout')

@section('title', 'DarNa | Mot de passe oublie')
@section('meta_description', 'Recuperer l acces a ton compte DarNa.')
@section('body_class', 'auth-page')

@section('content')
<section class="auth-shell">
    <div class="container">
        <div class="auth-grid auth-grid-simple">
            <div class="auth-copy-panel">
                <div class="section-kicker mb-3">Recuperation</div>
                <h1 class="section-title mb-3">Reprends la main sur ton <em>compte</em></h1>
                <p class="soft-copy mb-0">Saisis ton email et nous t enverrons un lien de reinitialisation si un compte DarNa correspond a cette adresse.</p>
            </div>

            <div class="auth-form-panel">
                <div class="auth-panel-head">
                    <div class="section-kicker mb-2">Mot de passe oublie</div>
                    <h2 class="font-display auth-title">Recevoir un lien</h2>
                </div>

                <form method="POST" action="{{ route('password.email') }}" class="d-grid gap-3">
                    @csrf
                    <div>
                        <label class="field-label" for="email">Email</label>
                        <input class="soft-input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <button class="primary-pill w-100" type="submit">Envoyer le lien</button>
                </form>

                <div class="auth-meta-links">
                    <a href="{{ route('login') }}">Retour a la connexion</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
