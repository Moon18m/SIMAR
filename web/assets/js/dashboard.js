// Variables para almacenar las gráficas
let graficaTemperatura = null;
let graficaHumedad = null;


// Cargar toda la información del dashboard
async function cargarDashboard() {

    const btn = document.getElementById("btnActualizar");
    btn.disabled = true;

    try {

        const respuesta = await fetch("../controllers/dashboardController.php");

        if (!respuesta.ok) throw new Error(`Error del servidor: ${respuesta.status}`);

        const datos = await respuesta.json();

        if (!datos.success) {
            alert(datos.mensaje);
            return;
        }

        actualizarCards(datos.data);
        actualizarEstados(datos.data.estados);
        cargarTablaAlertas(datos.data.ultimasAlertas);
        cargarTablaProductos(datos.data.productosProximos);
        cargarGraficaTemperatura(datos.data.graficaTemperatura);
        cargarGraficaHumedad(datos.data.graficaHumedad);

    } catch (error) {

        console.error("Error:", error);
        alert(error.message);

    } finally {

        btn.disabled = false;

    }

}

// Actualizar las tarjetas superiores
function actualizarCards(datos) {

    document.getElementById("temperaturaActual").textContent =
        datos.temperatura.valor + " °C";

    document.getElementById("humedadActual").textContent =
        datos.humedad.valor + " %";

    document.getElementById("totalProductos").textContent =
        datos.productos;

    document.getElementById("alertasActivas").textContent =
        datos.alertas;

}


// Actualiza los indicadores de estado del sistema
function actualizarEstados(estados) {

    const mapa = {
        esp32: "estadoESP",
        bd: "estadoBD",
        ia: "estadoIA",
        sensores: "estadoSensores"
    };

    for (const clave in mapa) {

        const elemento = document.getElementById(mapa[clave]);

        elemento.classList.remove("ok", "error");
        elemento.classList.add(estados[clave]); // "ok" o "error"

    }

}

// Muestra las últimas alertas en la tabla
function cargarTablaAlertas(alertas) {
    const tabla = document.getElementById("tablaAlertas");
    tabla.innerHTML = "";

    if (alertas.length === 0) {
        const tr = document.createElement("tr");
        const td = document.createElement("td");
        td.colSpan = 3;
        td.textContent = "No hay alertas registradas.";
        tr.appendChild(td);
        tabla.appendChild(tr);
        return;
    }

    alertas.forEach(alerta => {
        const tr = document.createElement("tr");
        tr.appendChild(crearCelda(alerta.fecha_hora));
        tr.appendChild(crearCelda(alerta.mensaje));
        tr.appendChild(crearCelda(alerta.estado));
        tabla.appendChild(tr);
    });

}

// Muestra los productos con menor vida útil restante
function cargarTablaProductos(productos) {

    const tabla = document.getElementById("tablaProductos");

    tabla.innerHTML = "";

    if (productos.length === 0) {
        const tr = document.createElement("tr");
        const td = document.createElement("td");
        td.colSpan = 3;
        td.textContent = "No hay productos registrados.";
        tr.appendChild(td);
        tabla.appendChild(tr);
        return;
    }

    productos.forEach(producto => {

        const tr = document.createElement("tr");
        tr.appendChild(crearCelda(producto.producto));
        tr.appendChild(crearCelda(producto.vencimiento));
        tr.appendChild(crearCelda(producto.dias));
        tabla.appendChild(tr);
        

    });


}

document.addEventListener("DOMContentLoaded", () => {

    cargarDashboard();

})

// Dibuja la gráfica de temperatura
function cargarGraficaTemperatura(datos) {

    const ctx = document.getElementById("graficaTemperatura");

    const horas = datos.map(item => item.hora);

    const valores = datos.map(item => Number(item.valor));

    // Si ya existe una gráfica la eliminamos antes de crear otra
    if (graficaTemperatura) {
        graficaTemperatura.destroy();
    }

    graficaTemperatura = new Chart(ctx, {

        type: "line",

        data: {

            labels: horas,

            datasets: [{

                label: "Temperatura (°C)",

                data: valores,

                borderWidth: 2,

                tension: 0.3,

                fill: false

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {

                    display: false

                }

            }

        }

    });

}

// Dibuja la gráfica de humedad
function cargarGraficaHumedad(datos) {

    const ctx = document.getElementById("graficaHumedad");

    const horas = datos.map(item => item.hora);

    const valores = datos.map(item => Number(item.valor));

    if (graficaHumedad) {
        graficaHumedad.destroy();
    }

    graficaHumedad = new Chart(ctx, {

        type: "line",

        data: {

            labels: horas,

            datasets: [{

                label: "Humedad (%)",

                data: valores,

                borderWidth: 2,

                tension: 0.3,

                fill: false

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {

                    display: false

                }

            }

        }

    });

}

function crearCelda(texto) {
    const td = document.createElement("td");
    td.textContent = texto;
    return td;
}

// Botón para actualizar el dashboard


setInterval(cargarDashboard, 15000);