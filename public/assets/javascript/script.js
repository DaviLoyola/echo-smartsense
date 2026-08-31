/* ============================================================
   AGROECHO – JAVASCRIPT UNIFICADO (APENAS FRONTEND & UX)
   ============================================================ 

// ----- 1. TEMA GLOBAL (DARK / LIGHT MODE) -----
let theme = localStorage.getItem('agroecho_theme') || 'light';

function applyTheme() {
    if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        const themeIcons = document.querySelectorAll('.theme-toggle i, .theme-toggle-index i');
        themeIcons.forEach(icon => { if (icon) icon.className = 'fas fa-sun'; });
    } else {
        document.documentElement.removeAttribute('data-theme');
        const themeIcons = document.querySelectorAll('.theme-toggle i, .theme-toggle-index i');
        themeIcons.forEach(icon => { if (icon) icon.className = 'fas fa-moon'; });
    }
}

function toggleTheme() {
    theme = theme === 'dark' ? 'light' : 'dark';
    localStorage.setItem('agroecho_theme', theme);
    applyTheme();
}
applyTheme();

// ----- 2. SIDEBAR MOBILE (HAMBURGER) -----
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const hamburger = document.getElementById('hamburgerBtn');
    if (!sidebar || !overlay || !hamburger) return;
    
    sidebar.classList.toggle('open');
    overlay.classList.toggle('active');
    hamburger.innerHTML = sidebar.classList.contains('open') ? '✕' : '☰';
}

// ----- 3. LOGOUT INTEGRADO AO LARAVEL -----
function logout() {
    window.location.href = '/logout';
}

// ============================================================
// LANDING PAGE (ANIMAÇÕES E CONTADORES)
// ============================================================
if (document.querySelector('body.landing-page')) {
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    });

    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    window.onclick = function (event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    }

    const counters = document.querySelectorAll('.counter');
    const countTo = (counter) => {
        const target = +counter.getAttribute('data-target');
        const duration = 1500;
        let startTime = null;

        const animate = (timestamp) => {
            if (!startTime) startTime = timestamp;
            const progress = timestamp - startTime;
            const progressPercentage = Math.min(progress / duration, 1);
            const ease = progressPercentage * (2 - progressPercentage);
            const currentValue = Math.floor(ease * target);
            counter.textContent = `${currentValue}%`;
            if (progress < duration) {
                requestAnimationFrame(animate);
            } else {
                counter.textContent = `${target}%`;
            }
        };
        requestAnimationFrame(animate);
    };

    const observerOptions = { threshold: 0.2 };
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                countTo(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    counters.forEach(counter => observer.observe(counter));
}

// ============================================================
// PÁGINA DE LOGIN / CADASTRO (UX DOS FORMULÁRIOS)
// ============================================================
if (document.getElementById('tab-login')) {
    
    window.togglePass = function(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    };

    window.switchTab = function(tab) {
        const isLogin = tab === 'login';
        document.querySelectorAll('.tab-btn').forEach((btn, i) => {
            btn.classList.toggle('active', isLogin ? i === 0 : i === 1);
        });
        document.getElementById('tab-login').classList.toggle('active', isLogin);
        document.getElementById('tab-register').classList.toggle('active', !isLogin);
        document.querySelector('.tab-scroll').scrollTop = 0;
    };

    const phoneInput = document.getElementById('regPhone');
    if(phoneInput) {
        phoneInput.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, '');
            if (val.length > 0) val = val.replace(/^(\d{2})(\d)/, '($1) $2');
            if (val.length > 10) val = val.replace(/(\d{5})(\d)/, '$1-$2');
            this.value = val;
        });
    }

    const pwdInput = document.getElementById('regPassword');
    const strengthFill = document.getElementById('strengthFill');
    const strengthLabel = document.getElementById('strengthLabel');
    
    if(pwdInput) {
        pwdInput.addEventListener('input', function() {
            const pwd = this.value;
            if (!pwd) {
                strengthFill.setAttribute('data-level', '');
                strengthLabel.textContent = '';
                return;
            }
            let score = 0;
            if (pwd.length >= 6) score++;
            if (/[A-Z]/.test(pwd)) score++;
            if (/[!@#$%^&*(),.?":{}|<>]/.test(pwd)) score++;
            if (/[0-9]/.test(pwd)) score++;
            
            let level = score <= 1 ? 'weak' : score <= 2 ? 'fair' : score <= 3 ? 'good' : 'strong';
            strengthFill.setAttribute('data-level', level);
            strengthLabel.textContent = { weak: 'Fraca', fair: 'Razoável', good: 'Boa', strong: 'Forte' }[level];
        });
    }
}

// ============================================================
// PAINEL ADMIN / DASHBOARD (GRÁFICO DINÂMICO DE STATUS)
// ============================================================
document.addEventListener("DOMContentLoaded", function () {
    const chartEl = document.getElementById('statusChart');
    
    if (chartEl) {
        const active = parseInt(chartEl.getAttribute('data-active')) || 0;
        const maintenance = parseInt(chartEl.getAttribute('data-maintenance')) || 0;
        const error = parseInt(chartEl.getAttribute('data-error')) || 0;

        const ctx = chartEl.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Operando Normal', 'Em Manutenção', 'Críticos / Falhas'],
                datasets: [{ 
                    data: [active, maintenance, error], 
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'], 
                    borderRadius: 10 
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }
});

// ============================================================
// MODAL DE CONFIRMAÇÃO GENÉRICO (PÁGINAS ADMIN)
// ============================================================
window.closeModal = function() {
    const modal = document.getElementById('confirmModal');
    if (modal) modal.classList.remove('active');
    window.pendingAction = null;
};

const confirmModal = document.getElementById('confirmModal');
if (confirmModal) {
    confirmModal.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
}

// ============================================================
// RELATÓRIOS (EXPORTAÇÃO EXCEL)
// ============================================================
window.exportarParaExcel = function() {
    try {
        var tabela = document.getElementById("tabela-bruta");
        if (!tabela) {
            alert("Nenhuma tabela encontrada para exportar.");
            return;
        }
        var workbook = XLSX.utils.table_to_book(tabela, {sheet: "Dados Brutos"});
        var dataHoje = new Date().toISOString().split('T')[0];
        var nomeArquivo = 'Relatorio_AgroEcho_' + dataHoje + '.xlsx';
        XLSX.writeFile(workbook, nomeArquivo);
    } catch (error) {
        console.error('Erro na exportação:', error);
        alert("Ocorreu um erro ao gerar o Excel. Tente novamente.");
    }
};

// ============================================================
// CONFIGURAÇÕES (TOAST FEEDBACK)
// ============================================================
function showToast(m) {
    const t = document.getElementById('toast');
    if(t) {
        t.textContent = m;
        t.style.display = 'block';
        setTimeout(() => t.style.display = 'none', 3000);
    }
}

// ============================================================
// MODAL DE GRÁFICOS EM TEMPO REAL (INTEGRAÇÃO LARAVEL VIA AJAX)
// ============================================================
let realTimeChartInstance = null;
let realTimeInterval = null;
let currentBombaId = null;
let currentChartData = [];
let currentLimits = {};

window.openChartModal = function(bombaId, bombaNome) {
    const modal = document.getElementById('chartModal');
    if(modal) {
        currentBombaId = bombaId;
        document.getElementById('chartModalTitle').innerText = 'Monitoramento - ' + bombaNome;
        modal.style.display = 'flex';
        
        fetchChartData();
        realTimeInterval = setInterval(fetchChartData, 1000);
    }
}

window.closeChartModal = function() {
    const modal = document.getElementById('chartModal');
    if(modal) {
        modal.style.display = 'none';
        if(realTimeInterval) clearInterval(realTimeInterval);
        currentBombaId = null;
    }
}

async function fetchChartData() {
    if(!currentBombaId) return;

    try {
        const response = await fetch(`/dispositivos/${currentBombaId}/realtime`);
        const result = await response.json();
        
        if (result.status === 'success') {
            currentChartData = result.leituras;
            currentLimits = result.limites || {};
            renderChart();
        }
    } catch (error) {
        console.error("Erro ao buscar dados de telemetria:", error);
    }
}

function getActiveLimits(metric) {
    if (!currentLimits) return { alert: null, crit: null };
    
    switch (metric) {
        case 'temperatura_motor': return { alert: currentLimits.temperatura_alerta, crit: currentLimits.temperatura_desligar };
        case 'corrente_a': return { alert: currentLimits.corrente_alerta, crit: currentLimits.corrente_desligar_alta };
        case 'tensao_v': return { alert: currentLimits.tensao_alerta_alta, crit: currentLimits.tensao_desligar_alta };
        case 'vibracao': return { alert: currentLimits.vibracao_alerta, crit: currentLimits.vibracao_desligar };
        case 'fluxo_agua': return { alert: currentLimits.fluxo_alerta_baixo, crit: currentLimits.fluxo_desligar_zero };
        default: return { alert: null, crit: null };
    }
}

window.renderChart = function() {
    const canvas = document.getElementById('realTimeChart');
    if(!canvas || currentChartData.length === 0) return;
    
    const ctx = canvas.getContext('2d');
    const metric = document.getElementById('metricSelect').value;
    const limits = getActiveLimits(metric);

    const labels = currentChartData.map(d => {
        const date = new Date(d.momento_leitura);
        return date.getHours().toString().padStart(2, '0') + ':' + 
               date.getMinutes().toString().padStart(2, '0') + ':' + 
               date.getSeconds().toString().padStart(2, '0');
    });

    const dataPoints = currentChartData.map(d => parseFloat(d[metric]));

    const alertData = limits.alert ? Array(labels.length).fill(limits.alert) : [];
    const critData = limits.crit ? Array(labels.length).fill(limits.crit) : [];

    const datasets = [{
        label: 'Leitura em Tempo Real',
        data: dataPoints,
        borderColor: '#2d7ff9',
        backgroundColor: 'rgba(45, 127, 249, 0.1)',
        borderWidth: 2,
        fill: true,
        tension: 0.4,
        pointRadius: 3
    }];

    if (limits.alert) {
        datasets.push({
            label: 'Limite de Alerta',
            data: alertData,
            borderColor: '#f59e0b',
            borderWidth: 2,
            borderDash: [5, 5],
            pointRadius: 0,
            fill: false
        });
    }

    if (limits.crit) {
        datasets.push({
            label: 'Limite Crítico (Corte)',
            data: critData,
            borderColor: '#ef4444',
            borderWidth: 2,
            borderDash: [5, 5],
            pointRadius: 0,
            fill: false
        });
    }

    if (realTimeChartInstance) {
        realTimeChartInstance.data.labels = labels;
        realTimeChartInstance.data.datasets = datasets;
        realTimeChartInstance.update('none');
    } else {
        realTimeChartInstance = new Chart(ctx, {
            type: 'line',
            data: { labels: labels, datasets: datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 0 },
                scales: {
                    y: { beginAtZero: false }
                }
            }
        });
    }
}

// ============================================================
// COMUNICAÇÃO SERIAL WEB (Cabo USB -> Navegador -> Banco de Dados)
// ============================================================
const btnConnectSerial = document.getElementById('btnConnectSerial');
const selectDispositivo = document.getElementById('selectDispositivoSerial');

if (btnConnectSerial) {
    btnConnectSerial.addEventListener('click', async () => {
        // 1. Valida se o usuário escolheu uma bomba
        if (!selectDispositivo.value) {
            alert("Por favor, selecione uma Motobomba no menu antes de conectar o cabo USB.");
            return;
        }

        const serialStatus = document.getElementById('serialStatus');

        if (!("serial" in navigator)) {
            alert("Seu navegador não suporta a Web Serial API. Use o Google Chrome, Edge ou Opera.");
            return;
        }

        try {
            const port = await navigator.serial.requestPort();
            await port.open({ baudRate: 9600 });

            serialStatus.innerText = "🟢 Transmitindo...";
            serialStatus.style.color = "#10b981";
            btnConnectSerial.style.display = 'none';
            
            // Trava o select para o usuário não mudar de bomba no meio da transmissão
            selectDispositivo.disabled = true; 

            const textDecoder = new TextDecoderStream();
            const readableStreamClosed = port.readable.pipeTo(textDecoder.writable);
            const reader = textDecoder.readable.getReader();

            const textEncoder = new TextEncoderStream();
            const writableStreamClosed = textEncoder.readable.pipeTo(port.writable);
            const writer = textEncoder.writable.getWriter();

            let buffer = "";
            const dispositivoIdSelecionado = parseInt(selectDispositivo.value);

            while (true) {
                const { value, done } = await reader.read();
                if (done) {
                    reader.releaseLock();
                    break;
                }
                
                buffer += value;
                let lines = buffer.split('\n');
                buffer = lines.pop(); 

                for (let line of lines) {
                    line = line.trim();
                    
                    if (line.startsWith('{') && line.endsWith('}')) {
                        console.log("[USB Dados Brutos]:", line);
                        
                        try {
                            const payload = JSON.parse(line);
                            
                            // 2. A MÁGICA ACONTECE AQUI: 
                            // Injeta o ID da bomba selecionada na tela direto no JSON do Arduino
                            payload.dispositivo_id = dispositivoIdSelecionado;
                            
                            const response = await fetch('/api/telemetria', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(payload)
                            });

                            if (response.ok) {
                                const result = await response.json();
                                
                                if (result.critico_desligar === true) {
                                    await writer.write('1'); // Ativa corte do Relé
                                } else {
                                    await writer.write('0'); // Mantém Relé ligado
                                }
                            }
                        } catch (e) {
                            console.error("Erro ao processar dados via USB:", e);
                        }
                    }
                }
            }
        } catch (error) {
            console.error("Erro na comunicação Serial:", error);
            serialStatus.innerText = "🔴 Erro/Desconectado";
            serialStatus.style.color = "#ef4444";
            btnConnectSerial.style.display = 'block';
            selectDispositivo.disabled = false; // Destrava o select em caso de erro
        }
    });
} */