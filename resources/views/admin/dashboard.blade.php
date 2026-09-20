@extends('admin.index')
@section('assuntoADM')

<main class="main">
    <div class="topbar">
        <div>
            <h2>Painel Administrativo</h2>
            <p>Controle geral dos dispositivos conectados</p>
        </div>
        <div class="admin-badge-top">ADMIN</div>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-number">{{ $total }}</div>
            <div class="stat-label">Total de Dispositivos</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #10b981;">{{ $ativos }}</div>
            <div class="stat-label">Operando Normal</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #f59e0b;">{{ $manutencao }}</div>
            <div class="stat-label">Em Manutenção</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #ef4444;">{{ $erros }}</div>
            <div class="stat-label">Críticos / Falhas</div>
        </div>
    </div>

    <div class="chart-box">
        <h3>Status dos Dispositivos</h3>
        <canvas id="statusChart"
            data-active="{{ $ativos }}"
            data-maintenance="{{ $manutencao }}"
            data-error="{{ $erros }}"
            height="80"></canvas>
    </div>

    <div class="table-box">
        <h3>Dispositivos Conectados</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Proprietário</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dispositivos as $d)
                    @php
                        $statusClass = match($d->status) {
                            'active' => 'active',
                            'maintenance' => 'maintenance',
                            'error' => 'inactive',
                            default => ''
                        };
                        $statusLabel = match($d->status) {
                            'active' => 'Operando',
                            'maintenance' => 'Manutenção',
                            'error' => 'Falha',
                            default => ucfirst($d->status)
                        };
                    @endphp
                    <tr>
                        <td><strong>{{ $d->id }}</strong></td>
                        <td>{{ $d->nome }}</td>
                        <td>{{ $d->tipo ?? '-' }}</td>
                        <td>{{ $d->usuario->nome ?? 'Sem proprietário' }}</td>
                        <td><span class="status {{ $statusClass }}">{{ $statusLabel }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">Nenhum dispositivo cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; color: var(--text-light); font-size: 0.85rem;">
        <span>Última atualização: {{ now()->format('d/m/Y H:i:s') }}</span>
        <button class="btn" onclick="window.location.reload()" style="background: var(--primary); color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 600;">
            Atualizar Dados
        </button>
    </div>
</main>

<script defer src="/assets/javascript/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection