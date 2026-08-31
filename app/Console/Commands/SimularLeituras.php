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
        // Busca as últimas 15 leituras da bomba específica
        $leituras = \App\Models\LeituraSensor::where('dispositivo_id', $id)
                        ->orderBy('momento_leitura', 'desc')
                        ->take(15)
                        ->get()
                        ->reverse()
                        ->values();

        // Busca os limites técnicos configurados para esta bomba
        $limites = DB::table('limites')->where('dispositivo_id', $id)->first();

        return response()->json([
            'status'   => 'success',
            'leituras' => $leituras,
            'limites'  => $limites
        ], 200);
    }

    // Retorna o status atualizado de todos os dispositivos e contadores
    public function obterStatusGeral()
    {
        $dispositivos = DB::table('dispositivos')->get();
        
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

    /**
     * SIMULAÇÃO TEMPORÁRIA – insere uma leitura aleatória a cada chamada
     * para a bomba de ID 5, dentro das faixas seguras.
     * Use via rota GET /simular-leitura
     */
    public function simular()
    {
        $dispositivoId = 5; // Altere se necessário

        // Gera valores aleatórios realistas (dentro dos limites de segurança)
        $dados = [
            'dispositivo_id'    => $dispositivoId,
            'corrente_a'        => rand(400, 750) / 100,   // 4.00 ~ 7.50 A
            'tensao_v'          => rand(2050, 2350) / 10,  // 205.0 ~ 235.0 V
            'potencia_kw'       => rand(200, 500) / 100,   // 2.00 ~ 5.00 kW
            'vibracao'          => rand(200, 600) / 100,   // 2.00 ~ 6.00 mm/s
            'fluxo_agua'        => rand(300, 800) / 10,    // 30.0 ~ 80.0 L/h
            'volume_total'      => rand(1000, 50000) / 100,// 10.00 ~ 500.00 L
            'temperatura_motor' => rand(400, 1200) / 10,   // 40.0 ~ 120.0 °C
            'momento_leitura'   => now(),
            'created_at'        => now()
        ];

        // Insere no banco
        DB::table('leituras_sensores')->insert($dados);

        // Atualiza a última comunicação do dispositivo
        DB::table('dispositivos')
            ->where('id', $dispositivoId)
            ->update(['ultima_comunicacao' => now()]);

        return response()->json(['status' => 'ok']);
    }
}