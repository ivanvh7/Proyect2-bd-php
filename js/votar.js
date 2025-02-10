document.addEventListener("DOMContentLoaded", function () {
    // Permitir clic en la fila para abrir detalle.php
    document.querySelectorAll(".clickable-row").forEach(row => {
        row.addEventListener("click", function () {
            window.location.href = this.dataset.href;
        });
    });

    // Sistema de votación con estrellas
    document.querySelectorAll(".rating").forEach(function (ratingElement) {
        let productId = ratingElement.getAttribute("data-product-id");
        let averageRating = parseFloat(ratingElement.getAttribute("data-average")) || 0;

        // Aplicar estrellas doradas según la valoración promedio guardada
        applyStoredStars(ratingElement, averageRating);

        ratingElement.querySelectorAll(".star").forEach(star => {
            star.addEventListener("mouseover", function () {
                let value = this.getAttribute("data-value");
                highlightStars(ratingElement, value);
            });

            star.addEventListener("click", function (event) {
                event.stopPropagation();
                let value = this.getAttribute("data-value");
                setStars(ratingElement, value);
                sendVote(productId, value, ratingElement);
            });
        });

        ratingElement.addEventListener("mouseleave", function () {
            resetStars(ratingElement);
        });
    });

    function applyStoredStars(ratingElement, value) {
        value = Math.round(value); // Redondeamos la valoración
        ratingElement.querySelectorAll(".star").forEach(star => {
            let starValue = parseInt(star.getAttribute("data-value"), 10);
            if (starValue <= value) {
                star.classList.add("persist-selected");
            } else {
                star.classList.remove("persist-selected");
            }
        });
    }

    function highlightStars(ratingElement, value) {
        value = parseInt(value, 10);
        ratingElement.querySelectorAll(".star").forEach(star => {
            let starValue = parseInt(star.getAttribute("data-value"), 10);
            if (starValue <= value) {
                star.classList.add("hovered");
            } else {
                star.classList.remove("hovered");
            }
        });
    }

    function resetStars(ratingElement) {
        ratingElement.querySelectorAll(".star").forEach(star => {
            if (!star.classList.contains("persist-selected")) {
                star.classList.remove("hovered");
            }
        });
    }

    function setStars(ratingElement, value) {
        value = parseInt(value, 10);
        ratingElement.querySelectorAll(".star").forEach(star => {
            let starValue = parseInt(star.getAttribute("data-value"), 10);
            if (starValue <= value) {
                star.classList.add("persist-selected");
            } else {
                star.classList.remove("persist-selected");
            }
        });
    }

    function sendVote(productId, value, ratingElement) {
        let formData = new FormData();
        formData.append("producto_id", productId);
        formData.append("valor", value);

        fetch("update.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ratingElement.parentNode.querySelector(".rating-average").textContent = data.media;
                ratingElement.parentNode.querySelector(".rating-count").textContent = data.total;

                // Aplicar las estrellas guardadas después de votar
                applyStoredStars(ratingElement, data.media);

                alert("¡Voto registrado con éxito!");
            } else {
                alert(data.error);
            }
        })
        .catch(error => console.error("Error:", error));
    }
});
