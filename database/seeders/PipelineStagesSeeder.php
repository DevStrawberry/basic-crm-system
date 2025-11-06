<?php

namespace Database\Seeders;

use App\Models\PipelineStage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PipelineStagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PipelineStage::insert([
            ['name' => 'Contato', 'ordering' => 1],
            ['name' => 'Diagnóstico', 'ordering' => 2],
            ['name' => 'Proposta de Valor', 'ordering' => 3],
            ['name' => 'Assinatura de Contrato', 'ordering' => 4],
            ['name' => 'Cliente Ativo', 'ordering' => 5],
            ['name' => 'Cliente Perdido', 'ordering' => 5]
        ]);
    }
}
