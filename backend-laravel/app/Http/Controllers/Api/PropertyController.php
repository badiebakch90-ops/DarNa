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
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('query')->trim()->toString();

        if ($search === '') {
            $search = $request->string('destination')->trim()->toString();
        }

        $properties = Property::query()
            ->with('photos')
            ->when(
                $request->string('type')->toString(),
                fn ($query, $type) => $query->whereRaw('LOWER(type) = ?', [strtolower($type)])
            )
            ->when(
                $request->integer('guests'),
                fn ($query, $guests) => $query->where('max_guests', '>=', $guests)
            )
            ->orderByDesc('featured')
            ->orderBy('name')
            ->get()
            ->when(
                $search !== '',
                fn (Collection $properties) => $properties
                    ->filter(fn (Property $property) => $this->matchesSearch($property, $search))
                    ->values()
            );

        return PropertyResource::collection($properties);
    }

    private function matchesSearch(Property $property, string $search): bool
    {
        $needle = $this->normalizeSearchValue($search);

        if ($needle === '') {
            return true;
        }

        return str_contains($this->searchableText($property), $needle);
    }

    private function searchableText(Property $property): string
    {
        return $this->normalizeSearchValue(implode(' ', array_filter([
            $property->slug,
            $property->type,
            $property->eyebrow,
            $property->name,
            $property->city,
            $property->location,
            $property->summary,
            $property->description,
            $property->story,
            $property->map_label,
            collect($property->facts ?? [])->flatten()->implode(' '),
            implode(' ', $property->amenities ?? []),
            implode(' ', $property->local_spots ?? []),
            implode(' ', $property->listing_tags ?? []),
        ])));
    }

    private function normalizeSearchValue(?string $value): string
    {
        return Str::of($value ?? '')
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '')
            ->toString();
    }

    public function show(string $slug)
    {
        $property = Property::query()
            ->with('photos')
            ->where('slug', $slug)
            ->firstOrFail();

        return new PropertyResource($property);
    }

    public function availability(Request $request, string $slug)
    {
        $property = Property::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $request->validate([
            'arrival_date' => ['required', 'date'],
            'departure_date' => ['required', 'date', 'after:arrival_date'],
        ]);

        $arrivalDate = $request->string('arrival_date')->toString();
        $departureDate = $request->string('departure_date')->toString();

        $conflictingReservations = Reservation::query()
            ->active()
            ->where('property_id', $property->id)
            ->overlapping($arrivalDate, $departureDate)
            ->orderBy('arrival_date')
            ->get();

        return response()->json([
            'data' => [
                'property' => [
                    'slug' => $property->slug,
                    'name' => $property->name,
                ],
                'arrival_date' => $arrivalDate,
                'departure_date' => $departureDate,
                'available' => $conflictingReservations->isEmpty(),
                'conflicts_count' => $conflictingReservations->count(),
                'blocked_ranges' => $conflictingReservations->map(fn (Reservation $reservation) => [
                    'arrival_date' => $reservation->arrival_date->toDateString(),
                    'departure_date' => $reservation->departure_date->toDateString(),
                    'status' => $reservation->status,
                ])->values(),
            ],
        ]);
    }
}
