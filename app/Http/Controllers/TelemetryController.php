<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\MonitoramentoService;

class TelemetryController extends Controller
{
    protected $monitoramentoService;

    // Injeta o nosso Service de Cálculos automaticamente
    public function __construct(MonitoramentoService $monitoramentoService)
    {
        $this->monitoramentoService = $monitoramentoService;
    }

    public function receberLeitura(Request $request)
    {
        // 1. Validação rápida dos dados recebidos do Hardware
        $dadosValidados = $request->validate([
            'dispositivo_id'    => 'required|integer|exists:dispositivos,id',
            'corrente_a'        => 'required|numeric',
            'tensao_v'          => 'required|numeric',
            'potencia_kw'       => 'required|numeric',
            'vibracao'          => 'required|numeric',
            'fluxo_agua'        => 'required|numeric',
            'volume_total'      => 'required|numeric',
            'temperatura_motor' => 'required|numeric',
        ]);

        // 2. Registra os dados recebidos na tabela de leituras
        DB::table('leituras_sensores')->insert([
            'dispositivo_id'    => $dadosValidados['dispositivo_id'],
            'corrente_a'        => $dadosValidados['corrente_a'],
            'tensao_v'          => $dadosValidados['tensao_v'],
            'potencia_kw'       => $dadosValidados['potencia_kw'],
            'vibracao'          => $dadosValidados['vibracao'],
            'fluxo_agua'        => $dadosValidados['fluxo_agua'],
            'volume_total'      => $dadosValidados['volume_total'],
            'temperatura_motor' => $dadosValidados['temperatura_motor'],
            'momento_leitura'   => now(),
            'created_at'        => now()
        ]);

        // 3. Atualiza o timestamp de última comunicação do hardware
        DB::table('dispositivos')->where('id', $dadosValidados['dispositivo_id'])->update([
            'ultima_comunicacao' => now()
        ]);

        // 4. Executa a inteligência de processamento de limites e envio de e-mails
        $this->monitoramentoService->analisar($dadosValidados['dispositivo_id'], $dadosValidados);

        // 5. Consulta o estado atualizado após a análise para mandar a instrução do relé
        $dispositivo = DB::table('dispositivos')->where('id', $dadosValidados['dispositivo_id'])->first();

        // Retorna o JSON direto para o Arduino ler e tomar a ação física na bomba
        return response()->json([
            'status'           => 'success',
            'critico_desligar' => (bool) $dispositivo->critico_desligar,
            'bomba_status'     => $dispositivo->status
        ], 200);
    }

    

    // Retorna os limites e as últimas 15 leituras de um dispositivo específico
    public function obterDadosRealTime($id)
    {
        // Busca as últimas 15 leituras da bomba específica, ordenadas da mais recente para a mais antiga, e reverte para o gráfico (da mais antiga para a mais recente)
        $leituras = \App\Models\LeituraSensor::where('dispositivo_id', $id)
                        ->orderBy('momento_leitura', 'desc')
                        ->take(15)
                        ->get()
                        ->reverse()
                        ->values(); 

        // Busca os limites técnicos configurados para esta bomba
        $limites = \Illuminate\Support\Facades\DB::table('limites')->where('dispositivo_id', $id)->first();

        // Retorna tudo em formato JSON para o AJAX
        return response()->json([
            'status' => 'success',
            'leituras' => $leituras,
            'limites' => $limites
        ], 200);
    }

    // Retorna o status atualizado de todos os dispositivos e contadores para sincronizar a tela
    public function obterStatusGeral()
    {
        $dispositivos = DB::table('dispositivos')->get();
        
        // Mapeia cada bomba trazendo sua respectiva última leitura atualizada
        $dadosBomba = $dispositivos->map(function($bomba) {
            $bomba->ultimaLeitura = DB::table('leituras_sensores')
                ->where('dispositivo_id', $bomba->id)
                ->orderBy('id', 'desc')
                ->first();
            return $bomba;
        });

        return response()->json([
            'total'       => $dispositivos->count(),
            'ativos'      => $dispositivos->where('status', 'active')->count(),
            'manutencao'  => $dispositivos->where('status', 'maintenance')->count(),
            'erros'       => $dispositivos->where('status', 'error')->count(),
            'dispositivos'=> $dadosBomba
        ], 200);
    }
}