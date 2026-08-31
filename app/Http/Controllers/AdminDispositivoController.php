<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDispositivoController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = DB::table('usuarios')->orderBy('nome', 'asc')->get();

        $query = DB::table('dispositivos')
            ->leftJoin('usuarios', 'dispositivos.usuario_id', '=', 'usuarios.id')
            ->select('dispositivos.*', 'usuarios.nome as dono_nome');

        // NOVO FILTRO: Filtra estritamente pelo ID do Usuário (Dono) selecionado
        if ($request->filled('usuario_filtro')) {
            $query->where('dispositivos.usuario_id', $request->usuario_filtro);
        }

        $dispositivos = $query->orderBy('dispositivos.id', 'desc')->get();

        $editDevice = null;
        $editLimits = null;
        if ($request->has('edit')) {
            $editDevice = DB::table('dispositivos')->where('id', $request->edit)->first();
            $editLimits = DB::table('limites')->where('dispositivo_id', $request->edit)->first();
        }

        // Garante que aponta para o nome correto do seu arquivo de view
        return view('admin.admin_criar_dispositivo', compact('dispositivos', 'usuarios', 'editDevice', 'editLimits'));
    }

    public function salvar(Request $request)
    {
        $dadosBomba = [
            'nome'        => $request->nome,
            'tipo'        => $request->tipo,
            'modelo'      => $request->modelo,
            'status'      => $request->status,
            'localizacao' => $request->localizacao,
            'usuario_id'  => $request->usuario_id,
        ];

        // Mapeamento exato conectado com o formulário e com o banco de dados
        $dadosLimites = [
            'corrente_alerta'         => $request->corrente_alerta ?? 7.150,
            'corrente_desligar_alta'  => $request->corrente_desligar_alta ?? 8.000,
            'corrente_desligar_baixa' => $request->corrente_desligar_baixa ?? 3.250,
            'tensao_alerta_baixa'     => $request->tensao_alerta_baixa ?? 204.600,
            'tensao_alerta_alta'      => $request->tensao_alerta_alta ?? 235.400,
            'tensao_desligar_baixa'   => $request->tensao_desligar_baixa ?? 198.000,
            'tensao_desligar_alta'    => $request->tensao_desligar_alta ?? 242.000,
            'vibracao_alerta'         => $request->vibracao_alerta ?? 6.600,
            'vibracao_desligar'       => $request->vibracao_desligar ?? 8.300,
            'temperatura_alerta'      => $request->temperatura_alerta ?? 124.000,
            'temperatura_desligar'    => $request->temperatura_desligar ?? 155.000,
            'fluxo_alerta_baixo'      => $request->fluxo_alerta_baixo ?? 30.000,
            'fluxo_desligar_zero'     => 0.000, // Padrão imutável exigido
        ];

        if ($request->filled('id')) {
            $id = $request->id;
            DB::table('dispositivos')->where('id', $id)->update($dadosBomba);
            DB::table('limites')->where('dispositivo_id', $id)->update($dadosLimites);

            return redirect()->route('admin.dispositivos.criar')->with('success', 'Bomba e limites atualizados!');
        } else {
            $dadosBomba['data_instalacao'] = date('Y-m-d');
            $novoId = DB::table('dispositivos')->insertGetId($dadosBomba);
            
            $dadosLimites['dispositivo_id'] = $novoId;
            DB::table('limites')->insert($dadosLimites);

            return redirect()->route('admin.dispositivos.criar')->with('success', 'Nova motobomba cadastrada!');
        }
    }

    public function excluir($id)
    {
        // Exclui os limites associados primeiro para evitar quebra de chave estrangeira (se houver)
        DB::table('limites')->where('dispositivo_id', $id)->delete();
        DB::table('dispositivos')->where('id', $id)->delete();
        
        return redirect()->route('admin.dispositivos.criar')->with('success', 'Bomba removida!');
    }
}