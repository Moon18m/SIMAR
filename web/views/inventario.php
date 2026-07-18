<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventario · SIMAR</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/variables.css">
<link rel="stylesheet" href="../assets/css/inventario.css">

</head>
<body>

  <div class="dashboard">
    
      <aside class="sidebar">

          <div>
              <a href="dashboard.html" class="logo">
                  <img src="../assets/img/iconos/icono_simar.png" alt="SIMAR">
                  <h2>S.I.M.A.R</h2>
              </a>
              <nav>
                  <ul>
                      <li><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a></li>
                      <li class="active"><a href="inventario.php"><i class="fa-solid fa-box"></i><span>Inventario</span></a></li>
                      <li><a href="#"><i class="fa-solid fa-microchip"></i><span>Sensores</span></a></li>
                      <li><a href="#"><i class="fa-solid fa-camera"></i><span>Cámara IA</span></a></li>
                      <li><a href="#"><i class="fa-solid fa-triangle-exclamation"></i><span>Alertas</span></a></li>
                      <li><a href="#"><i class="fa-solid fa-file-lines"></i><span>Reportes</span></a></li>
                  </ul>
              </nav>
          </div>

          <div class="sidebar-footer">
              <span>Sistema</span>
              <strong>SIMAR</strong>
          </div>

      </aside>

      <main class="contenido">
        <section class="inventario">       
          <header class="inventario__header">

            <div>
              <div class="inventario__eyebrow">Control de existencias</div>
              <h1 class="inventario__titulo">Inventario de alimentos</h1>
            </div>

            <div class="inventario__resumen">
              <div class="res--ok"><strong id="res-ok">0</strong><span>Vigentes</span></div>
              <div class="res--warning"><strong id="res-warning">0</strong><span>Por vencer</span></div>
              <div class="res--error"><strong id="res-error">0</strong><span>Vencidos</span></div>
              <div><strong id="res-total">0</strong><span>Total</span></div>
            </div>

          </header>

          <form class="inventario__form" id="inventario-form">
            
            <div class="campo">
              <label for="inventario-id-producto">ID de producto</label>
              <input type="text" id="inventario-id-producto" placeholder="Ej. LAC-002" required>
            </div>
            <div class="campo">
              <label for="inventario-cantidad">Cantidad</label>
              <input type="number" id="inventario-cantidad" min="1" step="1" placeholder="0" required>
            </div>
            <div class="campo">
              <label for="inventario-ingreso">Fecha de ingreso</label>
              <input type="date" id="inventario-ingreso" required>
            </div>
            <div class="campo">
              <label for="inventario-vida-util">Vida útil calculada (días)</label>
              <input type="number" id="inventario-vida-util" step="1" placeholder="Ej. 5" required>
            </div>
            <button type="submit" class="btn-agregar">Agregar</button>
          </form>

          <div class="inventario__toolbar">
            <input type="search" id="inventario-buscar" placeholder="Buscar por ID de producto…">
            <select id="inventario-filtro">
              <option value="todos">Todos los estados</option>
              <option value="ok">Vigentes</option>
              <option value="warning">Por vencer</option>
              <option value="error">Vencidos</option>
            </select>
            <select id="inventario-orden">
              <option value="urgencia">Ordenar por urgencia</option>
              <option value="ingreso">Ordenar por fecha de ingreso</option>
              <option value="id_producto">Ordenar alfabéticamente</option>
            </select>
          </div>

          <div class="inventario__grid" id="inventario-grid"></div>
          <p class="inventario__vacio" id="inventario-vacio" hidden>No hay alimentos que coincidan con la búsqueda.</p>
        </section>

        <script src="inventario.js"></script>
      </main>

  </div>

  <script src="inventario.js"></script>

</body>
</html>