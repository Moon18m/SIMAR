<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <title>SIMAR — Proyecto de Grado · MonCompany</title>
</head>
<body>

    <header id="inicio">
        <div class="header-inner">
            <a href="#inicio" class="logo-link">
                <img src="assets/img/logos/Logo_simar_blanco.jpeg" alt="Logo SIMAR" class="logo-img">
            </a>
            <nav>
                <a href="#problema">Problema</a>
                <a href="#solucion">Solución</a>
                <a href="#encuesta">Encuesta</a>
                <a href="#equipo">Equipo</a>
                <a href="#ods">ODS</a>
                <a href="registro.php" class="btn-register">Registrarse</a>
                <a href="login.php" class="btn-login">Iniciar sesión</a>
                
            </nav>
            <div class="nav-tag">Grado 10-2 · 2025–2026</div>
        </div>
    </header>


    <section class="hero">
        <div class="hero-content">
            <span class="hero-tag">Proyecto de Grado · Colegio Comfandi · MonCompany</span>
            <h1>
                <span class="hero-acron">S<span>I</span>MAR</span>
            </h1>
            <p class="hero-full">Sistema Inteligente Autónomo de Monitoreo y Respaldo para Alimentos Perecederos</p>
            <p class="hero-slogan">— Tecnología que no caduca</p>
            <div class="hero-cta">
                <a href="#problema" class="btn-primary">Ver el proyecto</a>
                <a href="#solucion" class="btn-outline">Nuestra solución</a>
            </div>
        </div>
        <div class="hero-right">
            
        <div class="hero-monitor">
            <div class="mon-card accent">
                <span class="mon-label">Temperatura</span>
                <span class="mon-value">4.2°C</span>
                <span class="mon-status ok"><span class="dot dot-ok"></span>Normal</span>
            </div>
            <div class="mon-card accent">
                <span class="mon-label">Humedad</span>
                <span class="mon-value">68%</span>
                <span class="mon-status ok"><span class="dot dot-ok"></span>Normal</span>
            </div>
            <div class="mon-card accent">
                <span class="mon-label">Suministro</span>
                <span class="mon-value" style="font-size:22px; padding-top:6px;">Batería</span>
                <span class="mon-status warn"><span class="dot dot-warn"></span>Respaldo activo</span>
            </div>
            <div class="mon-card accent">
                <span class="mon-label">Registros hoy</span>
                <span class="mon-value">1,440</span>
                <span class="mon-status ok"><span class="dot dot-ok"></span>En línea</span>
            </div>
            
             
        </div>
        <div class="life-card">
            <span class="life-label">Vida útil restante</span>
            <div class="life-bar">
                <div class="life-progress" id="lifeProgress"></div>
            </div>
            <span class="life-text" id="lifeText">100%</span>
        </div>

    </div>

