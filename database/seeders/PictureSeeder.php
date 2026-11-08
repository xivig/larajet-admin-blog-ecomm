<?php

namespace Database\Seeders;

use App\Models\Picture;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PictureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Picture::factory(100)->create([
                'name' => fake()->word(),
                'link' => fake()->imageUrl($width = 1600, $height = 1088),
            ]);
    }
}
