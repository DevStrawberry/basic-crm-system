<?php

namespace App\Services;

use App\Models\Contract;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ContractPdfService
{
    /**
     * Gera um PDF do contrato
     * 
     * @param Contract $contract
     * @return string|null Caminho do arquivo PDF gerado
     */
    public function generatePdf(Contract $contract): ?string
    {
        try {
            // Carrega os relacionamentos necessários
            $contract->load(['lead.client', 'proposal', 'assignedTo']);

            // Simulação de geração de PDF
            // Em produção, aqui seria usado uma biblioteca como DomPDF, TCPDF, ou wkhtmltopdf
            
            $content = $this->buildPdfContent($contract);
            
            // Define o caminho onde o PDF será salvo
            $filename = 'contracts/contract_' . $contract->id . '_' . time() . '.pdf';
            $filePath = storage_path('app/public/' . $filename);
            
            // Cria o diretório se não existir
            $directory = dirname($filePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Simulação: em produção, aqui seria gerado o PDF real
            // Por enquanto, apenas criamos um arquivo de texto simulando o PDF
            file_put_contents($filePath, $content);

            Log::info('PDF do contrato gerado com sucesso', [
                'contract_id' => $contract->id,
                'file_path' => $filename
            ]);

            return $filename;
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF do contrato', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Constrói o conteúdo do PDF (simulação)
     * 
     * @param Contract $contract
     * @return string
     */
    private function buildPdfContent(Contract $contract): string
    {
        $client = $contract->lead->client;
        $proposal = $contract->proposal;
        $assessor = $contract->assignedTo;

        $content = "CONTRATO #{$contract->contract_number}\n\n";
        $content .= "========================================\n\n";
        $content .= "CLIENTE:\n";
        $content .= "Nome: {$client->name}\n";
        $content .= "CPF: {$client->cpf}\n";
        $content .= "Email: {$client->email}\n";
        $content .= "Telefone: {$client->phone}\n\n";
        $content .= "========================================\n\n";
        $content .= "PROPOSTA:\n";
        $content .= "Título: {$proposal->title}\n";
        $content .= "Descrição: {$proposal->service_description}\n\n";
        $content .= "========================================\n\n";
        $content .= "VALORES:\n";
        $content .= "Valor Final: R$ " . number_format($contract->final_value, 2, ',', '.') . "\n";
        $content .= "Método de Pagamento: {$contract->payment_method}\n\n";
        $content .= "========================================\n\n";
        $content .= "PRAZO:\n";
        $content .= "Data Limite: " . ($contract->deadline ? $contract->deadline->format('d/m/Y') : 'Não definido') . "\n\n";
        $content .= "========================================\n\n";
        $content .= "ASSESSOR RESPONSÁVEL:\n";
        $content .= "Nome: " . ($assessor ? $assessor->name : 'Não atribuído') . "\n\n";
        $content .= "========================================\n\n";
        $content .= "STATUS: {$contract->status}\n\n";
        $content .= "========================================\n\n";
        $content .= "NOTAS:\n";
        $content .= ($contract->notes ?: 'Nenhuma nota registrada') . "\n\n";
        $content .= "========================================\n\n";
        $content .= "Data de Criação: " . $contract->created_at->format('d/m/Y H:i:s') . "\n";
        
        if ($contract->signed_at) {
            $content .= "Data de Assinatura: " . $contract->signed_at->format('d/m/Y H:i:s') . "\n";
        }

        return $content;
    }
}

