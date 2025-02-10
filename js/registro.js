document.getElementById('registerForm').addEventListener('submit', function(event) {
    event.preventDefault();
    let formData = new FormData(this);
    let url = 'crear.php' + (window.location.search.includes('admin=1') ? '?admin=1' : '');

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        let messageElement = document.getElementById('registerMessage');
        if (data.success) {
            messageElement.innerText = data.success;
            messageElement.style.color = "green";

            // Redirigir al index.php después de 1.5 segundos
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
        } else {
            messageElement.innerText = data.error;
            messageElement.style.color = "red";
        }
    })
    .catch(error => console.error('Error:', error));
});
