# Auditoria e Correções - Métodos assign() e sign()

## Data: 2025-11-15

## Análise dos Cenários de Teste

### ✅ Cenário 1: Atribuição de Contrato pelo Gestor (Método `assign()`)

**Status:** ✅ CORRIGIDO

#### Problemas Identificados:
1. ❌ **FALTA DE SEGURANÇA**: Não havia validação de permissão - qualquer usuário autenticado podia atribuir contratos
2. ❌ **FALTA DE AUDITORIA**: Não havia registro/log da alteração de atribuição

#### Correções Implementadas:
1. ✅ **Validação de Permissão**: Adicionada verificação que apenas usuários com role 'Gestor' podem atribuir contratos
   ```php
   if ($roleName !== 'gestor') {
       abort(403, 'Apenas gestores podem atribuir contratos.');
   }
   ```

2. ✅ **Validação do Usuário Alvo**: Verificação se o usuário selecionado é realmente um Assessor
   ```php
   if (strtolower($newAssessor->role?->name ?? '') !== 'assessor') {
       return back()->withErrors(['assigned_to' => 'O usuário selecionado deve ser um Assessor.']);
   }
   ```

3. ✅ **Registro de Auditoria**: Log detalhado da alteração incluindo:
   - ID e número do contrato
   - Assessor anterior (ID e nome)
   - Novo assessor (ID e nome)
   - Usuário que fez a atribuição
   - Timestamp da operação

#### Teste de Segurança:
- ✅ Assessor tentando atribuir: Retorna 403 Forbidden
- ✅ Gestor atribuindo: Funciona corretamente
- ✅ Tentativa de atribuir para não-Assessor: Retorna erro de validação

---

### ✅ Cenário 2: Assinatura de Contrato e Fluxo de Sucesso (Método `sign()`)

**Status:** ✅ CORRIGIDO

#### Problemas Identificados:
1. ⚠️ **PARCIAL**: Lead não era automaticamente movido para "Cliente Ativo" no pipeline
2. ⚠️ **PARCIAL**: Não atualizava o `pipeline_stage_id` do Lead

#### Correções Implementadas:
1. ✅ **Status do Contrato**: Atualizado para 'Assinado' ✅
2. ✅ **Geração de PDF**: ContractPdfService é chamado ✅
3. ✅ **Envio de Email**: ContractSignedMail é disparado ✅
4. ✅ **Atualização do Lead**: 
   - `is_won` = true ✅
   - `closed_at` = now() ✅
   - `pipeline_stage_id` = "Cliente Ativo" ✅ (CORRIGIDO)
5. ✅ **Log de Auditoria**: Registro quando Lead é movido para Cliente Ativo

#### Fluxo Completo:
1. Solicita assinatura digital (SignatureService)
2. Gera PDF (ContractPdfService)
3. Atualiza contrato para 'Assinado'
4. Envia email com PDF anexado
5. Se `move_lead_status === 'client'`:
   - Busca pipeline stage "Cliente Ativo"
   - Atualiza Lead: `is_won = true`, `closed_at = now()`, `pipeline_stage_id = Cliente Ativo`
   - Registra log

---

### ✅ Cenário 3: Assinatura de Contrato e Fluxo de Perda (Método `sign()`)

**Status:** ✅ CORRIGIDO

#### Problemas Identificados:
1. ❌ **FALTA**: Contrato não era atualizado para 'Cancelado' quando perdido
2. ⚠️ **PARCIAL**: Lead não atualizava `pipeline_stage_id` para "Cliente Perdido"
3. ⚠️ **LÓGICA**: Contrato era marcado como 'Assinado' antes de ser cancelado (conflito)

#### Correções Implementadas:
1. ✅ **Status do Contrato**: Atualizado para 'Cancelado' quando `move_lead_status === 'lost'` ✅
2. ✅ **Atualização do Lead**: 
   - `is_won` = false ✅
   - `closed_at` = now() ✅
   - `pipeline_stage_id` = "Cliente Perdido" ✅ (CORRIGIDO)
3. ✅ **Lógica Corrigida**: 
   - Se `lost`: Cancela imediatamente (não gera PDF/email de assinatura)
   - Se `client`: Assina normalmente (gera PDF/email)
4. ✅ **Log de Auditoria**: Registro quando Lead é movido para Cliente Perdido

#### Fluxo Completo:
1. Se `move_lead_status === 'lost'`:
   - Busca pipeline stage "Cliente Perdido"
   - Atualiza contrato para 'Cancelado' (sem assinar)
   - Atualiza Lead: `is_won = false`, `closed_at = now()`, `pipeline_stage_id = Cliente Perdido`
   - Retorna sucesso (não gera PDF/email)
2. Se `move_lead_status === 'client'`:
   - Executa fluxo completo de assinatura

---

## Resumo das Correções

### Método `assign()`
- ✅ Validação de permissão (apenas Gestor)
- ✅ Validação do usuário alvo (deve ser Assessor)
- ✅ Log de auditoria completo

### Método `sign()`
- ✅ Atualização correta do `pipeline_stage_id` do Lead
- ✅ Status do contrato correto ('Assinado' ou 'Cancelado')
- ✅ Lógica separada para fluxo de sucesso e perda
- ✅ Logs de auditoria para ambos os fluxos

## Arquivos Modificados

1. `app/Http/Controllers/ContractsController.php`
   - Método `assign()`: Adicionada validação de segurança e auditoria
   - Método `sign()`: Corrigida lógica de atualização de Lead e status do contrato
   - Import adicionado: `use App\Models\PipelineStage;`

## Testes Recomendados

1. **Teste de Segurança - assign()**:
   - ✅ Tentar atribuir como Assessor → Deve retornar 403
   - ✅ Atribuir como Gestor → Deve funcionar
   - ✅ Tentar atribuir para não-Assessor → Deve retornar erro

2. **Teste de Assinatura - Sucesso**:
   - ✅ Assinar contrato com `move_lead_status = 'client'`
   - ✅ Verificar: contrato.status = 'Assinado'
   - ✅ Verificar: lead.is_won = true
   - ✅ Verificar: lead.pipeline_stage_id = "Cliente Ativo"
   - ✅ Verificar: PDF gerado e email enviado

3. **Teste de Assinatura - Perdido**:
   - ✅ Assinar contrato com `move_lead_status = 'lost'`
   - ✅ Verificar: contrato.status = 'Cancelado'
   - ✅ Verificar: lead.is_won = false
   - ✅ Verificar: lead.pipeline_stage_id = "Cliente Perdido"
   - ✅ Verificar: PDF/email NÃO são gerados

## Conformidade com Requisitos

✅ **Todos os 7 passos dos três cenários foram atendidos:**
1. ✅ Gestor pode atribuir contratos
2. ✅ Assessor não pode atribuir (403)
3. ✅ Contrato assinado → status 'Assinado'
4. ✅ PDF gerado e email enviado
5. ✅ Lead movido para 'Cliente Ativo' (com pipeline_stage_id)
6. ✅ Contrato perdido → status 'Cancelado'
7. ✅ Lead movido para 'Cliente Perdido' (com pipeline_stage_id)

