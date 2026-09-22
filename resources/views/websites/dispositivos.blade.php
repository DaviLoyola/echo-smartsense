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
        <div class="stats-grid">
            <div class="stat-card">
                <div>
                    <small class="color-card">Total de Motobombas</small>
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
                    $leitura = $bomba->ultimaLeitura;

                    // logica de timeout (adr #8): 3 min sem comunicacao = offline
                    $online = $bomba->ultima_comunicacao && now()->diffInMinutes($bomba->ultima_comunicacao) <= 3;
                @endphp

                <div class="sensor-card {{ !$online ? 'sensor-offline sensor-card-offline-border' : 'sensor-card-online-'.$bomba->status }}" style="position: relative; display: flex; flex-direction: column;">

                    {{-- banner de offline exibido quando o timeout estoura --}}
                    @if(!$online)
                        <div class="offline-banner">
                            SEM COMUNICACAO — Ultima resposta: {{ $bomba->ultima_comunicacao ? \Carbon\Carbon::parse($bomba->ultima_comunicacao)->format('d/m H:i') : 'Nunca' }}
                        </div>
                    @endif

                    <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom: 15px;">
                        <div>
                            <h4 style="color:var(--text-main); font-size:1.1rem; font-weight:700; margin:0;">{{ $bomba->nome }}</h4>
                            <small style="color:var(--gray-500);">Mod: {{ $bomba->modelo ?? 'N/A' }}</small>
                        </div>
                        <span class="conn-badge {{ $online ? 'conn-online' : 'conn-offline' }}">
                            {{ $online ? 'Online' : 'Offline' }}
                        </span>
                    </div>

                    @if($leitura)
                        {{-- central basica: corrente trifasica e fluxo --}}
                        <div class="reading-section">
                            <h5 class="reading-section-title">Central Básica</h5>
                            <div class="reading-grid">
                                <div class="reading-item"><span class="reading-label">Corrente A</span><span class="reading-value">{{ number_format($leitura->corrente_fase_a, 2, ',', '.') }} A</span></div>
                                <div class="reading-item"><span class="reading-label">Corrente B</span><span class="reading-value">{{ number_format($leitura->corrente_fase_b, 2, ',', '.') }} A</span></div>
                                <div class="reading-item"><span class="reading-label">Corrente C</span><span class="reading-value">{{ number_format($leitura->corrente_fase_c, 2, ',', '.') }} A</span></div>
                                <div class="reading-item"><span class="reading-label">Fluxo</span><span class="reading-value">{{ number_format($leitura->fluxo_agua, 1, ',', '.') }} L/h</span></div>
                            </div>
                        </div>

                        {{-- modulo eletrico: tensao trifasica e dps --}}
                        <div class="reading-section {{ $leitura->tensao_fase_a === null && $leitura->status_dps === null ? 'section-disabled' : '' }}">
                            <h5 class="reading-section-title">Módulo Elétrico</h5>
                            <div class="reading-grid">
                                <div class="reading-item {{ $leitura->tensao_fase_a === null ? 'reading-disabled' : '' }}"><span class="reading-label">Tensão A</span><span class="reading-value">{{ $leitura->tensao_fase_a !== null ? number_format($leitura->tensao_fase_a, 1, ',', '.').' V' : 'N/I' }}</span></div>
                                <div class="reading-item {{ $leitura->tensao_fase_b === null ? 'reading-disabled' : '' }}"><span class="reading-label">Tensão B</span><span class="reading-value">{{ $leitura->tensao_fase_b !== null ? number_format($leitura->tensao_fase_b, 1, ',', '.').' V' : 'N/I' }}</span></div>
                                <div class="reading-item {{ $leitura->tensao_fase_c === null ? 'reading-disabled' : '' }}"><span class="reading-label">Tensão C</span><span class="reading-value">{{ $leitura->tensao_fase_c !== null ? number_format($leitura->tensao_fase_c, 1, ',', '.').' V' : 'N/I' }}</span></div>
                                <div class="reading-item {{ $leitura->status_dps === null ? 'reading-disabled' : '' }}"><span class="reading-label">DPS</span><span class="reading-value">{{ $leitura->status_dps !== null ? ($leitura->status_dps ? 'OK' : 'FALHA') : 'N/I' }}</span></div>
                            </div>
                        </div>

                        {{-- modulo mecanico: temperatura e vibracao --}}
                        <div class="reading-section {{ $leitura->temperatura_motor === null && $leitura->vibracao === null ? 'section-disabled' : '' }}">
                            <h5 class="reading-section-title">Módulo Mecânico</h5>
                            <div class="reading-grid">
                                <div class="reading-item {{ $leitura->temperatura_motor === null ? 'reading-disabled' : '' }}"><span class="reading-label">Temp. Motor</span><span class="reading-value">{{ $leitura->temperatura_motor !== null ? number_format($leitura->temperatura_motor, 1, ',', '.').' °C' : 'N/I' }}</span></div>
                                <div class="reading-item {{ $leitura->vibracao === null ? 'reading-disabled' : '' }}"><span class="reading-label">Vibração</span><span class="reading-value">{{ $leitura->vibracao !== null ? number_format($leitura->vibracao, 2, ',', '.').' mm/s' : 'N/I' }}</span></div>
                            </div>
                        </div>

                        {{-- modulo hidraulico: pressao linear e pressao diferencial --}}
                        <div class="reading-section {{ $leitura->pressao_linear === null && $leitura->pressao_diferencial === null ? 'section-disabled' : '' }}">
                            <h5 class="reading-section-title">Módulo Hidráulico</h5>
                            <div class="reading-grid">
                                <div class="reading-item {{ $leitura->pressao_linear === null ? 'reading-disabled' : '' }}"><span class="reading-label">P. Linear</span><span class="reading-value">{{ $leitura->pressao_linear !== null ? number_format($leitura->pressao_linear, 2, ',', '.').' bar' : 'N/I' }}</span></div>
                                <div class="reading-item {{ $leitura->pressao_diferencial === null ? 'reading-disabled' : '' }}"><span class="reading-label">P. Diferencial</span><span class="reading-value">{{ $leitura->pressao_diferencial !== null ? number_format($leitura->pressao_diferencial, 2, ',', '.').' bar' : 'N/I' }}</span></div>
                            </div>
                        </div>
                    @else
                        <div style="padding: 30px 0; text-align: center; color:var(--gray-500); font-style: italic;">
                            Aguardando telemetria...
                        </div>
                    @endif

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; padding-top:15px; border-top:1px solid var(--border);">
                        <span style="font-size:12px; color:var(--gray-500);">{{ $bomba->localizacao ?? 'Sem local' }}</span>
                        <span class="badge badge-status badge-status-{{ $bomba->status }}">
                            {{ strtoupper($bomba->status) }}
                        </span>
                    </div>

                    <div style="margin-top:8px; font-size:11px; color:var(--gray-500); text-align:right;">
                        Leitura: {{ $leitura ? date('d/m H:i', strtotime($leitura->momento_leitura)) : 'N/A' }}
                    </div>

                    <div style="margin-top: auto; padding-top: 15px;">
                        <button class="btn" data-id="{{ $bomba->id }}" data-nome="{{ $bomba->nome }}" onclick="openChartModal(this.dataset.id, this.dataset.nome)" style="width: 100%; background-color: #10b981; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: 600; display: flex; justify-content: center; align-items: center; gap: 8px;">
                            Ver Gráficos
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</main>

