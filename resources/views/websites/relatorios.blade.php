@extends('websites.index')
@section('assunto')

<main class="main-content">
    <header class="topbar">
        <div>
            <h2>Relatórios de Telemetria Bruta</h2>
            <p class="extracao-color">Extração de dados dos sensores</p>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <button class="btn" style="background:none; font-size: 20px;" onclick="toggleTheme()" id="themeBtn">💡</button>
            <div class="user-profile">
                <span style="font-weight: 600; font-size: 14px; color: var(--text-main);">{{ Auth::user()->nome ?? 'Usuário' }}</span>
                <button class="btn btn-logout" onclick="logout()">Sair</button>
            </div>
        </div>
    </header>

    <div id="relatorios-content" style="padding: 20px;">
        
        <div style="background: var(--bg-card); padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid var(--border-color, #e5e7eb);">
            <form method="GET" action="{{ url('/relatorios') }}">
                
                <div style="display: flex; gap: 15px; align-items: flex-end; margin-bottom: 20px;">
                    <div>
                        <label style="font-size: 12px; font-weight: bold; color: var(--text-main);">Data Inicial</label><br>
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" style="padding: 8px; border: 1px solid var(--border-color, #ccc); border-radius: 4px; background: var(--bg); color: var(--text-main);">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: bold; color: var(--text-main);">Data Final</label><br>
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}" style="padding: 8px; border: 1px solid var(--border-color, #ccc); border-radius: 4px; background: var(--bg); color: var(--text-main);">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-size: 12px; font-weight: bold; display: block; margin-bottom: 8px; color: var(--text-main);">Quais Motobombas incluir?</label>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; background: var(--bg); padding: 10px; border-radius: 6px; border: 1px solid var(--border-color, #e5e7eb);">
                        @foreach($todosDispositivos as $disp)
                            <label style="font-size: 13px; cursor: pointer; color: var(--text-main); display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="dispositivos_selecionados[]" value="{{ $disp->id }}" 
                                {{ in_array($disp->id, request('dispositivos_selecionados', [])) ? 'checked' : '' }}>
                                {{ $disp->nome }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-size: 12px; font-weight: bold; display: block; margin-bottom: 8px; color: var(--text-main);">Quais leituras exibir?</label>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; background: var(--bg); padding: 10px; border-radius: 6px; border: 1px solid var(--border-color, #e5e7eb);">
                        @php
                            $todasColunas = [
                                'tensao_v' => 'Tensão (V)',
                                'corrente_a' => 'Corrente (A)',
                                'potencia_kw' => 'Potência (kW)',
                                'vibracao' => 'Vibração (mm/s)',
                                'fluxo_agua' => 'Fluxo (L/h)',
                                'volume_total' => 'Vol. Total (L)',
                                'temperatura_motor' => 'Temp. Motor (°C)'
                            ];
                        @endphp

                        @foreach($todasColunas as $key => $label)
                            <label style="font-size: 13px; cursor: pointer; color: var(--text-main); display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="colunas[]" value="{{ $key }}"
                                {{ in_array($key, $colunas) ? 'checked' : '' }}>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div style="display: flex; gap: 10px; border-top: 1px solid var(--border-color, #e5e7eb); padding-top: 15px;">
                    <button type="submit" class="btn" style="background: #059669; color: white; padding: 9px 20px; border: none; border-radius: 4px; cursor: pointer;">Gerar Relatório</button>
                    <a href="{{ url('/relatorios') }}" class="btn" style="background: var(--bg); color: var(--text-main); padding: 9px 20px; border-radius: 4px; text-decoration: none; border: 1px solid var(--border-color, #ccc);">Limpar Tudo</a>
                    
                    <button type="button" class="btn" onclick="exportarParaExcel()" style="margin-left: auto; background: #217346; color: white; padding: 9px 20px; border: none; border-radius: 4px; cursor: pointer;">
                        Exportar Tabela para Excel
                    </button>
                </div>
            </form>
        </div>

        <div style="background: var(--bg-card); border-radius: 8px; overflow-x: auto; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid var(--border-color, #e5e7eb);">
            <table id="tabela-bruta" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--bg); border-bottom: 2px solid var(--border-color, #e5e7eb);">
                        <th style="padding: 10px; font-size: 12px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">Data e Hora</th>
                        <th style="padding: 10px; font-size: 12px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">ID</th>
                        <th style="padding: 10px; font-size: 12px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">Motobomba</th>
                        
                        @foreach($todasColunas as $key => $label)
                            @if(in_array($key, $colunas))
                                <th style="padding: 10px; font-size: 12px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">{{ $label }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($leituras as $leitura)
                        <tr style="border-bottom: 1px solid var(--border-color, #f3f4f6);">
                            <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900)">{{ date('d/m/Y H:i:s', strtotime($leitura->momento_leitura)) }}</td>
                            <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900)">{{ $leitura->dispositivo_id }}</td>
                            <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900); font-weight: 500;">{{ $leitura->dispositivo->nome ?? 'Excluída' }}</td>
                            
                            @foreach($todasColunas as $key => $label)
                                @if(in_array($key, $colunas))
                                    <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">
                                        {{ number_format($leitura->$key, 2, ',', '.') }}
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="padding: 20px; text-align: center; color: var(--gray--900)">Nenhum dado encontrado com os filtros selecionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script src="/assets/javascript/script.min.js"></script>

@endsection