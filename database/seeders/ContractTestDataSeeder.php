<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ContactSource;
use App\Models\Contract;
use App\Models\Lead;
use App\Models\LostReason;
use App\Models\PipelineStage;
use App\Models\Proposal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ContractTestDataSeeder extends Seeder
{
    /**
     * Executa o seeder de dados de teste para o fluxo de Contratos.
     * Cria todas as dependências necessárias na ordem correta.
     * 
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            try {
                // ============================================
                // 1. ROLES (Dependência de Users)
                // ============================================
                $assessorRole = Role::firstOrCreate(
                    ['name' => 'Assessor'],
                    []
                );
                
                $gestorRole = Role::firstOrCreate(
                    ['name' => 'Gestor'],
                    []
                );

                $this->command->info("✓ Roles criados/encontrados: Assessor (ID: {$assessorRole->id}), Gestor (ID: {$gestorRole->id})");

                // ============================================
                // 2. PIPELINE STAGES (Dependência de Leads)
                // ============================================
                $prospeccaoStage = PipelineStage::firstOrCreate(
                    ['name' => 'Em Prospecção'],
                    ['ordering' => '1']
                );

                $clienteAtivoStage = PipelineStage::firstOrCreate(
                    ['name' => 'Cliente Ativo'],
                    ['ordering' => '5']
                );

                $this->command->info("✓ Pipeline Stages criados/encontrados: Em Prospecção (ID: {$prospeccaoStage->id}), Cliente Ativo (ID: {$clienteAtivoStage->id})");

                // ============================================
                // 3. CONTACT SOURCES (Dependência de Clients)
                // ============================================
                $contactSource = ContactSource::firstOrCreate(
                    ['description' => 'Teste - Seeder'],
                    []
                );

                $this->command->info("✓ Contact Source criado/encontrado (ID: {$contactSource->id})");

                // ============================================
                // 4. LOST REASONS (Dependência de Leads)
                // ============================================
                $lostReason = LostReason::firstOrCreate(
                    ['description' => 'Teste - Sem motivo específico'],
                    []
                );

                $this->command->info("✓ Lost Reason criado/encontrado (ID: {$lostReason->id})");

                // ============================================
                // 5. USERS (Dependência de Clients, Leads, Proposals, Contracts)
                // ============================================
                $assessorUser = User::firstOrCreate(
                    ['email' => 'assessor.teste@saas.com'],
                    [
                        'name' => 'Assessor de Teste',
                        'password' => Hash::make('password'),
                        'role_id' => $assessorRole->id,
                        'status' => 'active',
                        'must_change_password' => false,
                    ]
                );

                $gestorUser = User::firstOrCreate(
                    ['email' => 'gestor.teste@saas.com'],
                    [
                        'name' => 'Gestor de Teste',
                        'password' => Hash::make('password'),
                        'role_id' => $gestorRole->id,
                        'status' => 'active',
                        'must_change_password' => false,
                    ]
                );

                $this->command->info("✓ Users criados/encontrados:");
                $this->command->info("  - Assessor: {$assessorUser->name} (ID: {$assessorUser->id}, Email: {$assessorUser->email})");
                $this->command->info("  - Gestor: {$gestorUser->name} (ID: {$gestorUser->id}, Email: {$gestorUser->email})");

                // ============================================
                // 6. CLIENT (Dependência de Leads)
                // ============================================
                $client = Client::firstOrCreate(
                    ['cpf' => '12345678901'],
                    [
                        'name' => 'Cliente de Teste para Contrato',
                        'email' => 'cliente.teste@saas.com',
                        'phone' => '11999999999',
                        'address' => 'Rua de Teste, 123',
                        'city' => 'São Paulo',
                        'state' => 'SP',
                        'owner_user_id' => $assessorUser->id,
                        'contact_source_id' => $contactSource->id,
                    ]
                );

                $this->command->info("✓ Client criado/encontrado: {$client->name} (ID: {$client->id})");

                // ============================================
                // 7. LEAD (Dependência de Contracts e Proposals)
                // ============================================
                $lead = Lead::firstOrCreate(
                    ['title' => 'Lead para Teste de Fluxo de Contrato'],
                    [
                        'description' => 'Lead criado automaticamente pelo seeder para testes do fluxo de contratos (UC05).',
                        'estimated_value' => 5000.00,
                        'is_won' => false,
                        'client_id' => $client->id,
                        'owner_id' => $assessorUser->id,
                        'pipeline_stage_id' => $prospeccaoStage->id,
                        'lost_reason_id' => $lostReason->id,
                        'interest_levels' => 'Quente',
                    ]
                );

                $this->command->info("✓ Lead criado/encontrado: {$lead->title} (ID: {$lead->id})");

                // ============================================
                // 8. PROPOSAL (Dependência de Contracts)
                // ============================================
                $proposal = Proposal::firstOrCreate(
                    [
                        'lead_id' => $lead->id,
                        'title' => 'Proposta de Teste para Contrato',
                    ],
                    [
                        'created_by' => $assessorUser->id,
                        'service_description' => 'Serviço de teste criado pelo seeder para validação do fluxo de contratos.',
                        'warranties' => 'Garantia padrão de 12 meses.',
                        'total_value' => 5000.00,
                        'valid_until' => now()->addDays(30)->format('Y-m-d'),
                        'status' => 'Aceita',
                        'sent_at' => now()->subDays(5)->format('Y-m-d'),
                        'notes' => 'Proposta criada automaticamente pelo seeder.',
                    ]
                );

                $this->command->info("✓ Proposal criada/encontrada: {$proposal->title} (ID: {$proposal->id})");

                // ============================================
                // 9. CONTRACT (Objetivo principal do seeder)
                // ============================================
                $contract = Contract::firstOrCreate(
                    [
                        'lead_id' => $lead->id,
                        'contract_number' => 1001,
                    ],
                    [
                        'proposal_id' => $proposal->id,
                        'assigned_to' => $assessorUser->id,
                        'status' => 'Em elaboração',
                        'final_value' => 5000.00,
                        'payment_method' => 12.0, // Campo é float na migration (número de parcelas)
                        'deadline' => now()->addDays(30)->format('Y-m-d'),
                        'notes' => 'Contrato de teste gerado por seeder para UC05 - Gerenciar Contratos.',
                    ]
                );

                $this->command->info("✓ Contract criado/encontrado: #{$contract->contract_number} (ID: {$contract->id})");

                // ============================================
                // 10. SAÍDA FINAL
                // ============================================
                $this->command->newLine();
                $this->command->info('═══════════════════════════════════════════════════════════');
                $this->command->info('✅ DADOS DE TESTE DE CONTRATOS CRIADOS COM SUCESSO!');
                $this->command->info('═══════════════════════════════════════════════════════════');
                $this->command->newLine();
                $this->command->info('📋 INFORMAÇÕES PARA TESTES:');
                $this->command->newLine();
                $this->command->info("📄 CONTRATO DE TESTE:");
                $this->command->info("   ID: {$contract->id}");
                $this->command->info("   Número: #{$contract->contract_number}");
                $this->command->info("   Status: {$contract->status}");
                $this->command->info("   Lead ID: {$lead->id}");
                $this->command->newLine();
                $this->command->info("👤 ASSESSOR DE TESTE:");
                $this->command->info("   ID: {$assessorUser->id}");
                $this->command->info("   Nome: {$assessorUser->name}");
                $this->command->info("   Email: {$assessorUser->email}");
                $this->command->info("   Senha: password");
                $this->command->newLine();
                $this->command->info("👔 GESTOR DE TESTE:");
                $this->command->info("   ID: {$gestorUser->id}");
                $this->command->info("   Nome: {$gestorUser->name}");
                $this->command->info("   Email: {$gestorUser->email}");
                $this->command->info("   Senha: password");
                $this->command->newLine();
                $this->command->info("🔗 ROTAS PARA TESTES:");
                $this->command->info("   - Visualizar Contrato: /leads/{$lead->id}/contract/{$contract->id}");
                $this->command->info("   - Atribuir Contrato: POST /leads/{$lead->id}/contract/{$contract->id}/assign");
                $this->command->info("   - Assinar Contrato: POST /leads/{$lead->id}/contract/{$contract->id}/sign");
                $this->command->newLine();
                $this->command->info('═══════════════════════════════════════════════════════════');

            } catch (\Throwable $e) {
                $this->command->error('❌ ERRO na execução do Seeder:');
                $this->command->error("   Mensagem: {$e->getMessage()}");
                $this->command->error("   Arquivo: {$e->getFile()}");
                $this->command->error("   Linha: {$e->getLine()}");
                $this->command->newLine();
                $this->command->error('Stack Trace:');
                $this->command->error($e->getTraceAsString());
                
                // Re-lança a exceção para que o DB::transaction faça rollback
                throw $e;
            }
        });
    }
}
