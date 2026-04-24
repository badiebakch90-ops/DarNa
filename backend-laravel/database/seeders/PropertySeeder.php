<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalog = require __DIR__.'/data/properties.php';

        foreach ($catalog as $payload) {
            $photos = $payload['photos'] ?? [];
            unset($payload['photos']);

            $property = Property::query()->updateOrCreate(
                ['slug' => $payload['slug']],
                $payload
            );

            $property->photos()->delete();

            foreach ($photos as $index => $photo) {
                $property->photos()->create([
                    'label' => $photo['label'],
                    'image_url' => $photo['image_url'],
                    'position' => $index + 1,
                ]);
            }
        }
    }
}
