// Solicita confirmación antes de enviar el formulario de eliminación.
document.querySelectorAll(".form-eliminar").forEach(function (formulario) {
    formulario.addEventListener("submit", function (evento) {
        const nombre = formulario.dataset.nombre || "este usuario";
        const confirmado = window.confirm("¿Deseas eliminar a " + nombre + "? Esta acción no se puede deshacer.");

        if (!confirmado) {
            evento.preventDefault();
        }
    });
});
