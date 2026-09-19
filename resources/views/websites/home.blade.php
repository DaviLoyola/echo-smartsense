<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECHO - Tecnologia que conecta, inovação que transforma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.min.css">
    <style>
        body.landing-page,
        body.landing-page * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body.landing-page .hero-brand {
            display: block;
            color: #ffffff;
            font-size: 1.25em;
            letter-spacing: 0.12em;
            text-shadow: 0 4px 24px rgba(0, 168, 150, 0.45);
        }

        body.landing-page .hero-tagline {
            display: block;
            font-size: 0.72em;
            letter-spacing: -0.5px;
        }

        body.landing-page .btn-pulse {
            transition: background-color 0.25s ease, border-color 0.25s ease, color 0.25s ease, transform 0.25s ease;
        }

        body.landing-page .btn-pulse:hover,
        body.landing-page .btn-pulse:focus-visible {
            background-color: #008f80 !important;
            border-color: #008f80 !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            outline: none;
        }

        body.landing-page .modules-section {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
        }

        body.landing-page .modules-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
            max-width: 1200px;
            margin: 40px auto 0;
        }

        body.landing-page .module-card {
            padding: 32px 24px 24px;
            text-align: center;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 12px 30px rgba(15, 76, 129, 0.08);
        }

        body.landing-page .module-card h3 {
            margin: 0 0 8px;
            color: #0f4c81;
            font-size: 22px;
            font-weight: 800;
        }

        body.landing-page .module-card p {
            min-height: 48px;
            margin: 0 0 24px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.5;
        }

        body.landing-page .module-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            aspect-ratio: 1.35;
            border: 1px dashed #94a3b8;
            border-radius: 12px;
            background: #f8fafc;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        body.landing-page .module-card.module-hydro h3 {
            color: #1479b8;
        }

        @media (max-width: 768px) {
            body.landing-page .modules-grid {
                grid-template-columns: 1fr;
                max-width: 440px;
            }
        }
    </style>
</head>

<script>
    // Verifica se a URL contém o parâmetro ?simular=1
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('simular') === '1') {
        console.log('Modo simulação ativado – inserindo leituras a cada 1 segundo');

        // Função que chama a rota de simulação
        function enviarLeituraSimulada() {
            fetch('/simular-leitura')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'ok') {
                        // Apenas para debug (opcional)
                        // console.log('Leitura inserida');
                    }
                })
                .catch(err => console.error('Erro na simulação:', err));
        }

        // Dispara a primeira leitura imediatamente
        enviarLeituraSimulada();

        // E repete a cada 1 segundo
        setInterval(enviarLeituraSimulada, 1000);
    }
</script>

