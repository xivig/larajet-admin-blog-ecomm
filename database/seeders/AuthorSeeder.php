<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Author::firstOrCreate(
                ['user_id' => $user->id],
                ['nickname' => fake()->firstName()]
            );
        }
    }
}
