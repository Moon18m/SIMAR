// Variables para almacenar las gráficas
let graficaTemperatura = null;
let graficaHumedad = null;


// Cargar toda la información del dashboard
async function cargarDashboard() {

    try {

        const respuesta = await fetch("../controllers/dashboardController.php");

        const datos = await respuesta.json();

        if (!datos.success) {

            alert(datos.mensaje);

            return;

        }

        actualizarCards(datos.data);

        cargarTablaAlertas(datos.data.ultimasAlertas);

        cargarTablaProductos(datos.data.productosProximos);

        cargarGraficaTemperatura(datos.data.graficaTemperatura);

        cargarGraficaHumedad(datos.data.graficaHumedad);

    } catch (error) {

        console.error("Error:", error);

        alert(error.message);



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

// Muestra las últimas alertas en la tabla
function cargarTablaAlertas(alertas) {

    const tabla = document.getElementById("tablaAlertas");

    tabla.innerHTML = "";

    if (alertas.length === 0) {

        tabla.innerHTML = `
            <tr>
                <td colspan="3">
                    No hay alertas registradas.
                </td>
            </tr>
        `;

        return;

    }

    alertas.forEach(alerta => {

        tabla.innerHTML += `

            <tr>

                <td>${alerta.fecha_hora}</td>

                <td>${alerta.mensaje}</td>

                <td>${alerta.estado}</td>

            </tr>

        `;

    });

}

// Muestra los productos con menor vida útil restante
function cargarTablaProductos(productos) {

    const tabla = document.getElementById("tablaProductos");

    tabla.innerHTML = "";

    if (productos.length === 0) {

        tabla.innerHTML = `
            <tr>
                <td colspan="3">
                    No hay productos registrados.
                </td>
            </tr>
        `;

        return;

    }

    productos.forEach(producto => {

        tabla.innerHTML += `

            <tr>

                <td>${producto.producto}</td>

                <td>${producto.vencimiento}</td>

                <td>${producto.dias}</td>

            </tr>

        `;

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

// Botón para actualizar el dashboard
document.getElementById("btnActualizar").addEventListener("click", () => {

    cargarDashboard();

});