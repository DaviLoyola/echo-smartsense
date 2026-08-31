<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MonitoramentoService
{
    public function analisar($dispositivoId, array $dados)
    {
        // 1. Busca as configurações de limites específicos desta bomba
        $limites = DB::table('limites')->where('dispositivo_id', $dispositivoId)->first();
        
        if (!$limites) {
            return; // Se a bomba não tiver limites configurados, encerra aqui
        }

        // 2. Busca o estado atual do dispositivo e o proprietário
        $dispositivo = DB::table('dispositivos')->where('id', $dispositivoId)->first();
        $usuario = DB::table('usuarios')->where('id', $dispositivo->usuario_id)->first();

        // IDENTIFICAÇÃO DE ESTADO (LIGADA VS DESLIGADA)
        // Se a corrente for maior ou igual a 0.1 A, a bomba está consumindo energia (Ligada)
        $bombaFisicamenteLigada = $dados['corrente_a'] >= 0.1;
        $statusAtual = $dispositivo->status;

        // CONTROLE AUTOMÁTICO DE STATUS (ATIVO / INATIVO)
        if (!$bombaFisicamenteLigada) {
            // Se a bomba está desligada e estava marcada como 'active', muda automaticamente para 'inactive'
            if ($statusAtual === 'active') {
                DB::table('dispositivos')->where('id', $dispositivoId)->update([
                    'status' => 'inactive'
                ]);
            }
            
            // IMPORTANTE: Se a bomba está desligada de propósito, fluxo zero e corrente zero são normais.
            // Encerramos a função aqui para NÃO rodar os cálculos e NÃO desligar/alertar à toa.
            return;
        } else {
            // Se a bomba ligou (tem corrente) e estava marcada como 'inactive', muda para 'active'
            if ($statusAtual === 'inactive') {
                DB::table('dispositivos')->where('id', $dispositivoId)->update([
                    'status' => 'active'
                ]);
                $statusAtual = 'active'; // Atualiza a variável local para o restante da lógica
            }
        }

        // Se a bomba estiver em manutenção, ignoramos os bloqueios automáticos para permitir testes do técnico
        if ($statusAtual === 'maintenance') {
            return;
        }

        // TRAVA DE SEGURANÇA: Se a bomba já está em estado de erro, significa que o desligamento já foi acionado.
        // Evitamos reprocessar os limites e inundar o e-mail do usuário enquanto o motor para por completo.
        if ($statusAtual === 'error') {
            return;
        }

        // ----------------------------------------------------------------------
        // CÁLCULOS DE PROTEÇÃO (Só rodam se a bomba estiver LIGADA/ATIVA)
        // ----------------------------------------------------------------------
        $deveDesligar = false;
        $motivosDesligar = [];
        
        $deveAlertar = false;
        $motivosAlerta = [];

        // --- VALIDAÇÃO DE FLUXO DE ÁGUA (Trabalho a Seco) ---
        // Exigência: Só condena se a bomba estiver ligada E o fluxo for igual ou menor ao limite zero
        if ($dados['fluxo_agua'] <= $limites->fluxo_desligar_zero) {
            $deveDesligar = true;
            $motivosDesligar[] = "Bomba EM FUNCIONAMENTO, mas com fluxo de água zerado ({$dados['fluxo_agua']}). Risco iminente de queima por trabalho a seco!";
        } elseif ($dados['fluxo_agua'] < $limites->fluxo_alerta_baixo) {
            $deveAlertar = true;
            $motivosAlerta[] = "Fluxo de água abaixo da vazão recomendada ({$dados['fluxo_agua']}).";
        }

        // --- VALIDAÇÃO DE CORRENTE OPERACIONAL (A) ---
        // Se ela está ligada, a corrente deve respeitar as faixas mínima e máxima estabelecidas
        if ($dados['corrente_a'] > $limites->corrente_desligar_alta || $dados['corrente_a'] < $limites->corrente_desligar_baixa) {
            $deveDesligar = true;
            $motivosDesligar[] = "Corrente elétrica fora da faixa segura de operação ({$dados['corrente_a']} A).";
        } elseif ($dados['corrente_a'] > $limites->corrente_alerta) {
            $deveAlertar = true;
            $motivosAlerta[] = "Corrente operando em nível elevado de alerta ({$dados['corrente_a']} A).";
        }

        // --- VALIDAÇÃO DE TENSÃO (V) ---
        if ($dados['tensao_v'] > $limites->tensao_desligar_alta || $dados['tensao_v'] < $limites->tensao_desligar_baixa) {
            $deveDesligar = true;
            $motivosDesligar[] = "Tensão elétrica na rede em nível crítico ({$dados['tensao_v']} V).";
        } elseif ($dados['tensao_v'] > $limites->tensao_alerta_alta || $dados['tensao_v'] < $limites->tensao_alerta_baixa) {
            $deveAlertar = true;
            $motivosAlerta[] = "Tensão com oscilação em nível de alerta ({$dados['tensao_v']} V).";
        }

        // --- VALIDAÇÃO DE VIBRAÇÃO ---
        if ($dados['vibracao'] > $limites->vibracao_desligar) {
            $deveDesligar = true;
            $motivosDesligar[] = "Vibração mecânica excessiva detectada ({$dados['vibracao']}). Risco de quebra de eixo.";
        } elseif ($dados['vibracao'] > $limites->vibracao_alerta) {
            $deveAlertar = true;
            $motivosAlerta[] = "Vibração fora do comum em nível de alerta ({$dados['vibracao']}).";
        }

        // --- VALIDAÇÃO DE TEMPERATURA (°C) ---
        if ($dados['temperatura_motor'] > $limites->temperatura_desligar) {
            $deveDesligar = true;
            $motivosDesligar[] = "Superaquecimento crítico atingido no estator/motor ({$dados['temperatura_motor']} °C).";
        } elseif ($dados['temperatura_motor'] > $limites->temperatura_alerta) {
            $deveAlertar = true;
            $motivosAlerta[] = "Temperatura do motor elevada em nível de alerta ({$dados['temperatura_motor']} °C).";
        }


        // 3. PROCESSAMENTO E ENVIO DAS AÇÕES OPERACIONAIS
        
        // Se houver qualquer motivo crítico, a prioridade absoluta é desligar
        if ($deveDesligar) {
            DB::table('dispositivos')->where('id', $dispositivoId)->update([
                'critico_desligar' => 1,
                'status'           => 'error'
            ]);

            // Dispara e-mail se o dono tiver as notificações ativadas
            if ($usuario && $usuario->notificacoes === 'on') {
                $this->enviarNotificacaoEmail($usuario->email, $dispositivo->nome, 'DESLIGAMENTO CRÍTICO DE EMERGÊNCIA', $motivosDesligar);
            }
        } 
        // Caso não seja crítico, avalia se precisa disparar aviso preventivo por e-mail
        elseif ($deveAlertar) {
            if ($usuario && $usuario->notificacoes === 'on') {
                $this->enviarNotificacaoEmail($usuario->email, $dispositivo->nome, 'ALERTA PREVENTIVO', $motivosAlerta);
            }
        }
    }

    private function enviarNotificacaoEmail($emailCliente, $nomeBomba, $tipoEvento, array $detalhes)
    {
        $mensagemCorpo = implode('<br>- ', $detalhes);

        Mail::html("
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #e1e5e9; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #b42323; margin-top: 0;'>Central de Monitoramento Echo</h2>
                <p>Uma nova ocorrência foi registrada para a motobomba: <strong>{$nomeBomba}</strong>.</p>
                <p>Status do Evento: <span style='font-weight: bold; text-transform: uppercase; color: #b42323;'>{$tipoEvento}</span></p>
                <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                <p><strong>Análise dos Sensores (Telemetria):</strong></p>
                <div style='color: #4b5563; background: #f9fafb; padding: 15px; border-left: 4px solid #b42323; margin-bottom: 20px;'>
                    - {$mensagemCorpo}
                </div>
                <p>O painel web foi atualizado. Caso tenha ocorrido um desligamento emergencial, inspecione o equipamento fisicamente antes de rearmar o sistema.</p>
            </div>
        ", function ($message) use ($emailCliente, $nomeBomba, $tipoEvento) {
            $message->to($emailCliente)
                    ->subject("ECHO [{$tipoEvento}] - Motobomba: {$nomeBomba}");
        });
    }
}