<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Echo - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.min.css" />
</head>
<body class="index-page">

<div class="container">
    <button class="theme-toggle" onclick="toggleTheme()" title="Alternar tema" id="themeBtn">
        <i class="fas fa-moon"></i>
    </button>

    <div class="brand-panel">
        <div class="logo-frame">
            <img src="/assets/img/Logo.png" class="logo-img" alt="AgroEcho Logo">
        </div>
        <div class="brand-name">Echo</div>
        <div class="brand-tagline">Irrigação Inteligente</div>
    </div>

    <div class="form-panel">
        <div class="tab-row">
            <button class="tab-btn {{ !$errors->has('nome') && !$errors->has('email.unique') ? 'active' : '' }}" onclick="switchTab('login')">Entrar</button>
            <button class="tab-btn {{ $errors->has('nome') || $errors->has('email.unique') ? 'active' : '' }}" onclick="switchTab('register')">Cadastrar</button>
        </div>

        <div class="tab-scroll">
            
            <div class="tab-content {{ !$errors->has('nome') && !$errors->has('email.unique') ? 'active' : '' }}" id="tab-login">
                <div class="section-title">Bem-vindo de volta</div>
                <div class="section-subtitle">Acesse sua conta para continuar.</div>
                
                @error('login')
                    <div class="alert-msg error" style="display: block;">{{ $message }}</div>
                @enderror

                <!-- CORRIGIDO: action com https -->
                <form method="POST" action="{{ secure_url('/login') }}">
                    @csrf
                    <div class="field-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="seu@email.com" required>
                    </div>
                    <div class="field-group">
                        <label>Senha</label>
                        <input type="password" id="loginPassword" name="password" placeholder="Sua senha" required>
                        <button type="button" class="toggle-password" onclick="togglePass('loginPassword', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <button type="submit" class="btn-primary">Entrar</button>
                </form>
            </div>

            <div class="tab-content {{ $errors->has('nome') || $errors->has('email.unique') ? 'active' : '' }}" id="tab-register">
                <div class="section-title">Criar conta</div>
                <div class="section-subtitle">Preencha os dados para começar.</div>
                
                @if($errors->any() && !$errors->has('login'))
                    <div class="alert-msg error" style="display: block;">
                        Verifique os campos preenchidos.
                        @error('email') <br>- {{ $message }} @enderror
                        @error('senha') <br>- {{ $message }} @enderror
                    </div>
                @endif

                <!-- CORRIGIDO: action com https -->
                <form method="POST" action="{{ secure_url('/register') }}">
                    @csrf
                    <div class="field-group">
                        <label>Nome completo <span class="asterisk">*</span></label>
                        <input type="text" name="nome" value="{{ old('nome') }}" placeholder="Seu nome completo" required>
                    </div>

                    <div class="field-group">
                        <label>Email <span class="asterisk">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="seu@email.com" required>
                    </div>

                    <div class="field-group">
                        <label>Telefone</label>
                        <input type="tel" id="regPhone" name="telefone" value="{{ old('telefone') }}" placeholder="(00) 00000-0000" maxlength="15">
                    </div>

                    <div class="field-group">
                        <label>Senha <span class="asterisk">*</span></label>
                        <input type="password" id="regPassword" name="senha" placeholder="Mínimo 6 caracteres" required>
                        <button type="button" class="toggle-password" onclick="togglePass('regPassword', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div class="strength-track">
                            <div class="strength-fill" id="strengthFill" data-level=""></div>
                        </div>
                        <div class="strength-label" id="strengthLabel" data-level=""></div>
                    </div>

                    <button type="submit" class="btn-primary">Criar conta</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script defer src="/assets/javascript/script.min.js"></script>
</body>
</html>