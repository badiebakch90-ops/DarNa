<?php

namespace App\Http\Requests\Host;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHostedPropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(['Riad', 'Villa', 'Appartement', 'Maison', 'Camp'])],
            'eyebrow' => ['nullable', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:160'],
            'city' => ['nullable', 'string', 'max:120'],
            'location' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string', 'max:4000'],
            'story' => ['nullable', 'string', 'max:4000'],
            'nightly_rate' => ['required', 'integer', 'min:100', 'max:100000'],
            'max_guests' => ['required', 'integer', 'min:1', 'max:20'],
            'bedrooms_count' => ['nullable', 'integer', 'min:1', 'max:20'],
            'bathrooms_count' => ['nullable', 'integer', 'min:1', 'max:20'],
            'cover_photo' => ['nullable', 'image', 'max:8192'],
            'gallery_photos' => ['nullable', 'array', 'max:8'],
            'gallery_photos.*' => ['image', 'max:8192'],
            'thumbnail_image' => ['nullable', 'url', 'max:2048'],
            'gallery_image_1' => ['nullable', 'url', 'max:2048'],
            'gallery_image_2' => ['nullable', 'url', 'max:2048'],
            'gallery_image_3' => ['nullable', 'url', 'max:2048'],
            'amenities_text' => ['nullable', 'string', 'max:4000'],
            'local_spots_text' => ['nullable', 'string', 'max:4000'],
            'listing_tags_text' => ['nullable', 'string', 'max:2000'],
            'map_label' => ['nullable', 'string', 'max:255'],
            'map_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'map_lng' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
