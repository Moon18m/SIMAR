/* ==========================================================
   SENSORES.JS
   ----------------------------------------------------------
   Consume datos reales de "controllers/sensoresController.php"
   (mismo patrón que "dashboard.js" con "dashboardController.php").
   El gráfico se crea UNA sola vez y luego solo se actualiza
   con .update() — nunca se destruye ni se vuelve a crear —
   para que cambiar de sensor/rango sea instantáneo y no
   vaya generando gráficos encima de otros.
   ========================================================== */

document.addEventListener("DOMContentLoaded", () => {

    const URL_CONTROLLER = "../controllers/sensoresController.php";

    /* -----------------------------------------
       Elementos
    ----------------------------------------- */
    const btnActualizar   = document.getElementById("btnActualizar");
    const spanTiempo      = document.getElementById("tiempoActualizacion");
    const selectSensor    = document.getElementById("selectSensor");
    const selectRango     = document.getElementById("selectRango");
    const tablaLecturas   = document.getElementById("tablaLecturas");
    const canvas          = document.getElementById("graficaSensores");

    /* -----------------------------------------
       Helpers
    ----------------------------------------- */
    function formatearHora(fechaHora) {
        if (!fechaHora) return "--:--:--";
        const partes = fechaHora.split(" ");
        return partes[1] ? partes[1] : fechaHora;
    }

    function badgeSegunEstado(estado) {
        switch (estado) {
            case "Activo":   return { texto: "🟢 Activo",   clase: "badge-ok" };
            case "Error":    return { texto: "🔴 Error",    clase: "badge-error" };
            default:         return { texto: "⚪ Inactivo",  clase: "badge-inactivo" };
        }
    }

    /* -----------------------------------------
       1) Resumen: tarjetas Temperatura / Humedad
          + panel "Estado del sistema"
    ----------------------------------------- */
    async function cargarResumen() {
        try {
            const res = await fetch(`${URL_CONTROLLER}?accion=resumen`);
            const json = await res.json();

            if (!json.success) return;

            const { sensores, conteo } = json.data;

            document.getElementById("totalActivos").textContent = conteo.activos;
            document.getElementById("totalError").textContent = conteo.error;
            document.getElementById("totalInactivos").textContent = conteo.inactivos;

            sensores.forEach((s) => {
                const tipo = s.tipo === "Temperatura" ? "Temperatura" : (s.tipo === "Humedad" ? "Humedad" : null);
                if (!tipo) return; // ignora tipos que aún no tienen tarjeta (Magnético, Corriente)

                const sufijo = tipo === "Temperatura" ? "Temperatura" : "Humedad";
                const unidad = tipo === "Temperatura" ? "°C" : "%";

                const valorEl = document.getElementById(`valor${sufijo}`);
                const horaEl  = document.getElementById(`hora${sufijo}`);
                const badgeEl = document.getElementById(`badge${sufijo}`);

                if (valorEl) {
                    valorEl.innerHTML = s.valor !== null
                        ? `${Number(s.valor).toFixed(1)} <span>${unidad}</span>`
                        : `Sin datos <span></span>`;
                }
                if (horaEl) horaEl.textContent = formatearHora(s.fecha_hora);
                if (badgeEl) {
                    const badge = badgeSegunEstado(s.estado);
                    badgeEl.textContent = badge.texto;
                    badgeEl.className = `badge-estado ${badge.clase}`;
                }
            });

        } catch (error) {
            console.error("Error al cargar el resumen de sensores:", error);
        }
    }

    /* -----------------------------------------
       2) Tabla: últimas lecturas
    ----------------------------------------- */
    async function cargarUltimasLecturas() {
        try {
            const res = await fetch(`${URL_CONTROLLER}?accion=ultimas`);
            const json = await res.json();

            if (!json.success || !tablaLecturas) return;

            if (json.data.length === 0) {
                tablaLecturas.innerHTML = `<tr><td colspan="3">Todavía no hay lecturas registradas.</td></tr>`;
                return;
            }

            tablaLecturas.innerHTML = json.data.map((lectura) => {
                const unidad = lectura.tipo === "Temperatura" ? "°C" : "%";
                const icono = lectura.tipo === "Temperatura" ? "fa-temperature-half" : "fa-droplet";
                return `
                    <tr>
                        <td><i class="fa-solid ${icono}"></i>${lectura.nombre}</td>
                        <td>${Number(lectura.valor).toFixed(2)} ${unidad}</td>
                        <td>${lectura.fecha_hora}</td>
                    </tr>
                `;
            }).join("");

            if (spanTiempo) {
                spanTiempo.textContent = formatearHora(json.data[0].fecha_hora);
            }

        } catch (error) {
            console.error("Error al cargar las últimas lecturas:", error);
        }
    }

    /* -----------------------------------------
       3) Gráfico de historial (Chart.js)
          Se crea UNA sola vez, después solo se
          actualiza con .update().
    ----------------------------------------- */
    let grafica = null;

    if (canvas && typeof Chart !== "undefined") {
        grafica = new Chart(canvas, {
            type: "line",
            data: {
                labels: [],
                datasets: [{
                    label: "Temperatura (°C)",
                    data: [],
                    borderColor: "#0e587d",
                    backgroundColor: "rgba(14, 88, 125, 0.12)",
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                resizeDelay: 100,
                animation: { duration: 300 },
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: false } },
            },
        });
    }

    async function cargarHistorial() {
        if (!grafica || !selectSensor || !selectRango) return;

        const tipo = selectSensor.value;
        const rango = selectRango.value;
        const unidad = tipo === "Temperatura" ? "°C" : "%";
        const color = tipo === "Temperatura" ? "#0e587d" : "#1c3166";

        selectSensor.disabled = true;
        selectRango.disabled = true;

        try {
            const res = await fetch(`${URL_CONTROLLER}?accion=historial&tipo=${encodeURIComponent(tipo)}&rango=${encodeURIComponent(rango)}`);
            const json = await res.json();

            if (!json.success) return;

            const etiquetas = json.data.map((d) => formatearHora(d.fecha_hora));
            const valores = json.data.map((d) => Number(d.valor));

            grafica.data.labels = etiquetas;
            grafica.data.datasets[0].data = valores;
            grafica.data.datasets[0].label = `${tipo} (${unidad})`;
            grafica.data.datasets[0].borderColor = color;
            grafica.data.datasets[0].backgroundColor = color + "1f";
            grafica.update();

        } catch (error) {
            console.error("Error al cargar el historial:", error);
        } finally {
            selectSensor.disabled = false;
            selectRango.disabled = false;
        }
    }

    /* -----------------------------------------
       Eventos
    ----------------------------------------- */
    if (selectSensor) selectSensor.addEventListener("change", cargarHistorial);
    if (selectRango) selectRango.addEventListener("change", cargarHistorial);

    if (btnActualizar) {
        btnActualizar.addEventListener("click", async () => {
            btnActualizar.disabled = true;
            btnActualizar.querySelector("i").classList.add("fa-spin");

            await Promise.all([
                cargarResumen(),
                cargarUltimasLecturas(),
                cargarHistorial(),
            ]);

            btnActualizar.disabled = false;
            btnActualizar.querySelector("i").classList.remove("fa-spin");
        });
    }

    /* -----------------------------------------
       Carga inicial
    ----------------------------------------- */
    cargarResumen();
    cargarUltimasLecturas();
    cargarHistorial();

});
