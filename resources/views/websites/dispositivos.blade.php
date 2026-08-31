@extends('websites.index')
@section('assunto')

<main class="main-content">
    <header class="topbar">
        <div>
            <h2 id="welcomeText">Olá, {{ Auth::user()->nome ?? 'Operador' }}</h2>
            <p class="monitoramento-color">Monitoramento e Proteção de Motobombas</p>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <button class="btn" style="background:none; font-size: 20px;" onclick="toggleTheme()" id="themeBtn">💡</button>
            <div class="user-profile">
                <span id="userName" style="font-weight: 600; font-size: 14px;">{{ Auth::user()->nome ?? 'Usuário' }}</span>
                <button class="btn btn-logout" onclick="logout()">Sair</button>
            </div>
        </div>
    </header>

    <div id="painel-content">
    
        <div style="margin-bottom: 20px; padding: 15px; background: rgba(45, 127, 249, 0.05); border: 1px solid #2d7ff9; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
    <div>
        <h4 style="margin: 0; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
            Conexão USB Direta
        </h4>
        <small style="color: var(--gray--900);">Transmita os dados do Arduino vinculando-o a uma motobomba específica.</small>
    </div>
    
    <div style="display: flex; gap: 10px; align-items: center;">
        <select id="selectDispositivoSerial" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; background: white; color: #334155; font-weight: 500; outline: none; cursor: pointer;">
            <option value="" disabled selected>1º Selecione a Motobomba...</option>
            @foreach($dispositivos as $bomba)
                <option value="{{ $bomba->id }}">{{ $bomba->nome }} (ID: {{ $bomba->id }})</option>
            @endforeach
        </select>

        <span id="serialStatus" style="font-size: 13px; font-weight: bold; color: #f59e0b;">Desconectado</span>
        
        <button id="btnConnectSerial" class="btn" style="background: #2d7ff9; color: white; padding: 8px 15px; border-radius: 6px; border: none; cursor: pointer; font-weight: bold;">
            2º Conectar USB
        </button>
    </div>
</div>

        <div class="stats-grid">
            <div class="stat-card">
                <div>
                    <small class="color-card" >Total de Motobombas</small>
                    <h3>{{ $totalDispositivos }}</h3>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <small class="color-card">Operando Normal</small>
                    <h3 style="color: #10b981;">{{ $ativos }}</h3>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <small class="color-card">Em Manutenção</small>
                    <h3 style="color: #f59e0b;">{{ $manutencao }}</h3>
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <small class="color-card">Críticos / Falhas</small>
                    <h3 style="color: #ef4444;">{{ $erros }}</h3>
                </div>
            </div>
        </div>

        <div class="devices-grid" style="margin-top: 20px; display: flex; flex-direction: column; gap: 20px;">
            @foreach($dispositivos as $bomba)
                @php
                    $statusColor = $bomba->status === 'error' ? '#ef4444' : ($bomba->status === 'maintenance' ? '#f59e0b' : '#10b981');
                    $leitura = $bomba->ultimaLeitura;
                @endphp

                <div class="sensor-card" style="border-top: 4px solid {{ $statusColor }}; position: relative; display: flex; flex-direction: column;">
                    
                    <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom: 15px;">
                        <div>
                            <h4 style="color:var(--text-main); font-size:1.1rem; font-weight:700; margin:0;">{{ $bomba->nome }}</h4>
                            <small style="color:var(--grey--900);">Mod: {{ $bomba->modelo ?? 'N/A' }}</small>
                        </div>
                    </div>

                    @if($leitura)
                        @php
                            $tempColor = $leitura->temperatura_motor > 70 ? '#ef4444' : ($leitura->temperatura_motor > 50 ? '#f59e0b' : 'var(--l-main)');
                        @endphp
                        <div class="sensor-value" style="color: {{ $tempColor }}; font-size: 2rem; font-weight: bold; margin-bottom: 15px;">
                            {{ number_format($leitura->temperatura_motor, 1, ',', '.') }} <small style="font-size:16px; color:var(--grey--900);">°C no Motor</small>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; font-size: 13px; color: var(--text-main); background: rgba(0,0,0,0.02); padding: 10px; border-radius: 6px;">
                            <div><strong>Tensão:</strong> {{ number_format($leitura->tensao_v, 1) }}V</div>
                            <div><strong>Corrente:</strong> {{ number_format($leitura->corrente_a, 1) }}A</div>
                            <div><strong>Potência:</strong> {{ number_format($leitura->potencia_kw, 2) }} kW</div>
                            <div><strong>Vibração:</strong> {{ number_format($leitura->vibracao, 2) }} mm/s</div>
                            <div><strong>Fluxo:</strong> {{ number_format($leitura->fluxo_agua, 1) }} L/h</div>
                            <div><strong>Vol. Total:</strong> {{ number_format($leitura->volume_total, 0, ',', '.') }} L</div>
                        </div>
                    @else
                        <div style="padding: 30px 0; text-align: center; color:var(--gray--900); font-style: italic;">
                            Aguardando telemetria...
                        </div>
                    @endif

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; padding-top:15px; border-top:1px solid var(--border);">
                        <span style="font-size:12px; color:var(--gray--900);">📍 {{ $bomba->localizacao ?? 'Sem local' }}</span>
                        
                        <span class="badge" style="background-color: {{ $statusColor }}20; color: {{ $statusColor }}; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                            ● {{ strtoupper($bomba->status) }}
                        </span>
                    </div>

                    <div style="margin-top:8px; font-size:11px; color:var(--gray--900); text-align:right;">
                        Leitura: {{ $leitura ? date('d/m H:i', strtotime($leitura->momento_leitura)) : 'N/A' }}
                    </div>

                    <div style="margin-top: auto; padding-top: 15px;">
                        <button class="btn" onclick="openChartModal({{ $bomba->id }}, '{{ addslashes($bomba->nome) }}')" style="width: 100%; background-color: #10b981; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: 600; display: flex; justify-content: center; align-items: center; gap: 8px;">
                            Ver Gráficos
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</main>

<div id="toast" class="toast" style="display:none;"></div>

<div id="chartModal" class="chart-modal-overlay">
    <div class="chart-modal-content">
        <div class="chart-modal-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="chartModalTitle">Gráfico de Linha em Tempo Real</h3>
            <button class="btn-close-chart" onclick="closeChartModal()">✖</button>
        </div>
        <div class="chart-modal-body" style="display: flex; flex-direction: column; gap: 15px;">
            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 10px;">
                <label for="metricSelect" style="font-weight: 600; color: var(--text-main); font-size: 14px;">Monitorar:</label>
                <select id="metricSelect" onchange="renderChart()" style="padding: 8px; border-radius: 6px; border: 1px solid var(--border); background: var(--bg); color: var(--text-main); font-size: 14px; cursor: pointer;">
                    <option value="temperatura_motor">Temperatura (°C)</option>
                    <option value="corrente_a">Corrente (A)</option>
                    <option value="tensao_v">Tensão (V)</option>
                    <option value="vibracao">Vibração (mm/s)</option>
                    <option value="fluxo_agua">Fluxo (L/h)</option>
                </select>
            </div>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="realTimeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script src="/assets/javascript/script.min.js"></script>

@endsection