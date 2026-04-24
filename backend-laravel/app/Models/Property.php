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


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyFactory> */
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'slug',
        'type',
        'eyebrow',
        'name',
        'city',
        'location',
        'summary',
        'description',
        'story',
        'nightly_rate',
        'rating',
        'reviews_count',
        'max_guests',
        'bedrooms_count',
        'bathrooms_count',
        'thumbnail_image',
        'type_badge_color',
        'gradient',
        'facts',
        'amenities',
        'local_spots',
        'listing_tags',
        'map_label',
        'map_lat',
        'map_lng',
        'featured',
    ];

    protected $casts = [
        'facts' => 'array',
        'amenities' => 'array',
        'local_spots' => 'array',
        'listing_tags' => 'array',
        'featured' => 'boolean',
        'rating' => 'decimal:2',
        'map_lat' => 'decimal:7',
        'map_lng' => 'decimal:7',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class)->orderBy('position');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
