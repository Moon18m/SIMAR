let alertas = [];

let paginaActual = 1;

const alertasPorPagina = 10;


// Carga todas las alertas desde el servidor
async function cargarAlertas() {

    mostrarCargando();

    try {

        const respuesta = await fetch(
            "../controllers/alertasController.php?accion=listar"
        );

        if (!respuesta.ok) {
            throw new Error("Error en la respuesta del servidor.");
        }

        const datos = await respuesta.json();

        if (!datos.success) {
            throw new Error(
                datos.mensaje || "No fue posible cargar las alertas."
            );
        }

        alertas = datos.data || [];

        paginaActual = 1;

        actualizarResumen();

        cargarTipos();

        aplicarFiltros();

        ocultarError();

    } catch (error) {

        console.error(error);

        mostrarError(
            "No fue posible cargar las alertas. Intenta nuevamente."
        );

    }

}


// Actualiza las tarjetas de resumen
function actualizarResumen() {

    const total = alertas.length;

    const activas = alertas.filter(
        alerta => alerta.estado === "Activa"
    ).length;

    const resueltas = alertas.filter(
        alerta => alerta.estado === "Resuelta"
    ).length;

    const fechaHoy = new Date()
        .toISOString()
        .split("T")[0];

    const hoy = alertas.filter(alerta =>
        alerta.fecha_hora.startsWith(fechaHoy)
    ).length;


    document.getElementById("totalAlertas").textContent =
        total;

    document.getElementById("alertasActivas").textContent =
        activas;

    document.getElementById("alertasAtendidas").textContent =
        resueltas;

    document.getElementById("alertasHoy").textContent =
        hoy;

}


// Carga los tipos de alerta disponibles en el filtro
function cargarTipos() {

    const select = document.getElementById("filtroTipo");

    const tipoSeleccionado = select.value;

    const tipos = [
        ...new Set(
            alertas.map(alerta => alerta.tipo)
        )
    ];

    select.innerHTML = `
        <option value="">Todos los tipos</option>
    `;

    tipos.forEach(tipo => {

        const opcion = document.createElement("option");

        opcion.value = tipo;

        opcion.textContent = tipo;

        select.appendChild(opcion);

    });

    select.value = tipoSeleccionado;

}


// Obtiene las alertas que cumplen los filtros
function aplicarFiltros() {

    const busqueda =
        document.getElementById("buscarAlerta")
        .value
        .toLowerCase()
        .trim();

    const estado =
        document.getElementById("filtroEstado")
        .value;

    const tipo =
        document.getElementById("filtroTipo")
        .value;

    const fechaDesde =
        document.getElementById("fechaDesde")
        .value;

    const fechaHasta =
        document.getElementById("fechaHasta")
        .value;


    const filtradas = alertas.filter(alerta => {

        const textoAlerta = (

            alerta.id_alerta +
            " " +
            alerta.tipo +
            " " +
            alerta.mensaje

        ).toLowerCase();


        const coincideBusqueda =
            !busqueda ||
            textoAlerta.includes(busqueda);


        const coincideEstado =
            !estado ||
            alerta.estado === estado;


        const coincideTipo =
            !tipo ||
            alerta.tipo === tipo;


        const fechaAlerta =
            alerta.fecha_hora.substring(0, 10);


        const coincideDesde =
            !fechaDesde ||
            fechaAlerta >= fechaDesde;


        const coincideHasta =
            !fechaHasta ||
            fechaAlerta <= fechaHasta;


        return (

            coincideBusqueda &&
            coincideEstado &&
            coincideTipo &&
            coincideDesde &&
            coincideHasta

        );

    });


    paginaActual = 1;

    mostrarTabla(filtradas);

}


// Muestra las alertas en la tabla
function mostrarTabla(alertasFiltradas) {

    const cuerpo =
        document.getElementById("cuerpoTablaAlertas");

    const estadoSinAlertas =
        document.getElementById("estadoSinAlertas");

    const paginacion =
        document.getElementById("paginacionAlertas");


    cuerpo.innerHTML = "";


    document.getElementById("cantidadResultados")
        .textContent = alertasFiltradas.length;


    if (alertasFiltradas.length === 0) {

        estadoSinAlertas.hidden = false;

        paginacion.hidden = true;

        return;

    }


    estadoSinAlertas.hidden = true;


    const totalPaginas =
        Math.ceil(
            alertasFiltradas.length /
            alertasPorPagina
        );


    if (paginaActual > totalPaginas) {
        paginaActual = totalPaginas;
    }


    const inicio =
        (paginaActual - 1) *
        alertasPorPagina;


    const fin =
        inicio +
        alertasPorPagina;


    const pagina =
        alertasFiltradas.slice(
            inicio,
            fin
        );


    pagina.forEach(alerta => {

        const fila =
            document.createElement("tr");


        fila.classList.add(
            "fila-alerta"
        );


        fila.dataset.alertaId =
            alerta.id_alerta;


        fila.innerHTML = `

            <td>
                ${escaparHTML(alerta.id_alerta)}
            </td>

            <td>
                ${escaparHTML(alerta.fecha_hora)}
            </td>

            <td>
                ${escaparHTML(alerta.tipo)}
            </td>

            <td>
                -
            </td>

            <td>
                ${escaparHTML(alerta.mensaje)}
            </td>

            <td>

                <span class="estado-alerta">
                    ${escaparHTML(alerta.estado)}
                </span>

            </td>

            <td class="acciones-tabla">

                <button
                    type="button"
                    class="accion-icono accion-icono--consultar"
                    data-action="consultar"
                    data-id="${alerta.id_alerta}"
                    aria-label="Consultar alerta"
                >
                    <i class="fa-solid fa-eye"></i>
                </button>

                ${
                    alerta.estado === "Activa"

                    ? `

                        <button
                            type="button"
                            class="accion-icono accion-icono--atender"
                            data-action="atender"
                            data-id="${alerta.id_alerta}"
                            aria-label="Marcar alerta como resuelta"
                        >
                            <i class="fa-solid fa-check"></i>
                        </button>

                    `

                    : ""

                }

            </td>

        `;


        cuerpo.appendChild(fila);

    });


    actualizarPaginacion(
        alertasFiltradas.length
    );

}


