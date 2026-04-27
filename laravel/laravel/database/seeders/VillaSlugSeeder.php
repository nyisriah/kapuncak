<?php

namespace Database\Seeders;

use App\Models\Villa;
use Illuminate\Database\Seeder;

class VillaSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Villa::query()
            ->orderBy('id')
            ->chunkById(100, function ($villas) {
                foreach ($villas as $villa) {
                    $base = $villa->slug ? $villa->slug : $villa->name;
                    $slug = Villa::generateUniqueSlug($base, $villa->id);

                    if ($villa->slug !== $slug) {
                        $villa->slug = $slug;
                        $villa->saveQuietly();
                    }
                }
            });
    }
}