</section>

    <section class="section white" id="problema">
        <div class="container">
            <span class="sec-label">01 — Identificación del problema</span>
            <h2 class="sec-title">¿Qué está fallando hoy?</h2>
            <p class="sec-intro">En Colombia se desperdicia cerca del 34% de los alimentos anualmente, con pérdidas concentradas en la etapa de almacenamiento. En Cali y el Valle del Cauca, pequeños y medianos negocios operan sin ningún sistema automatizado de monitoreo ambiental.</p>

            <div class="prob-grid">
                <div class="prob-card">
                    <span class="prob-num">01</span>
                    <h3>Cambios térmicos no detectados</h3>
                    <p>Los alimentos se deterioran por variaciones de temperatura que nadie supervisa en tiempo real.</p>
                </div>
                <div class="prob-card">
                    <span class="prob-num">02</span>
                    <h3>Cortes eléctricos sin respaldo</h3>
                    <p>Las interrupciones comprometen la cadena de frío sin continuidad operativa ni alertas.</p>
                </div>
                <div class="prob-card">
                    <span class="prob-num">03</span>
                    <h3>Sin registros históricos</h3>
                    <p>La falta de datos impide identificar patrones de riesgo o demostrar trazabilidad.</p>
                </div>
                <div class="prob-card">
                    <span class="prob-num">04</span>
                    <h3>Pérdidas económicas y sanitarias</h3>
                    <p>El deterioro prematuro genera costos evitables y riesgos de salud para comerciantes y consumidores.</p>
                </div>
                
            </div>

            <div class="pregunta">
                <span class="pregunta-label">Pregunta problematizadora</span>
                <p>¿Cómo puede un sistema inteligente y autónomo de monitoreo ambiental prevenir el deterioro prematuro de alimentos perecederos y garantizar la continuidad operativa ante interrupciones eléctricas en pequeños negocios y entornos de almacenamiento?</p>
            </div>
        </div>
    </section>

    <!-- SOLUCIÓN -->
    <section class="section dark" id="solucion">
        <div class="container">
            <span class="sec-label sec-label-light">02 — Propuesta de solución</span>
            <h2 class="sec-title sec-title-light">¿Qué es SIMAR?</h2>
            <p class="sec-intro sec-intro-light">Un sistema embebido basado en microcontrolador con sensores ambientales, respaldo energético autónomo e interfaz web local. Diseñado para pequeños negocios a bajo costo, sin depender de la nube.</p>

            <div class="feat-grid">
                <div class="feat">
                    <span class="feat-num">01</span>
                    <h3>Monitoreo en tiempo real</h3>
                    <p>Sensores DHT22 registran temperatura y humedad continuamente. Los datos se muestran en una interfaz web accesible desde cualquier dispositivo de la red local.</p>
                </div>
                <div class="feat">
                    <span class="feat-num">02</span>
                    <h3>Respaldo energético autónomo</h3>
                    <p>Batería recargable con circuito controlado que mantiene el sistema activo durante cortes eléctricos, protegiendo sensores y registro de datos.</p>
                </div>
                <div class="feat">
                    <span class="feat-num">03</span>
                    <h3>Alertas ante condiciones críticas</h3>
                    <p>Notificaciones automáticas cuando temperatura o humedad superan umbrales seguros, permitiendo actuar antes de que ocurra el daño.</p>
                </div>
                <div class="feat">
                    <span class="feat-num">04</span>
                    <h3>Historial y trazabilidad</h3>
                    <p>Base de datos local que construye un historial consultable y permite identificar patrones de riesgo en el almacenamiento.</p>
                </div>
                <div class="feat future">
                    <span class="feat-num">05</span>
                    <h3>Visión artificial <span class="future-badge">Expansión futura</span></h3>
                    <p>Módulo de visión por computador para identificación automática y conteo de productos almacenados.</p>
                </div>
            </div>

            <table class="comp-table">
                <thead>
                    <tr>
                        <th style="width:50%">Característica</th>
                        <th style="width:25%">Soluciones actuales</th>
                        <th style="width:25%">SIMAR</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Monitoreo ambiental continuo</td>
                        <td class="yes">Algunas</td>
                        <td class="yes">Sí</td>
                    </tr>
                    <tr>
                        <td>Respaldo energético integrado</td>
                        <td class="no">No</td>
                        <td class="yes">Sí</td>
                    </tr>
                    <tr>
                        <td>Interfaz web local accesible</td>
                        <td class="no">No</td>
                        <td class="yes">Sí</td>
                    </tr>
                    <tr>
                        <td>Costo accesible para pequeños negocios</td>
                        <td class="no">No</td>
                        <td class="yes">Sí</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

   
    <section class="section tinted" id="encuesta">
        <div class="container">
            <span class="sec-label">03 — Validación</span>
            <h2 class="sec-title">Encuesta de viabilidad</h2>
            <p class="sec-intro">Aplicada a 10 personas encargadas del control de alimentos en tiendas, supermercados y negocios de la región. Los resultados confirman la pertinencia del proyecto.</p>

            <div class="stats-grid">
                <div class="stat">
                    <div class="stat-num">75%</div>
                    <div class="stat-desc">controla los alimentos de forma <strong>completamente manual</strong></div>
                </div>
                <div class="stat">
                    <div class="stat-num">97%</div>
                    <div class="stat-desc">considera SIMAR <strong>útil o muy útil</strong></div>
                </div>
                <div class="stat">
                    <div class="stat-num">85%</div>
                    <div class="stat-desc">adoptaría el sistema con un <strong>costo accesible</strong></div>
                </div>
                <div class="stat">
                    <div class="stat-num">95%</div>
                    <div class="stat-desc">recomendaría implementarlo en <strong>supermercados</strong></div>
                </div>
            </div>

            <div class="conclusion">
                El 75% de los encuestados no tiene ningún sistema automatizado y el 20% carece de cualquier control formal. La demanda es real, la solución es necesaria y la disposición a adoptarla existe cuando el precio es razonable.
            </div>
        </div>
    </section>

  
    <section class="section white" id="equipo">
        <div class="container">
            <span class="sec-label">04 — Autores</span>
            <h2 class="sec-title">El equipo</h2>
            <p class="sec-intro">Estudiantes de grado 10-2 del Colegio Comfandi. Empresa: <strong>MonCompany</strong>.</p>
            <div class="team-grid">
                <div class="member">
                    <div class="avatar">GF</div>
                    <div class="member-name">Gabriel Flores Garzón</div>
                </div>
                <div class="member">
                    <div class="avatar">JG</div>
                    <div class="member-name">Juan Esteban Gutiérrez Florez</div>
                </div>
                <div class="member">
                    <div class="avatar">SM</div>
                    <div class="member-name">Samuel Monsalve López</div>
                </div>
                <div class="member">
                    <div class="avatar">SZ</div>
                    <div class="member-name">Santiago Zapata Segura</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ODS -->
    <section class="section tinted" id="ods">
        <div class="container">
            <span class="sec-label">05 — Impacto</span>
            <h2 class="sec-title">Objetivos de Desarrollo Sostenible</h2>
            <p class="sec-intro">SIMAR se alinea con tres ODS de la Agenda 2030, generando impacto económico, social y ambiental en el contexto colombiano.</p>
            <div class="ods-grid">
                <div class="ods-card ods2">
                    <span class="ods-num">ODS 2</span>
                    <div class="ods-name">Hambre Cero</div>
                    <p>Prevención del deterioro y desperdicio de alimentos perecederos a través del monitoreo ambiental continuo.</p>
                </div>
                <div class="ods-card ods9">
                    <span class="ods-num">ODS 9</span>
                    <div class="ods-name">Industria e Innovación</div>
                    <p>Solución tecnológica innovadora, modular y accesible para entornos con recursos limitados.</p>
                </div>
                <div class="ods-card ods12">
                    <span class="ods-num">ODS 12</span>
                    <div class="ods-name">Producción Responsable</div>
                    <p>Optimización del almacenamiento y conservación de alimentos, reduciendo el desperdicio en la cadena comercial.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-inner">
            <img src="assets/img/logos/Logo_simar_negro.jpeg" alt="SIMAR" class="footer-logo">
            <div class="footer-text">
                <p><strong>SIMAR</strong> — Sistema Inteligente Autónomo de Monitoreo y Respaldo para Alimentos Perecederos</p>
                <p>MonCompany · Colegio Comfandi · Grado 10-2 · Año lectivo 2025–2026</p>
            </div>
        </div>
    </footer>
    <script src="assets/js/index.js"></script>
</body>
</html>
