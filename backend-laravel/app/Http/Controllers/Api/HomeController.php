<?php

/**
 * ─────────────────────────────────────────────────────────────
 *   DarNa — Plateforme de location authentique au Maroc 🇲🇦
 *   Author    : Abdelbadie Abkhich (@badiebakch90-ops)
 *   Original  : https://github.com/badiebakch90-ops/DarNa
 *   Copyright : © 2026 Abdelbadie Abkhich — All rights reserved
 *   License   : See LICENSE file
 * ─────────────────────────────────────────────────────────────
 */


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\Property;

class HomeController extends Controller
{
    public function __invoke()
    {
        $properties = Property::query()->with('photos')->orderByDesc('featured')->get();

        $spotlight = $properties->firstWhere('featured', true) ?? $properties->first();

        $categories = $properties
            ->groupBy(fn (Property $property) => strtolower($property->type))
            ->map(fn ($items, $type) => [
                'type' => $type,
                'count' => $items->count(),
            ])
            ->values();

        $collectionsBlueprints = [
            [
                'slug' => 'riad-al-baraka',
                'filter' => 'riad',
                'city' => 'Marrakech',
                'title' => 'Rooftop Riads',
                'copy' => 'Patios, lanterns, plunge pools and late dinners for a deeply visual stay.',
                'tags' => ['Riad', 'Patio', '3 nights'],
            ],
            [
                'slug' => 'villa-oasis-bleue',
                'filter' => 'villa',
                'city' => 'Atlantique',
                'title' => 'Ocean Villas',
                'copy' => 'Large rooms, a pool and soft light to slow down without losing comfort.',
                'tags' => ['Villa', 'Pool', 'Sunset'],
            ],
            [
                'slug' => 'camp-merzouga-etoiles',
                'filter' => 'camp',
                'city' => 'Merzouga',
                'title' => 'Sahara Under Stars',
                'copy' => 'A calmer desert rhythm with campfire evenings and an open night sky.',
                'tags' => ['Camp', 'Dunes', 'Night sky'],
            ],
        ];

        $collections = collect($collectionsBlueprints)
            ->map(function (array $collection) use ($properties) {
                $property = $properties->firstWhere('slug', $collection['slug']);

                if (! $property) {
                    return null;
                }

                return [
                    ...$collection,
                    'image' => $property->thumbnail_image ?: optional($property->photos->first())->image_url,
                    'property' => new PropertyResource($property),
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'data' => [
                'stats' => [
                    'properties_count' => $properties->count(),
                    'cities_count' => $properties->pluck('city')->filter()->unique()->count(),
                    'satisfaction_rate' => 98,
                ],
                'spotlight' => $spotlight ? [
                    'property' => new PropertyResource($spotlight),
                    'note_one' => [
                        'value' => '48h',
                        'text' => 'A short city, spa and rooftop dinner escape already shaped for you.',
                    ],
                    'note_two' => [
                        'value' => '4.9/5',
                        'text' => 'Homes presented with a sharper editorial and premium feel.',
                    ],
                    'quote' => [
                        'stars' => '★★★★★',
                        'text' => 'You understand everything in seconds, then booking feels effortless.',
                        'author' => 'Nadia, Casablanca',
                    ],
                ] : null,
                'categories' => $categories,
                'collections' => $collections,
            ],
        ]);
    }
}
