<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lat = $this->map_lat ? (float) $this->map_lat : null;
        $lng = $this->map_lng ? (float) $this->map_lng : null;

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'type' => $this->type,
            'eyebrow' => $this->eyebrow,
            'name' => $this->name,
            'city' => $this->city,
            'location' => $this->location,
            'summary' => $this->summary,
            'description' => $this->description,
            'story' => $this->story,
            'nightly_rate' => $this->nightly_rate,
            'formatted_price' => number_format($this->nightly_rate, 0, ',', ' ').' MAD / nuit',
            'rating' => (float) $this->rating,
            'reviews_count' => $this->reviews_count,
            'reviews_label' => $this->reviews_count.' avis',
            'max_guests' => $this->max_guests,
            'bedrooms_count' => $this->bedrooms_count,
            'bathrooms_count' => $this->bathrooms_count,
            'thumbnail_image' => $this->thumbnail_image,
            'type_badge_color' => $this->type_badge_color,
            'gradient' => $this->gradient,
            'facts' => $this->facts ?? [],
            'amenities' => $this->amenities ?? [],
            'local_spots' => $this->local_spots ?? [],
            'listing_tags' => $this->listing_tags ?? [],
            'featured' => (bool) $this->featured,
            'map' => [
                'label' => $this->map_label,
                'lat' => $lat,
                'lng' => $lng,
                'google_maps_url' => ($lat !== null && $lng !== null)
                    ? "https://www.google.com/maps?q={$lat},{$lng}"
                    : null,
                'embed_url' => ($lat !== null && $lng !== null)
                    ? "https://www.google.com/maps?q={$lat},{$lng}&z=14&output=embed"
                    : null,
            ],
            'photos' => $this->whenLoaded('photos', function () {
                return $this->photos->map(fn ($photo) => [
                    'label' => $photo->label,
                    'image' => $photo->image_url,
                    'position' => $photo->position,
                ])->values();
            }),
        ];
    }
}
