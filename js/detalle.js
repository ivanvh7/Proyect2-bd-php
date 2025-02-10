document.getElementById("formComentario")?.addEventListener("submit", function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("guardar_comentario.php", { 
        method: "POST", 
        body: formData 
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error en la respuesta del servidor.");
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            let nuevoComentario = document.createElement("div");
            nuevoComentario.classList.add("border", "p-2", "mb-2");
            nuevoComentario.innerHTML = `<strong>${data.nombre || "Anónimo"}</strong> - ${data.comentario}`;
            document.getElementById("comentarios").prepend(nuevoComentario);
            this.reset();
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => {
        console.error("Error en la solicitud AJAX:", error);
        alert("Hubo un problema al enviar el comentario. Revisa la consola para más detalles.");
    });
});
