@extends('site.layout')

@section('title', 'DarNa | Reinitialiser le mot de passe')
@section('meta_description', 'Definir un nouveau mot de passe DarNa.')
@section('body_class', 'auth-page')

@section('content')
<section class="auth-shell">
    <div class="container">
        <div class="auth-grid auth-grid-simple">
            <div class="auth-copy-panel">
                <div class="section-kicker mb-3">Nouveau mot de passe</div>
                <h1 class="section-title mb-3">Choisis un mot de passe <em>plus solide</em></h1>
                <p class="soft-copy mb-0">Une fois valide, tu pourras te reconnecter sereinement et retrouver l'acces autorise a ton espace DarNa.</p>
            </div>

            <div class="auth-form-panel">
                <div class="auth-panel-head">
                    <div class="section-kicker mb-2">Reset</div>
                    <h2 class="font-display auth-title">Mettre a jour l'acces</h2>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="d-grid gap-3">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label class="field-label" for="email">Email</label>
                        <input class="soft-input" id="email" type="email" name="email" value="{{ old('email', $email) }}" required>
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

                    <div>
                        <label class="field-label" for="password_confirmation">Confirmation</label>
                        <input class="soft-input" id="password_confirmation" type="password" name="password_confirmation" required>
                    </div>

                    <button class="primary-pill w-100" type="submit">Mettre a jour</button>
                </form>

                <div class="auth-meta-links">
                    <a href="{{ route('login') }}">Retour a la connexion</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
