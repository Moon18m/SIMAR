const datos = window.SIMAR_REPORTES || {};

const configuracionComun = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { intersect: false, mode: "index" },
    plugins: { legend: { display: false } },
    scales: {
        x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } },
        y: { beginAtZero: false }
    }
};

function crearGraficaLinea(id, etiquetas, valores, etiqueta) {
    const canvas = document.getElementById(id);
    if (!canvas || typeof Chart === "undefined") return;

    new Chart(canvas, {
        type: "line",
        data: {
            labels: etiquetas,
            datasets: [{ label: etiqueta, data: valores, tension: 0.3, fill: false, pointRadius: 2 }]
        },
        options: configuracionComun
    });
}

crearGraficaLinea("graficaTemperatura", datos.temperatura?.etiquetas || [], datos.temperatura?.valores || [], "Temperatura °C");
crearGraficaLinea("graficaHumedad", datos.humedad?.etiquetas || [], datos.humedad?.valores || [], "Humedad %");

if (document.getElementById("graficaComparacion") && typeof Chart !== "undefined") {
    new Chart(document.getElementById("graficaComparacion"), {
        type: "bar",
        data: {
            labels: ["Temperatura", "Humedad"],
            datasets: [
                { label: "Mínimo", data: datos.comparacion?.minimo || [] },
                { label: "Máximo", data: datos.comparacion?.maximo || [] },
                { label: "Promedio", data: datos.comparacion?.promedio || [] }
            ]
        },
        options: { ...configuracionComun, plugins: { legend: { display: true } } }
    });
}

if (document.getElementById("graficaAlertas") && typeof Chart !== "undefined") {
    new Chart(document.getElementById("graficaAlertas"), {
        type: "bar",
        data: {
            labels: datos.alertas?.etiquetas || [],
            datasets: [{ label: "Alertas", data: datos.alertas?.valores || [] }]
        },
        options: configuracionComun
    });
}

const periodo = document.getElementById("periodo");
const fechaInicial = document.getElementById("fecha_inicial");
const fechaFinal = document.getElementById("fecha_final");

function formatearFecha(fecha) {
    return fecha.toISOString().slice(0, 10);
}

periodo?.addEventListener("change", () => {
    if (periodo.value === "personalizado") return;

    const hoy = new Date();
    const inicio = new Date(hoy);

    if (periodo.value === "semanal") inicio.setDate(hoy.getDate() - 6);
    if (periodo.value === "mensual") inicio.setDate(1);

    fechaInicial.value = formatearFecha(inicio);
    fechaFinal.value = formatearFecha(hoy);
});

document.getElementById("formFiltros")?.addEventListener("submit", (evento) => {
    if (fechaInicial.value && fechaFinal.value && fechaInicial.value > fechaFinal.value) {
        evento.preventDefault();
        alert("La fecha inicial no puede ser posterior a la fecha final.");
    }
});

document.getElementById("btnImprimir")?.addEventListener("click", () => window.print());