// Escapa texto para evitar insertar HTML directamente
function escaparHTML(texto) {

    const div =
        document.createElement("div");

    div.textContent =
        texto ?? "";

    return div.innerHTML;

}


// Actualiza los controles de paginación
function actualizarPaginacion(totalResultados) {

    const paginacion =
        document.getElementById("paginacionAlertas");

    const anterior =
        document.getElementById("paginaAnterior");

    const siguiente =
        document.getElementById("paginaSiguiente");

    const informacion =
        document.getElementById("informacionPagina");


    const totalPaginas =
        Math.ceil(
            totalResultados /
            alertasPorPagina
        );


    paginacion.hidden =
        totalPaginas <= 1;


    anterior.disabled =
        paginaActual <= 1;


    siguiente.disabled =
        paginaActual >= totalPaginas;


    informacion.textContent =
        `Página ${paginaActual} de ${totalPaginas}`;

}


// Resolver una alerta específica
async function resolverAlerta(idAlerta) {

    if (!confirm(
        "¿Deseas marcar esta alerta como resuelta?"
    )) {

        return;

    }


    try {

        const datosFormulario =
            new URLSearchParams();

        datosFormulario.append(
            "id_alerta",
            idAlerta
        );


        const respuesta = await fetch(
            "../controllers/alertasController.php?accion=resolver",
            {
                method: "POST",

                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded"
                },

                body: datosFormulario
            }
        );


        const datos =
            await respuesta.json();


        if (!datos.success) {

            throw new Error(
                datos.mensaje ||
                "No se pudo resolver la alerta."
            );

        }


        await cargarAlertas();


    } catch (error) {

        console.error(error);

        alert(
            error.message ||
            "No fue posible resolver la alerta."
        );

    }

}


// Consulta una alerta
function consultarAlerta(idAlerta) {

    const alerta =
        alertas.find(
            item =>
                String(item.id_alerta) ===
                String(idAlerta)
        );


    if (!alerta) {

        alert(
            "No se encontró la información de la alerta."
        );

        return;

    }


    alert(

        "ID: " + alerta.id_alerta +
        "\nTipo: " + alerta.tipo +
        "\nNivel: " + alerta.nivel +
        "\nEstado: " + alerta.estado +
        "\nFecha: " + alerta.fecha_hora +
        "\n\nMensaje:\n" + alerta.mensaje

    );

}


// Muestra mensaje de carga
function mostrarCargando() {

    const cuerpo =
        document.getElementById("cuerpoTablaAlertas");

    cuerpo.innerHTML = `

        <tr class="fila-mensaje">

            <td colspan="7">

                <i class="fa-solid fa-spinner fa-spin"></i>

                Cargando alertas...

            </td>

        </tr>

    `;

}


// Muestra el estado de error
function mostrarError(mensaje) {

    const estadoError =
        document.getElementById("estadoErrorAlertas");

    const mensajeError =
        document.getElementById("mensajeErrorAlertas");


    mensajeError.textContent =
        mensaje;

    estadoError.hidden =
        false;

}


// Oculta el estado de error
function ocultarError() {

    document.getElementById(
        "estadoErrorAlertas"
    ).hidden = true;

}


// Eventos de la vista
document.addEventListener(
    "DOMContentLoaded",
    () => {

        cargarAlertas();


        document
            .getElementById("btnActualizarAlertas")
            .addEventListener(
                "click",
                cargarAlertas
            );


        document
            .getElementById("formFiltrosAlertas")
            .addEventListener(
                "submit",
                event => {

                    event.preventDefault();

                    aplicarFiltros();

                }
            );


        document
            .getElementById("btnLimpiarFiltros")
            .addEventListener(
                "click",
                () => {

                    setTimeout(
                        aplicarFiltros,
                        0
                    );

                }
            );


        document
            .getElementById("cuerpoTablaAlertas")
            .addEventListener(
                "click",
                event => {

                    const boton =
                        event.target.closest(
                            "button[data-action]"
                        );


                    if (!boton) {
                        return;
                    }


                    const accion =
                        boton.dataset.action;


                    const idAlerta =
                        boton.dataset.id;


                    if (
                        accion === "atender"
                    ) {

                        resolverAlerta(
                            idAlerta
                        );

                    }


                    if (
                        accion === "consultar"
                    ) {

                        consultarAlerta(
                            idAlerta
                        );

                    }

                }
            );


        document
            .getElementById("paginaAnterior")
            .addEventListener(
                "click",
                () => {

                    if (paginaActual > 1) {

                        paginaActual--;

                        aplicarFiltros();

                    }

                }
            );


        document
            .getElementById("paginaSiguiente")
            .addEventListener(
                "click",
                () => {

                    const totalResultados =
                        alertas.length;


                    const totalPaginas =
                        Math.ceil(
                            totalResultados /
                            alertasPorPagina
                        );


                    if (
                        paginaActual <
                        totalPaginas
                    ) {

                        paginaActual++;

                        aplicarFiltros();

                    }

                }
            );

    }
);