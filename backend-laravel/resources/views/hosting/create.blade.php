@extends('site.layout')

@section('title', 'DarNa | Publier un bien')
@section('meta_description', 'Ajoute une maison, une villa, un appartement ou un riad sur DarNa.')

@section('content')
<section class="auth-shell">
    <div class="container">
        <div class="auth-grid">
            <div class="auth-copy-panel">
                <div class="section-kicker mb-3">Publier un bien</div>
                <h1 class="section-title mb-3">Ajoute ton logement <em>en quelques champs utiles</em></h1>
                <p class="soft-copy mb-4">Ce formulaire publie ton annonce sur la plateforme et active les notifications quand une reservation arrive sur ton bien.</p>
                <div class="auth-feature-list">
                    <div class="auth-feature-card">
                        <strong>Carte et localisation</strong>
                        <span>Tu peux definir un libelle de localisation et les coordonnees pour ouvrir la carte dans une grande fenetre.</span>
                    </div>
                    <div class="auth-feature-card">
                        <strong>Notifications de reservation</strong>
                        <span>Des qu un voyageur envoie une demande, tu la retrouves dans ton espace hote et dans tes notifications.</span>
                    </div>
                    <div class="auth-feature-card">
                        <strong>Compatible maison, villa, riad...</strong>
                        <span>Le meme flux marche pour une maison, une villa, un appartement, un riad ou un camp.</span>
                    </div>
                </div>
            </div>

            <div class="auth-form-panel">
                <div class="auth-panel-head">
                    <div class="section-kicker mb-2">Nouveau logement</div>
                    <h2 class="font-display auth-title">Publier maintenant</h2>
                </div>

                <form method="POST" action="{{ route('hosting.store') }}" class="row g-3" enctype="multipart/form-data" data-hosting-create-form>
                    @csrf

                    <div class="col-md-6">
                        <label class="field-label" for="type">Type</label>
                        <select class="soft-select" id="type" name="type" required>
                            @foreach (['Riad', 'Villa', 'Appartement', 'Maison', 'Camp'] as $type)
                                <option value="{{ $type }}" @selected(old('type') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="field-label" for="eyebrow">Sous-titre</label>
                        <input class="soft-input" id="eyebrow" type="text" name="eyebrow" value="{{ old('eyebrow') }}" placeholder="Ex: Escapade oceanique">
                        @error('eyebrow')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="field-label" for="name">Nom du logement</label>
                        <input class="soft-input" id="name" type="text" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="field-label" for="city">Ville</label>
                        <input class="soft-input" id="city" type="text" name="city" value="{{ old('city') }}" placeholder="Marrakech">
                        @error('city')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="field-label" for="location">Localisation affichee</label>
                        <input class="soft-input" id="location" type="text" name="location" value="{{ old('location') }}" placeholder="Marrakech, Medina" required>
                        @error('location')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="field-label" for="summary">Resume</label>
                        <textarea class="soft-textarea" id="summary" name="summary" rows="3" required>{{ old('summary') }}</textarea>
                        @error('summary')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="field-label" for="description">Description</label>
                        <textarea class="soft-textarea" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="field-label" for="story">Histoire du lieu</label>
                        <textarea class="soft-textarea" id="story" name="story" rows="4">{{ old('story') }}</textarea>
                        @error('story')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="field-label" for="nightly_rate">Prix / nuit</label>
                        <input class="soft-input" id="nightly_rate" type="number" min="100" name="nightly_rate" value="{{ old('nightly_rate') }}" required>
                        @error('nightly_rate')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="field-label" for="max_guests">Voyageurs max</label>
                        <input class="soft-input" id="max_guests" type="number" min="1" name="max_guests" value="{{ old('max_guests', 2) }}" required>
                        @error('max_guests')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="field-label" for="bedrooms_count">Chambres</label>
                        <input class="soft-input" id="bedrooms_count" type="number" min="1" name="bedrooms_count" value="{{ old('bedrooms_count') }}">
                        @error('bedrooms_count')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="field-label" for="bathrooms_count">Salles de bain</label>
                        <input class="soft-input" id="bathrooms_count" type="number" min="1" name="bathrooms_count" value="{{ old('bathrooms_count') }}">
                        @error('bathrooms_count')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="upload-panel">
                            <div class="d-flex flex-column flex-lg-row align-items-lg-start justify-content-lg-between gap-3">
                                <div>
                                    <label class="field-label" for="cover_photo">Photo principale depuis ton appareil</label>
                                    <input class="soft-input soft-file-input" id="cover_photo" type="file" name="cover_photo" accept="image/*">
                                    <div class="table-muted mt-2">Choisis la photo principale du logement. JPG, PNG, WEBP ou AVIF, jusqu a 8 Mo.</div>
                                    @error('cover_photo')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label class="field-label" for="gallery_photos">Autres photos du logement</label>
                                    <input class="soft-input soft-file-input" id="gallery_photos" type="file" name="gallery_photos[]" accept="image/*" multiple>
                                    <div class="table-muted mt-2">Ajoute plusieurs photos de la cuisine, des chambres, de la facade ou de la piscine.</div>
                                    @error('gallery_photos')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                    @error('gallery_photos.*')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="photo-preview-grid mt-4" data-hosting-photo-preview>
                                <div class="photo-preview-empty">
                                    Les photos choisies apparaitront ici avant la publication.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <details class="upload-alt-box">
                            <summary>Ou utiliser des liens d images</summary>
                            <div class="row g-3 mt-1">
                                <div class="col-12">
                                    <label class="field-label" for="thumbnail_image">Image principale URL</label>
                                    <input class="soft-input" id="thumbnail_image" type="url" name="thumbnail_image" value="{{ old('thumbnail_image') }}" placeholder="https://...">
                                    @error('thumbnail_image')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="field-label" for="gallery_image_1">Photo galerie 1</label>
                                    <input class="soft-input" id="gallery_image_1" type="url" name="gallery_image_1" value="{{ old('gallery_image_1') }}" placeholder="https://...">
                                    @error('gallery_image_1')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="field-label" for="gallery_image_2">Photo galerie 2</label>
                                    <input class="soft-input" id="gallery_image_2" type="url" name="gallery_image_2" value="{{ old('gallery_image_2') }}" placeholder="https://...">
                                    @error('gallery_image_2')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="field-label" for="gallery_image_3">Photo galerie 3</label>
                                    <input class="soft-input" id="gallery_image_3" type="url" name="gallery_image_3" value="{{ old('gallery_image_3') }}" placeholder="https://...">
                                    @error('gallery_image_3')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </details>
                    </div>

                    <div class="col-12">
                        <label class="field-label" for="amenities_text">Equipements</label>
                        <textarea class="soft-textarea" id="amenities_text" name="amenities_text" rows="3" placeholder="Wi-Fi, Piscine, Cuisine equipee">{{ old('amenities_text') }}</textarea>
                        <div class="table-muted mt-2">Separe les elements avec des virgules ou des retours a la ligne.</div>
                        @error('amenities_text')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="field-label" for="local_spots_text">Autour du logement</label>
                        <textarea class="soft-textarea" id="local_spots_text" name="local_spots_text" rows="3" placeholder="Plage a 10 min&#10;Souk a 5 min">{{ old('local_spots_text') }}</textarea>
                        @error('local_spots_text')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="field-label" for="listing_tags_text">Tags de l annonce</label>
                        <textarea class="soft-textarea" id="listing_tags_text" name="listing_tags_text" rows="2" placeholder="Vue mer, Family, Rooftop">{{ old('listing_tags_text') }}</textarea>
                        @error('listing_tags_text')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="field-label" for="map_label">Texte de localisation</label>
                        <input class="soft-input" id="map_label" type="text" name="map_label" value="{{ old('map_label') }}" placeholder="Proche de la plage">
                        @error('map_label')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="field-label" for="map_lat">Latitude</label>
                        <input class="soft-input" id="map_lat" type="number" step="0.0000001" name="map_lat" value="{{ old('map_lat') }}" placeholder="33.5731">
                        @error('map_lat')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="field-label" for="map_lng">Longitude</label>
                        <input class="soft-input" id="map_lng" type="number" step="0.0000001" name="map_lng" value="{{ old('map_lng') }}" placeholder="-7.5898">
                        @error('map_lng')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                        <button class="primary-pill" type="submit">Publier le logement</button>
                        <a class="ghost-pill" href="{{ route('hosting.index') }}">Retour a mon espace</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
