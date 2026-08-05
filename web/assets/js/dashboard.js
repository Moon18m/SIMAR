// Variables para almacenar las gráficas
let graficaTemperatura = null;
let graficaHumedad = null;


// Carga toda la información del dashboard
async function cargarDashboard(esManual = false) {

    const btn = document.getElementById("btnActualizar");
    const icono = btn.querySelector("i");

    // Solo bloquea y anima el botón cuando
    // la actualización fue iniciada manualmente
    if (esManual) {
        btn.disabled = true;
        icono.classList.add("fa-spin");
    }

    try {

        const respuesta = await fetch("../controllers/dashboardController.php");

        if (!respuesta.ok) {
            throw new Error(
                `Error del servidor: ${respuesta.status}`
            );
        }

        const datos = await respuesta.json();

        if (!datos.success) {
            alert(datos.mensaje);
            return;
        }

        // Actualiza las tarjetas
        actualizarCards(datos.data);

        // Actualiza los estados del sistema
        actualizarEstados(datos.data.estados);

        // Actualiza la tabla de alertas
        cargarTablaAlertas(datos.data.ultimasAlertas);

        // Actualiza la tabla de productos próximos a vencer
        cargarTablaProductos(datos.data.productosProximos);

        // Actualiza la gráfica de temperatura
        cargarGraficaTemperatura(
            datos.data.graficaTemperatura
        );

        // Actualiza la gráfica de humedad
        cargarGraficaHumedad(
            datos.data.graficaHumedad
        );

    } catch (error) {

        console.error("Error:", error);

        alert(
            "No fue posible cargar la información del dashboard."
        );

    } finally {

        // Reactiva el botón únicamente si fue
        // una actualización manual
        if (esManual) {

            btn.disabled = false;

            icono.classList.remove("fa-spin");

        }

    }

}


// Actualiza las tarjetas superiores
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

        const elemento =
            document.getElementById(mapa[clave]);

        if (!elemento) {
            continue;
        }

        elemento.classList.remove(
            "ok",
            "error"
        );

        elemento.classList.add(
            estados[clave]
        );

    }

}


// Muestra las últimas alertas
function cargarTablaAlertas(alertas) {

    const tabla =
        document.getElementById("tablaAlertas");

    tabla.innerHTML = "";

    if (alertas.length === 0) {

        const tr =
            document.createElement("tr");

        const td =
            document.createElement("td");

        td.colSpan = 3;

        td.textContent =
            "No hay alertas registradas.";

        tr.appendChild(td);

        tabla.appendChild(tr);

        return;
    }

    alertas.forEach(alerta => {

        const tr =
            document.createElement("tr");

        tr.appendChild(
            crearCelda(alerta.fecha_hora)
        );

        tr.appendChild(
            crearCelda(alerta.mensaje)
        );

        tr.appendChild(
            crearCelda(alerta.estado)
        );

        tabla.appendChild(tr);

    });

}


// Muestra los productos con menor vida útil restante
function cargarTablaProductos(productos) {

    const tabla =
        document.getElementById("tablaProductos");

    tabla.innerHTML = "";

    if (productos.length === 0) {

        const tr =
            document.createElement("tr");

        const td =
            document.createElement("td");

        td.colSpan = 2;

        td.textContent =
            "No hay productos registrados.";

        tr.appendChild(td);

        tabla.appendChild(tr);

        return;
    }

    productos.forEach(producto => {

        const tr =
            document.createElement("tr");

        tr.appendChild(
            crearCelda(producto.producto)
        );

        tr.appendChild(
            crearCelda(
                producto.horas + " horas"
            )
        );

        tabla.appendChild(tr);

    });

}


// Dibuja la gráfica de temperatura
function cargarGraficaTemperatura(datos) {

    const ctx =
        document.getElementById(
            "graficaTemperatura"
        );

    const horas =
        datos.map(item => item.hora);

    const valores =
        datos.map(item => Number(item.valor));

    // Elimina la gráfica anterior
    // antes de crear una nueva
    if (graficaTemperatura) {

        graficaTemperatura.destroy();

    }

    graficaTemperatura =
        new Chart(ctx, {

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

    const ctx =
        document.getElementById(
            "graficaHumedad"
        );

    const horas =
        datos.map(item => item.hora);

    const valores =
        datos.map(item => Number(item.valor));

    // Elimina la gráfica anterior
    // antes de crear una nueva
    if (graficaHumedad) {

        graficaHumedad.destroy();

    }

    graficaHumedad =
        new Chart(ctx, {

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


// Crea una celda de tabla de forma segura
function crearCelda(texto) {

    const td =
        document.createElement("td");

    td.textContent =
        texto ?? "";

    return td;

}


// Carga el dashboard cuando la página termina de cargar
document.addEventListener(
    "DOMContentLoaded",
    () => {

        cargarDashboard();

        // Configura el botón de actualizar
        const btn =
            document.getElementById(
                "btnActualizar"
            );

        if (btn) {

            btn.addEventListener(
                "click",
                () => {

                    cargarDashboard(true);

                }
            );

        }

    }
);


// Actualiza automáticamente el dashboard
// cada 15 segundos
setInterval(
    () => cargarDashboard(),
    15000
);