let inventario = [];

document.addEventListener("DOMContentLoaded", () => {
    cargarInventario();
});

function cargarInventario() {

    const cargando = document.getElementById("cargandoInventario");
    const btn = document.getElementById("btnActualizar");

    cargando.style.display = "flex";
    btn.disabled = true;

    fetch("../controllers/inventario/inventarioController.php")

        .then(response => {

            if (!response.ok) {
                throw new Error("Error al cargar el inventario.");
            }

            return response.json();

        })

        .then(datos => {

            inventario = datos;

            actualizarTabla();

        })

        .catch(error => {

            console.error(error);

        })

        .finally(() => {

            cargando.style.display = "none";
            btn.disabled = false;

        });

}

function actualizarTabla() {

    let datos = [...inventario];

    // Buscar
    const texto = document.getElementById("buscarProducto").value.toLowerCase();

    if (texto !== "") {

        datos = datos.filter(producto =>
            producto.nombre.toLowerCase().includes(texto)
        );

    }

    // Filtrar por estado
    const filtro = document.getElementById("estado").value;

    if (filtro !== "todos") {

        datos = datos.filter(producto => {

            const vidaUtil = Number(producto.vida_util_calculada);

            if (vidaUtil >= 48) {

                return filtro === "vigente";

            }

            if (vidaUtil >= 20) {

                return filtro === "vencer";

            }

            return filtro === "vencido";

        });

    }

    // Ordenar
    const orden = document.getElementById("orden").value;

    switch (orden) {

        case "nombre":

            datos.sort((a, b) => a.nombre.localeCompare(b.nombre));

            break;

        case "cantidad":

            datos.sort((a, b) => b.cantidad - a.cantidad);

            break;

        case "vida":

            datos.sort((a, b) => b.vida_util_calculada - a.vida_util_calculada);

            break;

        case "fecha":

            datos.sort((a, b) => new Date(b.fecha_ingreso) - new Date(a.fecha_ingreso));

            break;

    }

    llenarTabla(datos);

    actualizarResumen(datos);

}

function llenarTabla(datos) {

    const tabla = document.getElementById("tablaInventario");

    tabla.innerHTML = "";

    datos.forEach(producto => {

        const vidaUtil = Number(producto.vida_util_calculada);

        let estado = "";
        let claseEstado = "";

        if (vidaUtil >= 48) {

            estado = "Vigente";
            claseEstado = "success";

        } else if (vidaUtil >= 20) {

            estado = "Por vencer";
            claseEstado = "warning";

        } else {

            estado = "Vencido";
            claseEstado = "danger";

        }

        tabla.innerHTML += `
        <tr>

            <td>${producto.nombre}</td>

            <td>${producto.cantidad}</td>

            <td>${producto.fecha_ingreso}</td>

            <td>${vidaUtil} horas</td>

            <td>
                <span class="${claseEstado}">
                    ${estado}
                </span>
            </td>

            <td>

                <button class="btn-ver" data-id="${producto.id_inventario}" title="Ver">
                    <i class="fa-solid fa-eye"></i>
                </button>

                <button class="btn-editar" data-id="${producto.id_inventario}" title="Editar">
                    <i class="fa-solid fa-pen"></i>
                </button>

                <button class="btn-eliminar" data-id="${producto.id_inventario}" title="Eliminar">
                    <i class="fa-solid fa-trash"></i>
                </button>

            </td>

        </tr>
        `;

    });

}

function actualizarResumen(datos) {

    document.getElementById("totalProductos").textContent = datos.length;

    let vigentes = 0;
    let vencer = 0;
    let vencidos = 0;

    datos.forEach(producto => {

        const vidaUtil = Number(producto.vida_util_calculada);

        if (vidaUtil >= 48) {

            vigentes++;

        } else if (vidaUtil >= 20) {

            vencer++;

        } else {

            vencidos++;

        }

    });

    document.getElementById("productosVigentes").textContent = vigentes;
    document.getElementById("productosVencer").textContent = vencer;
    document.getElementById("productosVencidos").textContent = vencidos;

}

document.getElementById("buscarProducto").addEventListener("keyup", actualizarTabla);

document.getElementById("estado").addEventListener("change", actualizarTabla);

document.getElementById("orden").addEventListener("change", actualizarTabla);

document.getElementById("tablaInventario").addEventListener("click", (e) => {

    const botonVer = e.target.closest(".btn-ver");

    if (botonVer) {

        const id = botonVer.dataset.id;

        fetch(`../controllers/inventario/verInventarioController.php?id=${id}`)

            .then(response => {

                if (!response.ok) {
                    throw new Error("Error al obtener el producto.");
                }

                return response.json();

            })

            .then(producto => {

                mostrarModalVer(producto);

            })

            .catch(error => {

                console.error(error);

                alert("No se pudo cargar la información del producto.");

            });

        return;
    }


    const botonEditar = e.target.closest(".btn-editar");

    if (botonEditar) {

        const id = botonEditar.dataset.id;

        abrirModalEditar(id);

    }

});

function mostrarModalVer(producto) {

    const contenedor = document.getElementById("detalleProducto");

    contenedor.innerHTML = `

        <div class="campo">
            <label>Producto</label>
            <span>${producto.nombre}</span>
        </div>

        <div class="campo">
            <label>Cantidad</label>
            <span>${producto.cantidad}</span>
        </div>

        <div class="campo">
            <label>Fecha de ingreso</label>
            <span>${producto.fecha_ingreso}</span>
        </div>

        <div class="campo">
            <label>Vida útil</label>
            <span>${producto.vida_util_calculada} horas</span>
        </div>

    `;

    document.getElementById("modalVer").classList.add("active");

}

document.getElementById("cerrarModalVer").addEventListener("click", () => {

    document.getElementById("modalVer").classList.remove("active");

});

function abrirModalEditar(id) {

    const producto = inventario.find(
        producto => Number(producto.id_inventario) === Number(id)
    );

    if (!producto) {
        alert("No se encontró el producto seleccionado.");
        return;
    }

    document.getElementById("editarIdInventario").value =
        producto.id_inventario;

    document.getElementById("editarNombre").value =
        producto.nombre;

    document.getElementById("editarCantidad").value =
        producto.cantidad;

    document.getElementById("editarFechaIngreso").value =
        formatearFechaParaInput(producto.fecha_ingreso);

    document.getElementById("editarVidaUtil").value =
        producto.vida_util_calculada;

    document.getElementById("modalEditar").classList.add("active");

}


function formatearFechaParaInput(fecha) {

    if (!fecha) {
        return "";
    }

    return fecha.replace(" ", "T").slice(0, 16);

}

function cerrarModalEditar() {

    document.getElementById("modalEditar").classList.remove("active");

}


document.getElementById("cancelarEdicion").addEventListener(
    "click",
    cerrarModalEditar
);

document.getElementById("formEditarInventario").addEventListener("submit", (e) => {

    e.preventDefault();

    const formulario = e.target;
    const datos = new FormData(formulario);

    fetch("../controllers/inventario/EditarInventarioController.php", {
        method: "POST",
        body: datos
    })

        .then(response => {

            if (!response.ok) {
                throw new Error("Error al actualizar el producto.");
            }

            return response.json();

        })

        .then(resultado => {

            if (!resultado.success) {
                throw new Error(resultado.message);
            }

            alert(resultado.message);

            cerrarModalEditar();

            cargarInventario();

        })

        .catch(error => {

            console.error(error);

            alert(error.message);

        });

});