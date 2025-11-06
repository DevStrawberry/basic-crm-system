<?php

namespace Database\Seeders;

use App\Models\SocialNetwork;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialNetworksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SocialNetwork::insert([
            ['name' => 'Facebook'],
            ['name' => 'Instagram'],
            ['name' => 'Twitter (X)'],
            ['name' => 'LinkedIn'],
            ['name' => 'TikTok'],
            ['name' => 'Outra'],
        ]);
    }
}
