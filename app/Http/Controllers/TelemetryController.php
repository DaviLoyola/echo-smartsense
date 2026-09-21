<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dispositivo;
use App\Models\LeituraSensor;
use App\Models\EventoDecisao;
use App\Models\LimitesOperacionais;

class TelemetryController extends Controller
{
    // recebe o payload enriquecido do esp32 (adr #2)
    public function receberLeitura(Request $request)
    {
        // valida api_key e busca o dispositivo correspondente (adr #5)
        $dispositivo = Dispositivo::where('api_key', $request->input('api_key'))->first();

        if (!$dispositivo) {
            return response()->json([
                'status' => 'error',
                'message' => 'api_key invalida ou dispositivo nao encontrado'
            ], 401);
        }

        // valida os campos do payload de telemetria
        $dadosValidados = $request->validate([
            // campos da central (obrigatorios - sempre presentes)
            'leituras.corrente_fase_a' => 'required|numeric',
            'leituras.corrente_fase_b' => 'required|numeric',
            'leituras.corrente_fase_c' => 'required|numeric',
            'leituras.fluxo_agua'      => 'required|numeric',

            // modulo mecanico (opcionais - null se nao instalado)
            'leituras.vibracao'           => 'nullable|numeric',
            'leituras.temperatura_motor'  => 'nullable|numeric',

            // modulo eletrico (opcionais)
            'leituras.tensao_fase_a' => 'nullable|numeric',
            'leituras.tensao_fase_b' => 'nullable|numeric',
            'leituras.tensao_fase_c' => 'nullable|numeric',
            'leituras.status_dps'    => 'nullable|boolean',

            // modulo hidraulico (opcionais)
            'leituras.pressao_linear'      => 'nullable|numeric',
            'leituras.pressao_diferencial' => 'nullable|numeric',

            // metadados do evento (enviados pelo esp32 se houver alerta/corte)
            'status'            => 'required|in:normal,alerta,corte_emergencial,reconexao',
            'tipo_alerta'       => 'nullable|string|max:50',
            'acao_tomada'       => 'nullable|string|max:100',
            'motivo'            => 'nullable|string',
            'valor_limite'      => 'nullable|numeric',
        ]);

        $leituras = $dadosValidados['leituras'];

        // salva as leituras brutas na tabela leituras_sensores
        LeituraSensor::create([
            'dispositivo_id'      => $dispositivo->id,
            'corrente_fase_a'     => $leituras['corrente_fase_a'],
            'corrente_fase_b'     => $leituras['corrente_fase_b'],
            'corrente_fase_c'     => $leituras['corrente_fase_c'],
            'fluxo_agua'          => $leituras['fluxo_agua'],
            'vibracao'            => $leituras['vibracao'] ?? null,
            'temperatura_motor'   => $leituras['temperatura_motor'] ?? null,
            'tensao_fase_a'       => $leituras['tensao_fase_a'] ?? null,
            'tensao_fase_b'       => $leituras['tensao_fase_b'] ?? null,
            'tensao_fase_c'       => $leituras['tensao_fase_c'] ?? null,
            'status_dps'          => $leituras['status_dps'] ?? null,
            'pressao_linear'      => $leituras['pressao_linear'] ?? null,
            'pressao_diferencial' => $leituras['pressao_diferencial'] ?? null,
            'momento_leitura'     => now(),
        ]);

        // se o esp32 reportou alerta ou corte, registra na tabela de auditoria (adr #2)
        if (in_array($dadosValidados['status'], ['alerta', 'corte_emergencial', 'reconexao'])) {
            EventoDecisao::create([
                'dispositivo_id'    => $dispositivo->id,
                'tipo_evento'       => $dadosValidados['status'],
                'grandeza_acionada' => $dadosValidados['tipo_alerta'] ?? 'nao_informado',
                'valor_medido'      => $leituras[$dadosValidados['tipo_alerta']] ?? 0,
                'valor_limite'      => $dadosValidados['valor_limite'] ?? 0,
                'acao_tomada'       => $dadosValidados['acao_tomada'] ?? 'nenhuma',
                'motivo'            => $dadosValidados['motivo'] ?? null,
                'momento_evento'    => now(),
            ]);
        }

        // atualiza o timestamp de ultima comunicacao do dispositivo (adr #8)
        $dispositivo->update(['ultima_comunicacao' => now()]);

        return response()->json([
            'status'  => 'success',
            'message' => 'telemetria registrada'
        ], 200);
    }

    // retorna os limites operacionais para o esp32 calibrar sua logica local
    public function obterLimites($id)
    {
        $dispositivo = Dispositivo::find($id);

        if (!$dispositivo) {
            return response()->json([
                'status' => 'error',
                'message' => 'dispositivo nao encontrado'
            ], 404);
        }

        $limites = LimitesOperacionais::where('dispositivo_id', $id)->first();

        if (!$limites) {
            return response()->json([
                'status' => 'error',
                'message' => 'limites nao configurados para este dispositivo'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'limites' => $limites
        ], 200);
    }

    // retorna as ultimas 15 leituras e limites para o polling ajax do dashboard (adr #6)
    public function obterDadosRealTime($id)
    {
        $dispositivo = Dispositivo::find($id);

        if (!$dispositivo) {
            return response()->json(['status' => 'error', 'message' => 'dispositivo nao encontrado'], 404);
        }

        // busca as 15 leituras mais recentes e inverte para o grafico
        $leituras = LeituraSensor::where('dispositivo_id', $id)
            ->orderBy('momento_leitura', 'desc')
            ->take(15)
            ->get()
            ->reverse()
            ->values();

        // busca os limites configurados para esta bomba
        $limites = LimitesOperacionais::where('dispositivo_id', $id)->first();

        // calcula o status de comunicacao (adr #8)
        $timeout = 3; // minutos
        $online = $dispositivo->ultima_comunicacao &&
                  now()->diffInMinutes($dispositivo->ultima_comunicacao) <= $timeout;

        return response()->json([
            'status'        => 'success',
            'online'        => $online,
            'ultima_com'    => $dispositivo->ultima_comunicacao,
            'leituras'      => $leituras,
            'limites'       => $limites,
        ], 200);
    }

    // retorna o status geral de todos os dispositivos para o painel
    public function obterStatusGeral()
    {
        $dispositivos = Dispositivo::with('ultimaLeitura')->get();

        $timeout = 3; // minutos

        // monta o array com o status de cada bomba
        $dadosBomba = $dispositivos->map(function ($bomba) use ($timeout) {
            $online = $bomba->ultima_comunicacao &&
                      now()->diffInMinutes($bomba->ultima_comunicacao) <= $timeout;

            return [
                'id'                 => $bomba->id,
                'nome'               => $bomba->nome,
                'tipo'               => $bomba->tipo,
                'status'             => $bomba->status,
                'online'             => $online,
                'ultima_comunicacao' => $bomba->ultima_comunicacao,
                'ultimaLeitura'      => $bomba->ultimaLeitura,
            ];
        });

        return response()->json([
            'total'        => $dispositivos->count(),
            'ativos'       => $dispositivos->where('status', 'active')->count(),
            'manutencao'   => $dispositivos->where('status', 'maintenance')->count(),
            'erros'        => $dispositivos->where('status', 'error')->count(),
            'dispositivos' => $dadosBomba,
        ], 200);
    }
}