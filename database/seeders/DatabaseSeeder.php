<?php

namespace Database\Seeders;

use App\Models\SocialNetwork;
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
        // Ordem de execução
        $this->call([
            RolesSeeder::class,
            ContractSourcesSeeder::class,
            LostReasonsSeeder::class,
            PipelineStagesSeeder::class,
            SocialNetworksSeeder::class,
            UsersSeeder::class
        ]);
    }
}
