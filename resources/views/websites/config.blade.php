@extends('websites.index')
@section('assunto')

<main class="main-content">
    <header class="topbar">
        <div>
            <h2>Configurações</h2>
            <p class="extracao-color">Ajustes da conta e preferências do sistema</p>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <button class="btn" style="background:none; font-size: 20px;" onclick="toggleTheme()" id="themeBtn">💡</button>
            <div class="user-profile">
                <span id="userName" style="font-weight: 600; font-size: 14px; color: var(--text-main);">{{ Auth::user()->nome }}</span>
                <a href="/logout" class="btn btn-logout" style="text-decoration: none; font-size: 14px;">Sair</a>
            </div>
        </div>
    </header>

    <div id="config-content" style="margin-top: 30px;">
        <div style="max-width:500px; background:var(--card); padding:25px; border-radius:20px; border:1px solid var(--border);">
            
            <h3 style="margin-bottom:20px; color: var(--text-main);">Configurações</h3>

            @if($errors->any())
                <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('config.update') }}" method="POST">
                @csrf

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-size:14px; font-weight:600; color: var(--text-main);">Nome</label>
                    <input type="text" name="nome" value="{{ old('nome', Auth::user()->nome) }}" style="width:100%; padding:12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text-main); font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-size:14px; font-weight:600; color: var(--text-main);">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" style="width:100%; padding:12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text-main); font-size:14px;">
                </div>

                <div style="margin-bottom:25px;">
                    <label style="display:block; margin-bottom:5px; font-size:14px; font-weight:600; color: var(--text-main);">Notificações</label>
                    <select name="notificacoes" style="width:100%; padding:12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text-main); font-size:14px; cursor: pointer;">
                        <option value="on" {{ old('notificacoes', Auth::user()->notificacoes) == 'on' ? 'selected' : '' }}>Ativado</option>
                        <option value="off" {{ old('notificacoes', Auth::user()->notificacoes) == 'off' ? 'selected' : '' }}>Desativado</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; font-size:16px;">Salvar Configurações</button>
            </form>
        </div>
    </div>
</main>

<div id="toast" class="toast" style="display:none; position: fixed; bottom: 20px; right: 20px; background: var(--primary); color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 9999;"></div>

<script src="/assets/javascript/script.min.js"></script>

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof showToast === "function") {
            showToast("{{ session('success') }}");
        } else {
            const t = document.getElementById('toast');
            t.textContent = "{{ session('success') }}";
            t.style.display = 'block';
            setTimeout(() => t.style.display = 'none', 3000);
        }
    });
</script>
@endif

@endsection