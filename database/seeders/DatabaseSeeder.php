<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Free plan test user
        $free = User::factory()->create([
            'name'  => 'Free User',
            'email' => 'free@example.com',
        ]);
        $free->getOrCreateSubscription();

        // Pro plan test user
        $pro = User::factory()->create([
            'name'  => 'Pro User',
            'email' => 'pro@example.com',
        ]);
        $proSub = $pro->getOrCreateSubscription();
        $proSub->upgradeToPro();
    }
}
