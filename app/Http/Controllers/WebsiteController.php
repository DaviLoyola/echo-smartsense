<?php

namespace App\Http\Controllers; 

use App\Http\Controllers; 
use App\Models\Dispositivo;
use Illuminate\Http\Request;

class WebsiteController extends Controller{
    public function home(){
        return view('websites.home');
    }

    public function login(){
        return view('websites.login');
    }

    public function dispositivos(){
        // 1. Pega o usuário logado
        $usuario = auth()->user();

        // 2. Busca APENAS os dispositivos vinculados a este usuário com a última leitura
        $dispositivos = $usuario->dispositivos()->with('ultimaLeitura')->get();

        // Contadores calculados baseados estritamente nos dispositivos dele
        $totalDispositivos = $dispositivos->count();
        $ativos = $dispositivos->where('status', 'active')->count();
        $manutencao = $dispositivos->where('status', 'maintenance')->count();
        $erros = $dispositivos->where('status', 'error')->count();

        // Manda para a sua view original
        return view('websites.dispositivos', compact('dispositivos', 'totalDispositivos', 'ativos', 'manutencao', 'erros'));
    }

    public function config(){
        return view('websites.config');
    }

    public function relatorios(Request $request) 
    {
        $usuario = auth()->user();

        // 1. Guarda em um array todos os IDs de dispositivos que pertencem a esse usuário
        $meusIdsDispositivos = $usuario->dispositivos()->pluck('id')->toArray();

        // 2. Alimenta os checkboxes da tela APENAS com as motobombas dele
        $todosDispositivos = $usuario->dispositivos;

        // 3. Inicia a busca das leituras amarrada apenas aos IDs permitidos do usuário
        $query = \App\Models\LeituraSensor::whereIn('dispositivo_id', $meusIdsDispositivos)
            ->with('dispositivo')
            ->orderBy('momento_leitura', 'desc');

        // 4. Aplica o filtro de Data Inicial e Final
        if ($request->filled('data_inicio')) {
            $query->whereDate('momento_leitura', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('momento_leitura', '<=', $request->data_fim);
        }

        // 5. Aplica o filtro de Dispositivos Selecionados (Checkbox)
        if ($request->filled('dispositivos_selecionados')) {
            // SEGURANÇA: array_intersect garante que, mesmo se injetarem um ID de fora no HTML, o sistema remove antes de ir pro banco
            $filtradosSeguros = array_intersect($request->dispositivos_selecionados, $meusIdsDispositivos);
            $query->whereIn('dispositivo_id', $filtradosSeguros);
        }

        // 6. Executa a busca limitada
        $leituras = $query->limit(1000)->get();

        // 7. Define as colunas (mantendo seu padrão)
        $colunas = $request->input('colunas', [
            'tensao_v', 'corrente_a', 'potencia_kw', 'vibracao', 
            'fluxo_agua', 'volume_total', 'temperatura_motor'
        ]);

        return view('websites.relatorios', compact('leituras', 'todosDispositivos', 'colunas'));
    }
}