<body class="landing-page">

    <header class="header" id="navbar">
        <div class="header-container animate-fade-in">
            <a href="#" class="logo-container">
                <img src="/assets/img/Logo.png" alt="Logo AgroFlow" class="logo-img">
                <h1 class="logo-text"><span>Echo</span></h1>
            </a>

            <ul class="nav-menu">
                <li><a href="#">Home</a></li>
                <li><a href="#contexto">O Desafio</a></li>
                <li><a href="#tecnologia">Tecnologia</a></li>
                <li><a href="#gestao">Gestão</a></li>
            </ul>
            <a href="/login" class="btn-dashboard">Login</a>
        </div>
    </header>

    <section class="hero animate-fade-in">
        <div class="hero-container">
            <div class="hero-tag animate-slide-up">Ecossistema IoT de Monitoramento de Bombas</div>
            <h1 class="animate-slide-up delay-1"><span class="hero-brand">ECHO</span><span class="hero-tagline">Tecnologia que conecta, inovação que transforma</span></h1>
            <p class="animate-slide-up delay-2">Aumente em até <strong>35% a Vida Útil do Equipamento</strong> e <strong>economize com Auditoria,
                Relatórios Técnicos e </strong> com a gestão automatizada.</p>

            <div class="hero-actions animate-slide-up delay-3">
                <a href="/login" class="btn-primary">Começar Agora</a>
                <a href="#contexto" class="btn-secondary">Conhecer o Desafio</a>
            </div>

            <div class="hero-mini-cards animate-slide-up delay-3">
                <div class="mini-card">
                    <div class="mini-card-icon icon-vida"><svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                           <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg></div>
                    <div class="mini-card-info" >
                            <div class="counter" data-target="35"
                                style="font-size: 22px; font-weight: 700; color: #ffffff; line-height: 1.2; letter-spacing: 0.5px;">0%
                            </div>
                        <p>Vida Útil do Equipamento</p>
                    </div>
                </div>
                <div class="mini-card">
                    <div class="mini-card-icon icon-auditoria"><svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" /><polyline points="14 2 14 8 20 8" /><path d="M8 18v-4" /><path d="M12 18v-7" /><path d="M16 18v-2" />
                        </svg></div>
                    <div class="mini-card-info">
                        <h4>Auditoria </h4>
                        <p>Relatórios Técnicos</p>
                    </div>
                </div>
                <div class="mini-card">
                    <div class="mini-card-icon icon-lora"><svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="5" />
                            <path
                                d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                        </svg></div>
                    <div class="mini-card-info">
                        <h4>LoRa</h4>
                        <p>Conectividade Rural</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contexto" class="section" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
        <div class="section-tag scroll-animate">O Cenário Atual</div>
        <h2 class="section-title-gradient scroll-animate">
            <span class="text-tech">O Desafio das</span> <span class="text-precision">Motobombas de irrigação</span>
        </h2>
        <p class="section-subtitle scroll-animate">Entenda por que sistemas convencionais operam no escuro e desperdiçam
            recursos valiosos dos seus motores.</p>

        <div class="tech-grid scroll-animate" style="margin-top: 40px;">
            <div class="tech-card-large" style="text-align: center; padding: 40px 24px;">
                <div class="counter" data-target="20"
                    style="font-size: 56px; font-weight: 800; color: #0f4c81; margin-bottom: 10px; line-height: 1;">0%
                </div>
                <h4 style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 12px;">Menos água em períodos críticos</h4>
                <p style="font-size: 15px; color: #64748b;"> Em sistemas de irrigação de grande porte, uma falha inesperada pode deixar o sistema
                     parado por cerca de 20% do tempo em janelas de alta demanda, reduzindo a lâmina aplicada e afetando diretamente o rendimento da lavoura. Nosso monitoramento IoT antecipa anomalias em motobombas e reduz paradas não programadas.</p>
            </div>

            <div class="tech-card-large" style="text-align: center; padding: 40px 24px;">
                <div class="counter" data-target="15"
                    style="font-size: 56px; font-weight: 800; color: #e74c3c; margin-bottom: 10px; line-height: 1;">0%
                </div>
                <h4 style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 12px;">Proteção da produtividade</h4>
                <p style="font-size: 15px; color: #64748b;">Estudos de extensão rural mostram que uma irrigação mal distribuída pode reduzir a produtividade em até 15%
                     em culturas sensíveis, enquanto falhas de bombeamento em irrigação podem causar perdas substanciais de produção. Nosso sistema ajuda a evitar subirrigação por alerta precoce de falhas elétricas, cavitação e operação a seco.</p>
            </div>

            <div class="tech-card-large" style="text-align: center; padding: 40px 24px;">
                <div class="counter" data-target="50"
                    style="font-size: 56px; font-weight: 800; color: #f59e0b; margin-bottom: 10px; line-height: 1;">0%
                </div>
                <h4 style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 12px;"> Eficiência operacional da irrigação
                </h4>
                <p style="font-size: 15px; color: #64748b;">Mais de 50% dos sistemas de irrigação no Kansas não operam na eficiência ideal, segundo avaliação
                     recente da K-State, o que resulta em perdas de água e produtividade. Com sensores IoT de pressão, corrente, vibração e nível, sua operação identifica desvio de performance antes que a motobomba falhe.</p>
            </div>
        </div>
    </section>

    <section id="tecnologia" class="section">
        <div class="section-tag scroll-animate">A Solução</div>
        <h2 class="section-title-gradient scroll-animate">
            <span class="text-tech">Monitoramento de</span> <span class="text-precision">Precisão</span>
        </h2>
        <p class="section-subtitle scroll-animate">Uma visão limpa e inteligente para suas motobombas</p>

        <div class="tech-grid">
            <div class="tech-card-large scroll-animate">
                <div class="tech-icon-box" style="background-color: #e0f2fe; color: #3b82f6;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12a7 7 0 0 1 14 0M8.5 12a3.5 3.5 0 0 1 7 0M12 12v.01" />
                    </svg>
                </div>
                <h3>Telemetria Multissensorial</h3>
                <p>Acompanhamento completo de grandezas elétricas, térmicas e mecânicas do motor em tempo real.</p>
                <button class="btn-pulse" onclick="openModal('modal-lora')">Leituras</button>
            </div>

            <div class="tech-card-large scroll-animate">
                <div class="tech-icon-box" style="background-color: #e6fdf5; color: #00a896;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                    </svg>
                </div>
                <h3>Corte Crítico Automatizado</h3>
                <p>Interrupção imediata do funcionamento da bomba ao detectar qualquer operação fora das margens seguras.</p>
                <button class="btn-pulse" onclick="openModal('modal-algoritmo')">Quando?</button>
            </div>

            <div class="tech-card-large scroll-animate">
                <div class="tech-icon-box" style="background-color: #e6f7f6; color: #00a896;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="4 7 4 4 20 4 20 7"></polyline>
                        <line x1="9" y1="20" x2="15" y2="20"></line>
                        <line x1="12" y1="4" x2="12" y2="20"></line>
                    </svg>
                </div>
                <h3>Detecção de Trabalho a Seco</h3>
                <p>Monitoramento hidráulico integrado para evitar o desgaste do selo mecânico por falta de água.</p>
                <button class="btn-pulse" onclick="openModal('modal-sensores')">Como?</button>
            </div>
        </div>
    </section>

    <section id="modulos" class="section modules-section">
        <div class="section-tag scroll-animate">NOSSA TECNOLOGIA</div>
        <h2 class="section-title-gradient scroll-animate">Nossos Módulos</h2>
        <p class="section-subtitle scroll-animate">Soluções especializadas para uma visão completa da operação.</p>

        <div class="modules-grid scroll-animate">
            <article class="module-card">
                <h3>mechSense</h3>
                <p>Monitoramento Mecânico Avançado</p>
                <div class="module-placeholder" aria-label="Espaço reservado para imagem 3D do mechSense">Imagem 3D</div>
            </article>
            <article class="module-card">
                <h3>PowerSense</h3>
                <p>Gestão e Diagnóstico Elétrico Integral</p>
                <div class="module-placeholder" aria-label="Espaço reservado para imagem 3D do PowerSense">Imagem 3D</div>
            </article>
            <article class="module-card module-hydro">
                <h3>HydroSense</h3>
                <p>Controle Hidráulico Automatizado (Identidade Visual Azul)</p>
                <div class="module-placeholder" aria-label="Espaço reservado para imagem 3D do HydroSense">Imagem 3D</div>
            </article>
        </div>
    </section>

    <section id="gestao" class="section" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div class="section-tag scroll-animate">MONITORAMENTO INDUSTRIAL</div>
        <h2 class="section-title-gradient scroll-animate">
            <span class="text-tech">Proteção</span> <span class="text-precision">Integral</span>
        </h2>
        <p class="section-subtitle scroll-animate">Evite a queima de motores, monitore variáveis elétricas e automatize desligamentos críticos.
        </p>

        <div class="smart-cost-banner scroll-animate">
            <div style="flex: 1;">
                <h3
                    style="color: #00a896; display: flex; align-items: center; gap: 10px; font-size: 24px; font-weight: 700; margin-bottom: 10px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Segurança de Borda: Corte por Limite Crítico
                </h3>
                <p style="font-size: 16px; color: #475569; line-height: 1.6; margin-bottom: 10px;">Desligamento autônomo
                    e instantâneo ao detectar sobrecarga de corrente, superaquecimento ou oscilações severas de tensão.</p>
                <button class="btn-pulse" onclick="openModal('modal-smartcost')">Proteger meu patrimônio elétrico</button>
            </div>

            <div class=periodo-critico
                style="background-color: #ffffff; padding: 24px 32px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); text-align: center; border: 1px solid #e2e8f0; flex-shrink: 0; min-width: 250px;">
                <div style="color: #e74c3c; font-weight: 700; font-size: 15px; margin-bottom: 8px;">STATUS DE ALERTA
                </div>
                <div
                    style="color: #111827; font-size: 28px; font-weight: 800; margin-bottom: 8px; font-variant-numeric: tabular-nums;">
                    SOBRECARGA</div>
                <div
                    style="background-color: #d1fae5; color: #059669; font-size: 14px; font-weight: 700; padding: 6px 12px; border-radius: 20px; display: inline-block;">
                    Corte Preventivo</div>
            </div>
        </div>

        <div class="tech-grid scroll-animate">
            <div class="tech-card-small">
                <div class="tech-icon-box" style="background-color: #fef2f2; color: #e74c3c;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                        </path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div class="tech-info-small">
                    <h4>Proteção Elétrica Ativa</h4>
                    <p> Evita a queima de motores e o desperdício em caso de rompimentos.</p>
                </div>
                <button class="btn-pulse" style="width: 100%; justify-content: center;"
                    onclick="openModal('modal-vazamento')">Como o Failsafe funciona?</button>
            </div>

            <div class="tech-card-small">
                <div class="tech-icon-box" style="background-color: #fffbeb; color: #d97706;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
                <div class="tech-info-small">
                    <h4>Indicador Heartbeat</h4>
                    <p>Alertas automáticos caso algum sensor fique offline no campo.</p>
                </div>
                <button class="btn-pulse" style="width: 100%; justify-content: center;"
                    onclick="openModal('modal-heartbeat')">Descubra a tecnologia</button>
            </div>

            <div class="tech-card-small">
                <div class="tech-icon-box" style="background-color: #e0f2fe; color: #0f4c81;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="14" y="2" width="6" height="6" rx="1"></rect>
                        <rect x="3" y="14" width="6" height="6" rx="1"></rect>
                        <rect x="14" y="14" width="6" height="6" rx="1"></rect>
                    </svg>
                </div>
                <div class="tech-info-small">
                    <h4>Relatórios Automatizados</h4>
                    <p>Histórico inteligente de consumo de água, energia e chuvas.</p>
                </div>
                <button class="btn-pulse" style="width: 100%; justify-content: center;"
                    onclick="openModal('modal-relatorio')">Transformar dados em lucro</button>
            </div>
        </div>
    </section>

    <footer class="footer-main">
        <div class="footer-container">
            <div class="footer-brand">
                <div class="logo-container">
                    <h1 class="logo-text" style="color: #ffffff;"><span>Echo</span></h1>
                </div>
                <p>Transformando o campo com tecnologia inteligente.</p>
            </div>

            <div class="footer-links-group">
                <div class="footer-column">
                    <h4>PRODUTO</h4>
                    <ul>
                        <li><a href="#">Sensores IoT</a></li>
                        <li><a href="/login">Dashboard</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>EMPRESA</h4>
                    <ul>
                        <li><a href="#">Sobre Nós</a></li>
                        <li><a href="#">Contato</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-socials">
                <a href="#" class="icon-instagram" aria-label="Instagram"><svg width="20" height="20"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg></a>
                <a href="https://br.linkedin.com/in/raphael-augusto-de-paula-freitas-699850382" class="icon-linkedin" aria-label="LinkedIn"><svg width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                        <rect x="2" y="9" width="4" height="12"></rect>
                        <circle cx="4" cy="4" r="2"></circle>
                    </svg></a>
                <a href="#" class="icon-whatsapp" aria-label="WhatsApp"><svg width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                        </path>
                    </svg></a>
            </div>
        </div>
        <div
            style="max-width: 1200px; margin: 30px auto 0 auto; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px; text-align: center;">
            <p style="font-size: 13px; color: #64748b;">© 2026 Echo. Todos os direitos reservados.</p>
        </div>
    </footer>

    <div class="modal-overlay" id="modal-lora">
        <div class="modal-content">
            
            <h3>Telemetria Multissensorial</h3>
              <p>Exibe o painel com os dados consolidados das grandezas elétricas, térmicas e mecânicas capturadas em tempo real.</p>
        </div>
    </div>

    <div class="modal-overlay" id="modal-algoritmo">
        <div class="modal-content">
            
            <h3>Corte Crítico Automatizado</h3>
              <p>Mostra o histórico de alertas e os parâmetros exatos que acionam a interrupção automática por segurança.</p>
        </div>
    </div>

    <div class="modal-overlay" id="modal-sensores">
        <div class="modal-content">
            
            <h3>Detecção de Trabalho a Seco</h3>
              <p>Explica o método de monitoramento hidráulico utilizado para proteger o selo mecânico contra a falta de água.</p>
        </div>
    </div>

    <div class="modal-overlay" id="modal-smartcost">
        <div class="modal-content">
            
            <h3>Proteção do Motor: Detecção de Trabalho a Seco</h3>
            <p>Ao correlacionar a potência elétrica consumida com o fluxo de água efetivo na tubulação,
                 o sistema sabe exatamente se a bomba está puxando ar na captação e interrompe a operação na hora,
                  impedindo a queima por falta de refrigeração.</p>
        </div>
    </div>

    <div class="modal-overlay" id="modal-vazamento">
        <div class="modal-content"> 
            <h3>Ação Instantânea (Failsafe)</h3>
            <p>Se a plataforma mandar a bomba ligar, mas o sensor de fluxo não detectar água correndo nos canos em
                exatos <strong>60 segundos</strong>, o sistema desliga tudo! Isso evita que as bombas trabalhem a seco
                (o que queima os motores) e impede o desperdício caso algum cano tenha estourado.</p>
        </div>
    </div>

    <div class="modal-overlay" id="modal-heartbeat">
        <div class="modal-content">
            <h3>Sinal de Vida Constante</h3>
            <p>O Echo faz checagens constantes de conexão, como um batimento cardíaco ("Heartbeat"). Caso qualquer
                hardware no meio do campo fique offline ou perca a bateria por <strong>mais de 1 hora</strong>, o
                sistema apita no seu celular. Você age rápido, antes que a planta sofra.</p>
        </div>
    </div>

    <div class="modal-overlay" id="modal-relatorio">
        <div class="modal-content">
            <h3>Relatórios Inteligentes</h3>
            <p>Você recebe diretamente no seu e-mail PDFs gerados de forma inteligente com todo o histórico de litros de
                água usados, kilowatts consumidos e chuvas por talhão. Perfeito para auditorias, fechamento financeiro e
                planejamento das próximas safras.</p>
        </div>
    </div>

    <script src="assets/javascript/script.min.js"></script>
    <script>
        window.openModal = function (modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        window.closeModal = function (modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        };
    </script>
</body>

</html>