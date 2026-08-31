<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Echo - Gerenciar Motobombas</title>
    <link rel="stylesheet" href="/assets/css/style.min.css">
    <style>
        .section-title { grid-column: 1 / -1; margin-top: 15px;  solid #e1e5e9; color: var(--gray--900); font-size: 1.1rem; }
        .filter-form { display: flex; gap: 10px; margin-bottom: 15px; }
        .filter-form select { padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; width: 300px; background-color: white; }
    </style>
</head>
<body class="admin-page">

<button class="hamburger" onclick="toggleSidebar()" id="hamburgerBtn">☰</button>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="container">
    <nav class="sidebar" id="sidebar">
        <div class="admin-header">
            <span class="admin-badge">ADMINISTRADOR</span>
            <div class="logo">Echo</div>
            <div class="user-name" id="sidebarUserName">{{ auth()->user()->nome ?? 'Admin' }}</div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="/admin" class="nav-link">Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="/admin/criar" class="nav-link">Gerenciar Usuários</a>
            </li>
            <li class="nav-item">
                <a href="/admin/motobombas" class="nav-link active">Gerenciar Motobombas</a>
            </li>
        </ul>

        <div class="bottom-links">
            <button class="theme-toggle" onclick="toggleTheme()">Alternar Tema</button>
            <a href="/" class="nav-link">Página Inicial</a>
            <a href="#" class="nav-link" onclick="logout()">Sair</a>
        </div>
        
        @if(auth()->check() && auth()->user()->role === 'admin')
            <button class="btn-back-user" style="background: #ffffff;color: #000000;padding: 10px 12px;border: none;border-radius: 6px;cursor: pointer;font-weight: bold;width: 100%;margin-bottom: 10px; margin-top: auto;" onclick="window.location.href='/dispositivos'">
                Voltar para Usuário
            </button>
        @endif
    </nav>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h1>Gerenciar Motobombas</h1>
                <p>Cadastre as bombas e defina os parâmetros de alerta e corte</p>
            </div>
            <span class="admin-label">ADMIN</span>
        </div>

        <div class="card">
            <h2>{{ $editDevice ? 'Editar Motobomba #'.$editDevice->id : 'Cadastrar Nova Motobomba' }}</h2>
            
            @if(session('success'))
                <div style="background:#e2f7ed; color:#1b8f5a; padding:12px; border-radius:6px; margin-bottom:15px; font-weight:bold;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="background:#ffe2e2; color:#b42323; padding:12px; border-radius:6px; margin-bottom:15px; font-weight:bold;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.dispositivos.salvar') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $editDevice->id ?? '' }}">

                <div class="form-grid">
                    <h3 class="section-title">Dados Gerais</h3>
                    <div class="form-group">
                        <label>Nome da Bomba</label>
                        <input type="text" name="nome" value="{{ $editDevice->nome ?? '' }}" required placeholder="Ex: Bomba do Poço 02">
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <input type="text" name="tipo" value="{{ $editDevice->tipo ?? 'Motobomba' }}" required>
                    </div>
                    <div class="form-group">
                        <label>Modelo</label>
                        <input type="text" name="modelo" value="{{ $editDevice->modelo ?? '' }}" placeholder="Ex: Schneider 2.0 CV">
                    </div>
                    <div class="form-group">
                        <label>Localização</label>
                        <input type="text" name="localizacao" value="{{ $editDevice->localizacao ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="active" {{ ($editDevice && $editDevice->status == 'active') ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ ($editDevice && $editDevice->status == 'inactive') ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cliente / Dono</label>
                        <select name="usuario_id" required>
                            <option value="">Selecione o proprietário...</option>
                            @foreach($usuarios as $user)
                                <option value="{{ $user->id }}" {{ ($editDevice && $editDevice->usuario_id == $user->id) ? 'selected' : '' }}>
                                    {{ $user->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <h3 class="section-title">Parâmetros de Proteção (Padrão Schneider 2.0 CV)</h3>
                    
                    <div class="form-group"><label>Corrente Alerta (A)</label><input type="number" step="0.001" name="corrente_alerta" value="{{ $editLimits->corrente_alerta ?? '7.150' }}"></div>
                    <div class="form-group"><label>Corrente Corte Alta (A)</label><input type="number" step="0.001" name="corrente_desligar_alta" value="{{ $editLimits->corrente_desligar_alta ?? '8.000' }}"></div>
                    <div class="form-group"><label>Corrente Trabalho a Seco (A)</label><input type="number" step="0.001" name="corrente_desligar_baixa" value="{{ $editLimits->corrente_desligar_baixa ?? '3.250' }}"></div>
                    
                    <div class="form-group"><label>Tensão Alerta Baixa (V)</label><input type="number" step="0.001" name="tensao_alerta_baixa" value="{{ $editLimits->tensao_alerta_baixa ?? '204.600' }}"></div>
                    <div class="form-group"><label>Tensão Alerta Alta (V)</label><input type="number" step="0.001" name="tensao_alerta_alta" value="{{ $editLimits->tensao_alerta_alta ?? '235.400' }}"></div>
                    <div class="form-group"><label>Tensão Corte Baixa (V)</label><input type="number" step="0.001" name="tensao_desligar_baixa" value="{{ $editLimits->tensao_desligar_baixa ?? '198.000' }}"></div>
                    <div class="form-group"><label>Tensão Corte Alta (V)</label><input type="number" step="0.001" name="tensao_desligar_alta" value="{{ $editLimits->tensao_desligar_alta ?? '242.000' }}"></div>
                    
                    <div class="form-group"><label>Vibração Alerta (mm/s)</label><input type="number" step="0.001" name="vibracao_alerta" value="{{ $editLimits->vibracao_alerta ?? '6.600' }}"></div>
                    <div class="form-group"><label>Vibração Corte (mm/s)</label><input type="number" step="0.001" name="vibracao_desligar" value="{{ $editLimits->vibracao_desligar ?? '8.300' }}"></div>
                    
                    <div class="form-group"><label>Temperatura Alerta (°C)</label><input type="number" step="0.001" name="temperatura_alerta" value="{{ $editLimits->temperatura_alerta ?? '124.000' }}"></div>
                    <div class="form-group"><label>Temperatura Corte (°C)</label><input type="number" step="0.001" name="temperatura_desligar" value="{{ $editLimits->temperatura_desligar ?? '155.000' }}"></div>
                    
                    <div class="form-group"><label>Fluxo Alerta Baixo (%)</label><input type="number" step="0.001" name="fluxo_alerta_baixo" value="{{ $editLimits->fluxo_alerta_baixo ?? '30.000' }}"></div>

                    <div class="form-group" style="display:flex; align-items:end; gap:10px; margin-top: 15px; grid-column: 1 / -1;">
                        <button type="submit" class="btn-create" style="padding:12px 20px;">Salvar</button>
                        @if($editDevice)
                            <a href="{{ route('admin.dispositivos.criar') }}" style="background:#e1e5e9; color:#4b5563; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:bold;">Cancelar</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Bombas Cadastradas</h2>
            
            <form action="{{ route('admin.dispositivos.criar') }}" method="GET" class="filter-form">
                <select name="usuario_filtro">
                    <option value="">Todos os Usuários (Donos)</option>
                    @foreach($usuarios as $user)
                        <option value="{{ $user->id }}" {{ request('usuario_filtro') == $user->id ? 'selected' : '' }}>
                            {{ $user->nome }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" style="background:#2257a7; color:white; border:none; padding:8px 15px; border-radius:6px; cursor:pointer; font-weight:bold;">Filtrar</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Modelo</th>
                        <th>Dono</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dispositivos as $d)
                        <tr>
                            <td><strong>#{{ $d->id }}</strong></td>
                            <td>{{ $d->nome }}</td>
                            <td>{{ $d->modelo ?? '-' }}</td>
                            <td>{{ $d->dono_nome ?? 'Sem vínculo' }}</td>
                            <td>
                                <span style="background: {{ $d->status === 'active' ? '#e2f7ed' : '#ffe2e2' }}; color: {{ $d->status === 'active' ? '#1b8f5a' : '#b42323' }}; padding:4px 8px; border-radius:4px; font-weight:bold;">
                                    {{ $d->status === 'active' ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; gap:8px;">
                                    <a href="{{ route('admin.dispositivos.criar', ['edit' => $d->id]) }}" style="background:#dcebff; color:#2257a7; padding:6px 12px; border-radius:6px; text-decoration:none; font-weight:bold; font-size:0.9rem;">Editar</a>
                                    <form action="{{ route('admin.dispositivos.excluir', $d->id) }}" method="POST" onsubmit="return confirm('Deseja apagar esta bomba?');">
                                        @csrf
                                        <button type="submit" style="background:#ffe2e2; color:#b42323; padding:6px 12px; border-radius:6px; border:none; cursor:pointer; font-weight:bold; font-size:0.9rem;">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</div>

<script defer src="/assets/javascript/script.min.js"></script>
</body>
</html>