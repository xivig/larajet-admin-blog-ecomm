<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Administrators
        $this->call([
            AdminSeeder::class,
        ]);

        // 2. Seed Base Users
        User::factory(50)->create();

        // 3. Seed Dependent Entities
        $this->call([
            CategorySeeder::class,
            AuthorSeeder::class,
            TagSeeder::class,
            PictureSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
