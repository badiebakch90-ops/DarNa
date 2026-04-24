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


namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\StoreHostedPropertyRequest;
use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HostingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $properties = $user->properties()
            ->withCount([
                'reservations',
                'reservations as pending_reservations_count' => fn ($query) => $query->where('status', 'pending'),
            ])
            ->latest()
            ->get();

        $propertyIds = $properties->pluck('id');

        $latestReservations = $propertyIds->isEmpty()
            ? collect()
            : Reservation::query()
                ->whereIn('property_id', $propertyIds)
                ->latest()
                ->limit(10)
                ->get();

        $notifications = $user->notifications()
            ->latest()
            ->limit(10)
            ->get();

        $unreadNotificationsCount = $user->unreadNotifications()->count();

        if ($unreadNotificationsCount > 0) {
            $user->unreadNotifications()->update(['read_at' => now()]);
        }

        return view('hosting.index', [
            'properties' => $properties,
            'latestReservations' => $latestReservations,
            'notifications' => $notifications,
            'stats' => [
                'properties' => $properties->count(),
                'reservations' => $latestReservations->count(),
                'pending' => $properties->sum('pending_reservations_count'),
                'unread_notifications' => $unreadNotificationsCount,
            ],
        ]);
    }

    public function create()
    {
        return view('hosting.create');
    }

    public function store(StoreHostedPropertyRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $type = $this->normalizeType($validated['type']);
        $uploadedCover = $this->storeUploadedPhoto($request->file('cover_photo'), 'cover');
        $uploadedGalleryImages = $this->storeUploadedGalleryPhotos($request->file('gallery_photos', []));
        $galleryImages = collect()
            ->when($uploadedCover !== null, fn (Collection $photos) => $photos->push($uploadedCover))
            ->when($uploadedCover === null && filled($validated['thumbnail_image'] ?? null), fn (Collection $photos) => $photos->push($validated['thumbnail_image']))
            ->merge($uploadedGalleryImages)
            ->merge($this->galleryUrlsFromPayload($validated))
            ->filter(fn (?string $url) => filled($url))
            ->unique()
            ->values();
        $thumbnail = $uploadedCover ?: ($validated['thumbnail_image'] ?? null);

        if ($thumbnail === null && $galleryImages->isNotEmpty()) {
            $thumbnail = $galleryImages->first();
        }

        $property = DB::transaction(function () use ($request, $validated, $type, $thumbnail, $galleryImages) {
            $property = Property::query()->create([
                'owner_id' => $request->user()->id,
                'slug' => $this->generateUniqueSlug($validated['name']),
                'type' => $type,
                'eyebrow' => $validated['eyebrow'] ?: 'Nouveau logement hote',
                'name' => $validated['name'],
                'city' => $validated['city'] ?: null,
                'location' => $validated['location'],
                'summary' => $validated['summary'],
                'description' => $validated['description'],
                'story' => $validated['story'] ?: $validated['description'],
                'nightly_rate' => (int) $validated['nightly_rate'],
                'rating' => 0,
                'reviews_count' => 0,
                'max_guests' => (int) $validated['max_guests'],
                'bedrooms_count' => $validated['bedrooms_count'] ?: null,
                'bathrooms_count' => $validated['bathrooms_count'] ?: null,
                'thumbnail_image' => $thumbnail,
                'type_badge_color' => $this->styleForType($type)['badge_color'],
                'gradient' => $this->styleForType($type)['gradient'],
                'facts' => $this->buildFacts($validated),
                'amenities' => $this->extractList($validated['amenities_text'] ?? ''),
                'local_spots' => $this->extractList($validated['local_spots_text'] ?? ''),
                'listing_tags' => $this->extractList($validated['listing_tags_text'] ?? '', 8),
                'map_label' => $validated['map_label'] ?: 'Localisation de '.$validated['name'],
                'map_lat' => $validated['map_lat'] ?: null,
                'map_lng' => $validated['map_lng'] ?: null,
                'featured' => false,
            ]);

            $galleryImages
                ->unique()
                ->values()
                ->each(function (string $imageUrl, int $index) use ($property): void {
                    $property->photos()->create([
                        'label' => $index === 0 ? 'Photo principale' : 'Vue '.($index + 1),
                        'image_url' => $imageUrl,
                        'position' => $index + 1,
                    ]);
                });

            return $property;
        });

        return redirect()
            ->route('hosting.index')
            ->with('status', "Ton logement {$property->name} est maintenant publie sur la plateforme.");
    }

    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'logement-'.Str::lower(Str::random(6));
        }

        $slug = $baseSlug;
        $suffix = 2;

        while (Property::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    private function buildFacts(array $validated): array
    {
        $facts = [
            ['Voyageurs', (int) $validated['max_guests'].' voyageurs'],
        ];

        if (! empty($validated['bedrooms_count'])) {
            $facts[] = ['Chambres', (int) $validated['bedrooms_count'].' chambres'];
        }

        if (! empty($validated['bathrooms_count'])) {
            $facts[] = ['Salles de bain', (int) $validated['bathrooms_count'].' salles de bain'];
        }

        if (! empty($validated['city'])) {
            $facts[] = ['Ville', $validated['city']];
        }

        return $facts;
    }

    /**
     * @return array<int, string>
     */
    private function extractList(string $value, int $limit = 12): array
    {
        return Str::of($value)
            ->replace("\r", '')
            ->explode("\n")
            ->flatMap(fn (string $line) => preg_split('/\s*,\s*/', trim($line)) ?: [])
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->unique()
            ->take($limit)
            ->values()
            ->all();
    }

    private function galleryUrlsFromPayload(array $validated): Collection
    {
        return collect([
            $validated['gallery_image_1'] ?? null,
            $validated['gallery_image_2'] ?? null,
            $validated['gallery_image_3'] ?? null,
        ])->filter(fn (?string $url) => filled($url))->values();
    }

    private function storeUploadedPhoto(?UploadedFile $file, string $prefix): ?string
    {
        if (! $file) {
            return null;
        }

        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';
        $path = Storage::disk('property_uploads')->putFileAs(
            'properties/'.now()->format('Y/m'),
            $file,
            $prefix.'-'.Str::uuid().'.'.$extension
        );

        if (! $path) {
            return null;
        }

        return '/uploads/'.$path;
    }

    /**
     * @param  array<int, UploadedFile>|UploadedFile|null  $files
     */
    private function storeUploadedGalleryPhotos(array|UploadedFile|null $files): Collection
    {
        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        return collect($files ?? [])
            ->filter(fn ($file) => $file instanceof UploadedFile)
            ->map(fn (UploadedFile $file, int $index) => $this->storeUploadedPhoto($file, 'gallery-'.($index + 1)))
            ->filter(fn (?string $url) => filled($url))
            ->values();
    }

    /**
     * @return array{badge_color: string, gradient: string}
     */
    private function styleForType(string $type): array
    {
        return match (Str::lower($type)) {
            'villa' => [
                'badge_color' => 'rgba(34, 122, 104, 0.85)',
                'gradient' => 'linear-gradient(135deg, rgba(34, 122, 104, 0.9), rgba(18, 70, 59, 0.92))',
            ],
            'appartement' => [
                'badge_color' => 'rgba(71, 76, 168, 0.85)',
                'gradient' => 'linear-gradient(135deg, rgba(71, 76, 168, 0.9), rgba(36, 40, 94, 0.92))',
            ],
            'maison' => [
                'badge_color' => 'rgba(161, 112, 48, 0.85)',
                'gradient' => 'linear-gradient(135deg, rgba(161, 112, 48, 0.9), rgba(104, 67, 24, 0.92))',
            ],
            'camp' => [
                'badge_color' => 'rgba(145, 92, 58, 0.85)',
                'gradient' => 'linear-gradient(135deg, rgba(145, 92, 58, 0.9), rgba(85, 50, 29, 0.92))',
            ],
            default => [
                'badge_color' => 'rgba(196, 98, 45, 0.85)',
                'gradient' => 'linear-gradient(135deg, rgba(196, 98, 45, 0.9), rgba(97, 42, 18, 0.92))',
            ],
        };
    }

    private function normalizeType(string $type): string
    {
        return match (Str::lower($type)) {
            'villa' => 'Villa',
            'appartement' => 'Appartement',
            'maison' => 'Maison',
            'camp' => 'Camp',
            default => 'Riad',
        };
    }
}
