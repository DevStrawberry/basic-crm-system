# 🧪 Script de Teste de Aceitação: Assinatura de Contrato

## 📋 Objetivo

Simular o envio de um POST para a rota `sign` do `ContractsController.php` com todos os parâmetros obrigatórios para testar o fluxo de assinatura de contrato.

## ⚠️ Correções Aplicadas

O script original tinha os seguintes problemas que foram corrigidos:

1. ❌ **Valor incorreto**: `move_lead_status = 'won'` → ✅ Corrigido para `'client'` ou `'lost'`
2. ❌ **Campo inexistente**: `result` não é usado no método `sign()` → ✅ Removido
3. ❌ **Rota hardcoded**: IDs fixos `/leads/1/contract/1` → ✅ Tornado dinâmico

## 🚀 Como Usar

### Opção 1: Script JavaScript para Console

1. **Faça login** no sistema (Gestor ou Assessor)
2. **Abra o DevTools** (F12) e vá para a aba **Console**
3. **Copie e cole** o script abaixo
4. **Substitua** os valores conforme necessário:
   - `leadId`: ID do Lead (use o ID do seeder)
   - `contractId`: ID do Contrato (use o ID do seeder)
   - `csrfToken`: Token CSRF da sessão atual
5. **Execute** o script

### Script Corrigido

```javascript
/*
 * SCRIPT DE TESTE: Assinatura de Contrato (Fluxo de SUCESSO)
 * Execute este script no Console do navegador (F12)
 */

// 🚨 SUBSTITUA O TOKEN CSRF COM O VALOR REAL DA SUA SESSÃO 🚨
const csrfToken = 'COLE_O_SEU_TOKEN_AQUI';

// --- DADOS DA AÇÃO ---
const leadId = 1; // SUBSTITUA com o ID real do Lead do seeder
const contractId = 1; // SUBSTITUA com o ID real do Contrato do seeder
const targetUrl = `/leads/${leadId}/contract/${contractId}/sign`;
const signedByValue = 'Cliente de Teste'; // Nome do signatário
const moveLeadStatus = 'client'; // 'client' para sucesso, 'lost' para perda

// 1. Cria um formulário temporário no DOM
const form = document.createElement('form');
form.method = 'POST';
form.action = targetUrl;

// Função auxiliar para criar campos ocultos
function createHiddenField(name, value) {
    const field = document.createElement('input');
    field.type = 'hidden';
    field.name = name;
    field.value = value;
    return field;
}

// 2. Adiciona os campos obrigatórios
form.appendChild(createHiddenField('_token', csrfToken));
form.appendChild(createHiddenField('signed_by', signedByValue));
form.appendChild(createHiddenField('move_lead_status', moveLeadStatus));

// 3. Envia o formulário
document.body.appendChild(form);
form.submit();

console.log('✅ POST enviado para:', targetUrl);
console.log('📋 Parâmetros:', { 
    signed_by: signedByValue, 
    move_lead_status: moveLeadStatus 
});
console.log('🔍 Verifique o banco de dados para confirmar as alterações.');
```

### Opção 2: Página HTML de Teste

Uma página HTML interativa foi criada em `public/test-contract-sign.html` que permite:

- ✅ Preencher os parâmetros via formulário
- ✅ Gerar o script automaticamente
- ✅ Copiar o script para a área de transferência
- ✅ Executar o teste diretamente (se estiver logado)

**Acesse:** `http://seu-dominio.local/test-contract-sign.html`

## 📊 Valores Esperados

### Para `move_lead_status = 'client'` (Sucesso):

| Campo | Valor Esperado |
|-------|----------------|
| `contracts.status` | `'Assinado'` |
| `contracts.signed_by` | Valor informado |
| `contracts.signed_at` | Data/hora atual |
| `leads.is_won` | `true` |
| `leads.pipeline_stage_id` | ID do stage "Cliente Ativo" |
| `leads.closed_at` | Data/hora atual |
| PDF | ✅ Gerado |
| Email | ✅ Enviado |

### Para `move_lead_status = 'lost'` (Perda):

| Campo | Valor Esperado |
|-------|----------------|
| `contracts.status` | `'Cancelado'` |
| `leads.is_won` | `false` |
| `leads.pipeline_stage_id` | ID do stage "Cliente Perdido" |
| `leads.closed_at` | Data/hora atual |
| PDF | ❌ NÃO gerado |
| Email | ❌ NÃO enviado |

## 🔍 Como Obter o Token CSRF

### Método 1: Via Meta Tag
```javascript
document.querySelector('meta[name="csrf-token"]').content
```

### Método 2: Via Input Hidden
```javascript
document.querySelector('input[name="_token"]').value
```

### Método 3: Via Formulário
1. Acesse qualquer página com formulário (ex: `/leads/1/contract/1/edit`)
2. Inspecione o elemento (F12)
3. Procure por `<input name="_token" value="...">`
4. Copie o valor

## ✅ Validação Pós-Teste

Após executar o script, verifique no banco de dados:

```sql
-- Verificar status do contrato
SELECT id, contract_number, status, signed_by, signed_at, assigned_to 
FROM contracts 
WHERE id = [CONTRACT_ID];

-- Verificar status do lead
SELECT id, title, is_won, pipeline_stage_id, closed_at 
FROM leads 
WHERE id = [LEAD_ID];

-- Verificar pipeline stage
SELECT ps.name, ps.id 
FROM pipeline_stages ps 
WHERE ps.name IN ('Cliente Ativo', 'Cliente Perdido');
```

## 🐛 Troubleshooting

### Erro: "Token CSRF não encontrado"
- ✅ Certifique-se de estar logado
- ✅ Acesse uma página do sistema antes de executar o script
- ✅ Verifique se o token está correto

### Erro: "403 Forbidden"
- ✅ Verifique se você tem permissão (Gestor ou Assessor)
- ✅ Verifique se o contrato existe
- ✅ Verifique se o contrato está atribuído a você (se for Assessor)

### Erro: "Validation failed"
- ✅ Verifique se `signed_by` não está vazio
- ✅ Verifique se `move_lead_status` é `'client'`, `'lost'` ou vazio
- ✅ Verifique se os IDs do Lead e Contrato estão corretos

## 📝 Notas Importantes

1. **Valores Corretos para `move_lead_status`**:
   - ✅ `'client'` - Move Lead para Cliente Ativo (sucesso)
   - ✅ `'lost'` - Move Lead para Cliente Perdido (falha)
   - ✅ `''` ou `null` - Não move o Lead

2. **Campo `result` foi removido**: Este campo não existe na validação do método `sign()` e não é necessário.

3. **Rota Correta**: A rota é `/leads/{lead_id}/contract/{contract_id}/sign` (POST)

4. **Dados do Seeder**: Use os IDs retornados pelo `ContractTestDataSeeder` para os testes.

