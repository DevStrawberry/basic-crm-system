<?php

namespace Database\Seeders;

use App\Models\LostReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LostReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LostReason::insert([
            ['description' => 'Cliente optou por concorrente'],
            ['description' => 'Preço não competitivo'],
            ['description' => 'Sem resposta do cliente'],
            ['description' => 'Falta de orçamento'],
            ['description' => 'Produto ou serviço não atendeu às necessidades'],
            ['description' => 'Mudança de prioridade do cliente'],
            ['description' => 'Prazo de entrega inadequado'],
            ['description' => 'Problemas de comunicação'],
            ['description' => 'Cancelamento pelo cliente'],
            ['description' => 'Outro motivo'],
        ]);
    }
}
