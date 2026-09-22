@extends('websites.index')
@section('assunto')

<main class="main-content">
    <header class="topbar">
        <div>
            <h2>Relatórios de Telemetria e Auditoria</h2>
            <p class="extracao-color">Extração de dados dos sensores e histórico de cortes</p>
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
                    <label style="font-size: 12px; font-weight: bold; display: block; margin-bottom: 8px; color: var(--text-main);">Quais leituras exibir? (Apenas Telemetria)</label>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; background: var(--bg); padding: 10px; border-radius: 6px; border: 1px solid var(--border-color, #e5e7eb);">
                        @php
                            $todasColunas = [
                                'corrente_fase_a' => 'Corrente A (A)',
                                'corrente_fase_b' => 'Corrente B (A)',
                                'corrente_fase_c' => 'Corrente C (A)',
                                'tensao_fase_a' => 'Tensão A (V)',
                                'tensao_fase_b' => 'Tensão B (V)',
                                'tensao_fase_c' => 'Tensão C (V)',
                                'fluxo_agua' => 'Fluxo (L/h)',
                                'vibracao' => 'Vibração (mm/s)',
                                'temperatura_motor' => 'Temp. Motor (°C)',
                                'status_dps' => 'Status DPS',
                                'pressao_linear' => 'Pressão Linear (mca)',
                                'pressao_diferencial' => 'Pressão Diferencial (mca)'
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
                </div>
            </form>
        </div>

        <!-- sistema de abas -->
        <div style="display: flex; gap: 10px; margin-bottom: 15px; border-bottom: 2px solid var(--border-color, #e5e7eb);">
            <button onclick="mudarAba('aba-telemetria')" id="btn-aba-telemetria" class="btn" style="background: var(--bg-card); color: #059669; font-weight: bold; border: 1px solid var(--border-color, #e5e7eb); border-bottom: 2px solid #059669; border-radius: 4px 4px 0 0; padding: 10px 20px; cursor: pointer; margin-bottom: -2px;">Telemetria Bruta</button>
            <button onclick="mudarAba('aba-auditoria')" id="btn-aba-auditoria" class="btn" style="background: transparent; color: var(--text-main); border: none; border-radius: 4px 4px 0 0; padding: 10px 20px; cursor: pointer;">Histórico de Auditoria e Cortes</button>
        </div>

        <!-- aba 1: telemetria -->
        <div id="aba-telemetria">
            <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                <button type="button" class="btn" onclick="exportarParaExcel('tabela-bruta', 'Telemetria')" style="background: #217346; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
                    Exportar Excel (Telemetria)
                </button>
            </div>
            <div style="background: var(--bg-card); border-radius: 8px; overflow-x: auto; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid var(--border-color, #e5e7eb);">
                <table id="tabela-bruta" style="width: 100%; text-align: left; border-collapse: collapse; white-space: nowrap;">
                    <thead>
                        <tr style="background: var(--bg); border-bottom: 2px solid var(--border-color, #e5e7eb);">
                            <th style="padding: 10px; font-size: 12px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900); position: sticky; left: 0; background: var(--bg); z-index: 2;">Data e Hora</th>
                            <th style="padding: 10px; font-size: 12px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900); position: sticky; left: 120px; background: var(--bg); z-index: 2;">Motobomba</th>
                            
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
                                <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900); position: sticky; left: 0; background: var(--bg-card); z-index: 1;">{{ date('d/m/Y H:i:s', strtotime($leitura->momento_leitura)) }}</td>
                                <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900); font-weight: 500; position: sticky; left: 120px; background: var(--bg-card); z-index: 1;">{{ $leitura->dispositivo->nome ?? 'Excluída' }} (ID: {{ $leitura->dispositivo_id }})</td>
                                
                                @foreach($todasColunas as $key => $label)
                                    @if(in_array($key, $colunas))
                                        <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">
                                            @if($key === 'status_dps')
                                                {{ $leitura->$key !== null ? ($leitura->$key ? 'Normal' : 'Surto') : '-' }}
                                            @else
                                                {{ $leitura->$key !== null ? number_format($leitura->$key, 2, ',', '.') : '-' }}
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($colunas) + 2 }}" style="padding: 20px; text-align: center; color: var(--gray--900)">Nenhum dado encontrado com os filtros selecionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- aba 2: auditoria e cortes -->
        <div id="aba-auditoria" style="display: none;">
            <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                <button type="button" class="btn" onclick="exportarParaExcel('tabela-auditoria', 'Auditoria')" style="background: #217346; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
                    Exportar Excel (Auditoria)
                </button>
            </div>
            <div style="background: var(--bg-card); border-radius: 8px; overflow-x: auto; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid var(--border-color, #e5e7eb);">
                <table id="tabela-auditoria" style="width: 100%; text-align: left; border-collapse: collapse; white-space: nowrap;">
                    <thead>
                        <tr style="background: var(--bg); border-bottom: 2px solid var(--border-color, #e5e7eb);">
                            <th style="padding: 10px; font-size: 12px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">Data e Hora</th>
                            <th style="padding: 10px; font-size: 12px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">Motobomba</th>
                            <th style="padding: 10px; font-size: 12px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">Tipo de Evento</th>
                            <th style="padding: 10px; font-size: 12px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">Ação Tomada</th>
                            <th style="padding: 10px; font-size: 12px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">Motivo / Grandeza</th>
                            <th style="padding: 10px; font-size: 12px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">Valores (Medido / Limite)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($eventos as $evento)
                            <tr style="border-bottom: 1px solid var(--border-color, #f3f4f6);">
                                <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">{{ date('d/m/Y H:i:s', strtotime($evento->momento_evento)) }}</td>
                                <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900); font-weight: 500;">{{ $evento->dispositivo->nome ?? 'Excluída' }} (ID: {{ $evento->dispositivo_id }})</td>
                                <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">
                                    @if($evento->tipo_evento === 'corte_emergencial')
                                        <span style="color: #ef4444; font-weight: bold;">Corte Emergencial</span>
                                    @elseif($evento->tipo_evento === 'alerta_operacional')
                                        <span style="color: #f59e0b; font-weight: bold;">Alerta Operacional</span>
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $evento->tipo_evento)) }}
                                    @endif
                                </td>
                                <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">{{ ucfirst($evento->acao_tomada) }}</td>
                                <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">
                                    {{ $evento->motivo }} 
                                    @if($evento->grandeza_acionada)
                                        <br><span style="font-size: 11px; color: #6b7280;">({{ $evento->grandeza_acionada }})</span>
                                    @endif
                                </td>
                                <td style="padding: 10px; font-size: 13px; border-right: 1px solid var(--border-color, #e5e7eb); color: var(--gray--900);">
                                    @if($evento->valor_medido !== null && $evento->valor_limite !== null)
                                        Medido: {{ $evento->valor_medido }} / Limite: {{ $evento->valor_limite }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 20px; text-align: center; color: var(--gray--900)">Nenhum evento de auditoria encontrado com os filtros selecionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script src="/assets/javascript/script.js"></script>
<script>
    // logica simples para mudanca de abas via js com persistencia
    function mudarAba(abaId) {
        document.getElementById('aba-telemetria').style.display = 'none';
        document.getElementById('aba-auditoria').style.display = 'none';
        
        document.getElementById('btn-aba-telemetria').style.background = 'transparent';
        document.getElementById('btn-aba-telemetria').style.border = 'none';
        document.getElementById('btn-aba-telemetria').style.color = 'var(--text-main)';
        
        document.getElementById('btn-aba-auditoria').style.background = 'transparent';
        document.getElementById('btn-aba-auditoria').style.border = 'none';
        document.getElementById('btn-aba-auditoria').style.color = 'var(--text-main)';
        
        document.getElementById(abaId).style.display = 'block';
        
        let btnAtivo = document.getElementById('btn-' + abaId);
        btnAtivo.style.background = 'var(--bg-card)';
        btnAtivo.style.border = '1px solid var(--border-color, #e5e7eb)';
        btnAtivo.style.borderBottom = '2px solid #059669';
        btnAtivo.style.color = '#059669';
        btnAtivo.style.marginBottom = '-2px';

        // salva a aba selecionada para nao perder ao recarregar com filtros
        localStorage.setItem('abaAtivaRelatorios', abaId);
    }

    // ao carregar a pagina, verifica se tem uma aba salva
    document.addEventListener('DOMContentLoaded', function() {
        let abaSalva = localStorage.getItem('abaAtivaRelatorios');
        if (abaSalva) {
            mudarAba(abaSalva);
        } else {
            mudarAba('aba-telemetria');
        }
    });
</script>

@endsection