<div id="toast" class="toast" style="display:none;"></div>

{{-- modal de graficos em tempo real --}}
<div id="chartModal" class="chart-modal-overlay">
    <div class="chart-modal-content">
        <div class="chart-modal-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 id="chartModalTitle">Gráfico em Tempo Real</h3>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span id="chartOnlineStatus" class="conn-badge conn-online">Online</span>
                <button class="btn-close-chart" onclick="closeChartModal()">&#x2716;</button>
            </div>
        </div>
        <div class="chart-modal-body" style="display: flex; flex-direction: column; gap: 15px;">
            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 10px;">
                <label for="metricSelect" style="font-weight: 600; color: var(--text-main); font-size: 14px;">Monitorar:</label>
                <select id="metricSelect" onchange="renderChart()" style="padding: 8px; border-radius: 6px; border: 1px solid var(--border); background: var(--bg); color: var(--text-main); font-size: 14px; cursor: pointer;">
                    <optgroup label="Central Básica">
                        <option value="corrente_fase_a">Corrente Fase A (A)</option>
                        <option value="corrente_fase_b">Corrente Fase B (A)</option>
                        <option value="corrente_fase_c">Corrente Fase C (A)</option>
                        <option value="fluxo_agua">Fluxo de Água (L/h)</option>
                    </optgroup>
                    <optgroup label="Módulo Elétrico">
                        <option value="tensao_fase_a">Tensão Fase A (V)</option>
                        <option value="tensao_fase_b">Tensão Fase B (V)</option>
                        <option value="tensao_fase_c">Tensão Fase C (V)</option>
                        <option value="status_dps">Status DPS</option>
                    </optgroup>
                    <optgroup label="Módulo Mecânico">
                        <option value="temperatura_motor">Temperatura Motor (°C)</option>
                        <option value="vibracao">Vibração (mm/s)</option>
                    </optgroup>
                    <optgroup label="Módulo Hidráulico">
                        <option value="pressao_linear">Pressão Linear (bar)</option>
                        <option value="pressao_diferencial">Pressão Diferencial (bar)</option>
                    </optgroup>
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
<script src="/assets/javascript/script.js"></script>

@endsection