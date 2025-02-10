document.addEventListener("DOMContentLoaded", function() {
    // Función para eliminar un elemento (producto, usuario o comentario)
    function eliminarElemento(tipo, id) {
        if (confirm("¿Estás seguro de que quieres eliminar este elemento?")) {
            fetch("listado_admin.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ [tipo]: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Eliminar el elemento de la tabla sin recargar la página
                    let elemento = document.getElementById(`${tipo}_${id}`);
                    if (elemento) {
                        elemento.remove();
                    }
                    location.reload(); // Recargar la página para actualizar la vista
                } else {
                    alert("Error al eliminar el elemento.");
                }
            })
            .catch(error => console.error("Error en la solicitud:", error));
        }
    }

    // Hacer la función accesible globalmente
    window.eliminarElemento = eliminarElemento;

    // Manejo del formulario de añadir producto
    const formProducto = document.getElementById("formProducto");
    const mensajeDiv = document.createElement("div");
    mensajeDiv.id = "mensaje";
    formProducto.appendChild(mensajeDiv); // Agregar mensaje al formulario

    formProducto.addEventListener("submit", function(event) {
        event.preventDefault(); // Evitar recarga de la página

        let formData = new FormData(formProducto);

        fetch("listado_admin.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                formProducto.reset(); // Limpiar formulario
                mensajeDiv.innerHTML = `<p style="color: green; font-weight: bold;">Producto agregado correctamente.</p>`;
                
                setTimeout(() => {
                    location.reload(); // Recargar la página después de agregar el producto
                }, 1000);
            } else {
                mensajeDiv.innerHTML = `<p style="color: red; font-weight: bold;">${data.error || "Error al agregar el producto."}</p>`;
                setTimeout(() => { mensajeDiv.innerHTML = ""; }, 3000);
            }
        })
        .catch(error => {
            console.error("Error en la solicitud:", error);
            mensajeDiv.innerHTML = `<p style="color: red; font-weight: bold;">Error en la solicitud.</p>`;
            setTimeout(() => { mensajeDiv.innerHTML = ""; }, 3000);
        });
    });
});
