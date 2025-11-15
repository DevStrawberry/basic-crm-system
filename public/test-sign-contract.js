/**
 * SCRIPT DE TESTE: Assinatura de Contrato
 * 
 * CORREÇÕES APLICADAS:
 * 1. ✅ move_lead_status: 'won' → 'client' (valor correto)
 * 2. ✅ Removido campo 'result' (não existe na validação)
 * 3. ✅ IDs dinâmicos (não hardcoded)
 * 
 * USO:
 * 1. Faça login no sistema
 * 2. Abra Console (F12)
 * 3. Cole e execute este script
 * 4. Substitua os valores conforme necessário
 */

(function() {
    'use strict';

    // ============================================
    // CONFIGURAÇÃO - SUBSTITUA ESTES VALORES
    // ============================================
    const CONFIG = {
        leadId: 1, // ID do Lead (use o ID do seeder)
        contractId: 1, // ID do Contrato (use o ID do seeder)
        signedBy: 'Cliente de Teste', // Nome do signatário
        moveLeadStatus: 'client', // 'client' (sucesso) ou 'lost' (perda)
    };

    // ============================================
    // OBTER TOKEN CSRF
    // ============================================
    function getCsrfToken() {
        // Tenta obter do meta tag
        const metaToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (metaToken) return metaToken;

        // Tenta obter do input hidden
        const inputToken = document.querySelector('input[name="_token"]')?.value;
        if (inputToken) return inputToken;

        // Tenta obter de qualquer formulário na página
        const formToken = document.querySelector('form input[name="_token"]')?.value;
        if (formToken) return formToken;

        return null;
    }

    // ============================================
    // VALIDAÇÃO
    // ============================================
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        console.error('❌ ERRO: Token CSRF não encontrado!');
        console.error('   → Faça login e acesse uma página do sistema primeiro');
        console.error('   → Ou substitua manualmente a constante csrfToken no script');
        return;
    }

    if (!CONFIG.signedBy || CONFIG.signedBy.trim() === '') {
        console.error('❌ ERRO: signedBy não pode estar vazio!');
        return;
    }

    if (CONFIG.moveLeadStatus && !['client', 'lost'].includes(CONFIG.moveLeadStatus)) {
        console.error('❌ ERRO: move_lead_status deve ser "client" ou "lost"');
        return;
    }

    // ============================================
    // CRIAR E ENVIAR FORMULÁRIO
    // ============================================
    const targetUrl = `/leads/${CONFIG.leadId}/contract/${CONFIG.contractId}/sign`;

    console.log('🚀 Iniciando teste de assinatura de contrato...');
    console.log('📋 Configuração:', CONFIG);
    console.log('🔗 URL:', targetUrl);

    // Criar formulário
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = targetUrl;
    form.style.display = 'none';

    // Função auxiliar para criar campos
    function createHiddenField(name, value) {
        const field = document.createElement('input');
        field.type = 'hidden';
        field.name = name;
        field.value = value;
        return field;
    }

    // Adicionar campos obrigatórios
    form.appendChild(createHiddenField('_token', csrfToken));
    form.appendChild(createHiddenField('signed_by', CONFIG.signedBy));

    // Adicionar move_lead_status apenas se fornecido
    if (CONFIG.moveLeadStatus) {
        form.appendChild(createHiddenField('move_lead_status', CONFIG.moveLeadStatus));
    }

    // Adicionar ao DOM e submeter
    document.body.appendChild(form);
    
    console.log('✅ Formulário criado e enviado!');
    console.log('📊 Parâmetros enviados:', {
        signed_by: CONFIG.signedBy,
        move_lead_status: CONFIG.moveLeadStatus || 'não informado'
    });
    console.log('🔍 Verifique o banco de dados para confirmar as alterações.');
    console.log('');
    console.log('📝 Valores esperados:');
    if (CONFIG.moveLeadStatus === 'client') {
        console.log('   ✅ contracts.status = "Assinado"');
        console.log('   ✅ leads.is_won = true');
        console.log('   ✅ leads.pipeline_stage_id = "Cliente Ativo"');
        console.log('   ✅ PDF gerado e email enviado');
    } else if (CONFIG.moveLeadStatus === 'lost') {
        console.log('   ✅ contracts.status = "Cancelado"');
        console.log('   ✅ leads.is_won = false');
        console.log('   ✅ leads.pipeline_stage_id = "Cliente Perdido"');
        console.log('   ❌ PDF/Email NÃO são gerados');
    }

    form.submit();
})();

