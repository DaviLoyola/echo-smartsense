<?php

namespace App\Http\Controllers; 

use App\Models\Dispositivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebsiteController extends Controller{
    public function home(){
        return view('websites.home');
    }

    public function login(){
        return view('websites.login');
    }

    public function dispositivos(){
        // 1. pega o usuario logado
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

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
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        // 1. ids de dispositivos permitidos
        $meusIdsDispositivos = $usuario->dispositivos()->pluck('id')->toArray();

        // 2. alimenta os checkboxes
        $todosDispositivos = $usuario->dispositivos;

        // 3. inicia as queries amarradas aos ids do usuario
        $queryLeituras = \App\Models\LeituraSensor::whereIn('dispositivo_id', $meusIdsDispositivos)
            ->with('dispositivo')
            ->orderBy('momento_leitura', 'desc');

        $queryEventos = \App\Models\EventoDecisao::whereIn('dispositivo_id', $meusIdsDispositivos)
            ->with('dispositivo')
            ->orderBy('momento_evento', 'desc');

        // 4. aplica o filtro de data
        if ($request->filled('data_inicio')) {
            $queryLeituras->whereDate('momento_leitura', '>=', $request->data_inicio);
            $queryEventos->whereDate('momento_evento', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $queryLeituras->whereDate('momento_leitura', '<=', $request->data_fim);
            $queryEventos->whereDate('momento_evento', '<=', $request->data_fim);
        }

        // 5. aplica filtro de dispositivos
        if ($request->filled('dispositivos_selecionados')) {
            $filtradosSeguros = array_intersect($request->dispositivos_selecionados, $meusIdsDispositivos);
            $queryLeituras->whereIn('dispositivo_id', $filtradosSeguros);
            $queryEventos->whereIn('dispositivo_id', $filtradosSeguros);
        }

        // 6. executa as buscas limitadas
        $leituras = $queryLeituras->limit(1000)->get();
        $eventos = $queryEventos->limit(1000)->get();

        // 7. colunas selecionadas para aba 1
        $colunas = $request->input('colunas', [
            'corrente_fase_a', 'corrente_fase_b', 'corrente_fase_c',
            'tensao_fase_a', 'tensao_fase_b', 'tensao_fase_c',
            'fluxo_agua', 'vibracao', 'temperatura_motor',
            'pressao_linear', 'pressao_diferencial'
        ]);

        return view('websites.relatorios', compact('leituras', 'eventos', 'todosDispositivos', 'colunas'));
    }
}