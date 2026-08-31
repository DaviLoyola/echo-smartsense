<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Echo - Gerenciar Usuários</title>
    <link rel="stylesheet" href="/assets/css/style.min.css">
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
                <a href="/admin/criar" class="nav-link active">Gerenciar Usuários</a>
            </li>
             <li class="nav-item">
                <a href="/admin/motobombas" class="nav-link">Gerenciar Motobombas</a>
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
                <h1>Gerenciar Usuários</h1>
                <p>Criar, editar e visualizar usuários do sistema</p>
            </div>
            <span class="admin-label">ADMIN</span>
        </div>

        <div class="card">
            <h2>{{ $editUser ? 'Alterar Dados do Usuário' : 'Criar Novo Usuário' }}</h2>
            
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

            <form action="{{ route('admin.usuarios.salvar') }}" method="POST" id="createUserForm">
                @csrf
                
                <input type="hidden" name="id" value="{{ $editUser->id ?? '' }}">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" name="nome" value="{{ $editUser->nome ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ $editUser->email ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label>Senha {{ $editUser ? '(Deixe vazio para não mudar)' : '' }}</label>
                        <input type="password" name="senha" {{ $editUser ? '' : 'required' }}>
                    </div>
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="tel" name="telefone" id="regPhone" value="{{ $editUser->telefone ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select name="role">
                            <option value="user" {{ ($editUser && $editUser->role == 'user') ? 'selected' : '' }}>Usuário</option>
                            <option value="admin" {{ ($editUser && $editUser->role == 'admin') ? 'selected' : '' }}>Administrador</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fixo/Permanente</label>
                        <select name="permanent">
                            <option value="0" {{ ($editUser && $editUser->permanent == 0) ? 'selected' : '' }}>Não</option>
                            <option value="1" {{ ($editUser && $editUser->permanent == 1) ? 'selected' : '' }}>Sim</option>
                        </select>
                    </div>
                    <div class="form-group" style="display:flex; align-items:end; gap:10px;">
                        <button type="submit" class="btn-create" id="submitBtn">
                            {{ $editUser ? 'Salvar Alterações' : 'Criar Usuário' }}
                        </button>
                        
                        @if($editUser)
                            <a href="{{ route('admin.usuarios.criar') }}" style="background:#e1e5e9; color:#4b5563; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:bold; text-align:center;">
                                Cancelar
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Usuários Cadastrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Telefone</th>
                        <th>Fixo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    @foreach($usuarios as $u)
                        <tr>
                            <td><strong>#{{ $u->id }}</strong></td>
                            <td>{{ $u->nome }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                                <span class="role-badge" style="background: {{ $u->role === 'admin' ? '#ffe2e2' : '#dcebff' }}; color: {{ $u->role === 'admin' ? '#b42323' : '#2257a7' }}; padding:4px 8px; border-radius:4px; font-weight:bold;">
                                    {{ $u->role === 'admin' ? 'Administrador' : 'Usuário' }}
                                </span>
                            </td>
                            <td>{{ $u->telefone ?? '-' }}</td>
                            <td>{{ $u->permanent ? 'Sim' : 'Não' }}</td>
                            <td>
                                <div style="display:flex; gap:8px;">
                                    <a href="{{ route('admin.usuarios.criar', ['edit' => $u->id]) }}" style="background:#dcebff; color:#2257a7; padding:6px 12px; border-radius:6px; text-decoration:none; font-weight:bold; font-size:0.9rem;">
                                        Editar
                                    </a>

                                    <form action="{{ route('admin.usuarios.excluir', $u->id) }}" method="POST" onsubmit="return confirm('Apagar usuário {{ $u->nome }}?');">
                                        @csrf
                                        <button type="submit" style="background:#ffe2e2; color:#b42323; padding:6px 12px; border-radius:6px; border:none; cursor:pointer; font-weight:bold; font-size:0.9rem;">
                                            Excluir
                                        </button>
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