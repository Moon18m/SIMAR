/**
 * camara.js
 * - El stream reintenta conectarse solo si falla (esto es necesario porque
 *   una imagen rota no se arregla sola).
 * - El estado de IA se refresca con el botón "Actualizar" (mismo patrón de
 *   refresco manual que ya usa el dashboard), no con polling automático.
 */

const INTERVALO_REINTENTO_STREAM_MS = 5000;

document.addEventListener("DOMContentLoaded", () => {
    inicializarStream();
    inicializarBotonActualizar();
});

function inicializarStream() {
    const img = document.getElementById("streamImg");
    const overlay = document.getElementById("overlayError");
    const estadoCamara = document.getElementById("estadoCamara");

    if (!img || !overlay) {
        return; // No hay dispositivo configurado; nada que vigilar.
    }

    const srcOriginal = img.src;

    img.addEventListener("load", () => {
        overlay.hidden = true;
        marcarEstado(estadoCamara, true);
    });


    img.addEventListener("error", () => {
        overlay.hidden = false;
        marcarEstado(estadoCamara, false);
        setTimeout(() => {
            img.src = srcOriginal + (srcOriginal.includes("?") ? "&" : "?") + "t=" + Date.now();
            // Si tras el reintento no hay error en X segundos, asumimos que reconectó
            setTimeout(() => {
                overlay.hidden = true;
                marcarEstado(estadoCamara, true);
            }, 20000);
        }, INTERVALO_REINTENTO_STREAM_MS);
    });
}

function marcarEstado(elemento, ok) {
    if (!elemento) return;
    elemento.classList.toggle("ok", ok);
    elemento.classList.toggle("error", !ok);
}

function inicializarBotonActualizar() {
    const boton = document.getElementById("btnActualizar");
    if (!boton) return;

    boton.addEventListener("click", async () => {
        const icono = boton.querySelector("i");
        boton.disabled = true;
        icono?.classList.add("fa-spin");

        await refrescarEstadoIA();

        icono?.classList.remove("fa-spin");
        boton.disabled = false;
    });

    // Carga inicial al entrar a la vista.
    refrescarEstadoIA();
}

async function refrescarEstadoIA() {
    const contenedor = document.getElementById("ultimaEjecucion");
    const estadoIA = document.getElementById("estadoIA");
    if (!contenedor) return;

    try {
        const respuesta = await fetch("../controllers/ObtenerEstadoIAController.php");
        const datos = await respuesta.json();

        if (!datos.ok) {
            marcarEstado(estadoIA, false);
            return;
        }

        pintarUltimaEjecucion(contenedor, datos.ejecucion);
        marcarEstado(estadoIA, datos.activo);
    } catch (error) {
        console.error("No se pudo refrescar el estado de IA:", error);
        marcarEstado(estadoIA, false);
    }
}

function pintarUltimaEjecucion(contenedor, ejecucion) {
    if (!ejecucion) {
        contenedor.innerHTML = '<p class="ultima-ejecucion__vacio">Aún no hay ejecuciones registradas.</p>';
        return;
    }

    const estadoClase = ejecucion.estado.toLowerCase();
    const detalleHtml = ejecucion.detalle
        ? `<p class="ultima-ejecucion__detalle">${escaparHtml(ejecucion.detalle)}</p>`
        : "";

    contenedor.innerHTML = `
        <p class="ultima-ejecucion__fecha">${escaparHtml(ejecucion.fecha_hora)}</p>
        <p class="ultima-ejecucion__estado ultima-ejecucion__estado--${estadoClase}">
            ${escaparHtml(ejecucion.estado)}
        </p>
        ${detalleHtml}
    `;
}

function escaparHtml(texto) {
    const div = document.createElement("div");
    div.textContent = texto;
    return div.innerHTML;
}
