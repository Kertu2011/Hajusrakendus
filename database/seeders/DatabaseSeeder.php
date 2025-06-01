<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Testing User',
            'email' => 'user@example.com',
        ]);

        /** @var Collection<int, User> $createdUsers */
        $createdUsers = User::factory(10)->create();

        $this->call([
            ProductSeeder::class, // Add this line
        ]);

        Pet::factory(20)->make()->each(function ($pet) use ($createdUsers) {
            $pet->user_id = $createdUsers->random()->id;
            $pet->save();
        });
    }
}
