<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Echo - Dashboard</title>

<link rel="stylesheet" href="/assets/css/style.min.css" >


</head>

<body class="dashboard-page">

<!--HAMBURGUER -->
<button class="hamburger" onclick="toggleSidebar()" id="hamburgerBtn">☰</button>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="container">

    <aside class="sidebar" id="sidebar">
        <div class="admin-header">
            <span class="admin-badge">ADMINISTRADOR</span>
            <div class="logo">Echo</div>
            <div class="user-name" id="sidebarUserName">{{ auth()->user()->nome ?? 'Admin' }}</div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="/admin" class="nav-link active">Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="/admin/criar" class="nav-link">Gerenciar Usuários</a>
            </li>
            <li class="nav-item">
                <a href="/admin/motobombas" class="nav-link">Gerenciar Motobombas</a>
            </li>
        </ul>

        <div class="bottom-links">
            <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn">Alternar Tema</button>

            <a href="/" class="nav-link">Página Inicial</a>
            <a href="#" class="nav-link" onclick="logout()">Sair</a>
        </div>
        
        @if(auth()->check() && auth()->user()->role === 'admin')
            <button class="btn-back-user" style="background: #ffffff; color: #000000; padding: 10px 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; margin-bottom: 10px; margin-top: auto;" onclick="window.location.href='/dispositivos'">
                Voltar para Usuário
            </button>
        @endif
    </aside> @yield('assuntoADM')

</body>
</html>