<?php

namespace Database\Seeders;

use App\Models\ContactSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractSourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactSource::insert([
            ['description' => 'Site Institucional'],
            ['description' => 'Redes Sociais'],
            ['description' => 'Indicação de Cliente'],
            ['description' => 'E-mail de Marketing'],
            ['description' => 'Anúncios Online (Google/Facebook Ads)'],
            ['description' => 'Evento ou Feira'],
            ['description' => 'Telefone/Contato Direto'],
            ['description' => 'Chat Online (Whatsapp)'],
            ['description' => 'Outbound (Prospecção Ativa)'],
            ['description' => 'Outro']
        ]);
    }
}
