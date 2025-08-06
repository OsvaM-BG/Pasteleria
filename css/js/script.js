
document.addEventListener('DOMContentLoaded', () => {
    // Ejemplo de funcionalidad: Validar formulario de contacto
    const contactForm = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');

    if (contactForm) {
        contactForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Evita el envío por defecto del formulario

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();

            if (name === '' || email === '' || message === '') {
                displayMessage('Por favor, completa todos los campos obligatorios.', 'error');
            } else if (!isValidEmail(email)) {
                displayMessage('Por favor, ingresa un email válido.', 'error');
            } else {
                // Aquí iría la lógica para enviar el formulario (ej. a un servidor)
                // Por ahora, solo simularemos un envío exitoso
                displayMessage('¡Mensaje enviado con éxito! Nos pondremos en contacto pronto.', 'success');
                contactForm.reset(); // Limpia el formulario
            }
        });
    }

    function isValidEmail(email) {
        // Expresión regular simple para validar email
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function displayMessage(message, type) {
        formMessage.textContent = message;
        formMessage.className = `form-message ${type}`; // Añade clase 'success' o 'error'
        formMessage.style.display = 'block'; // Muestra el mensaje

        // Opcional: Ocultar el mensaje después de unos segundos
        setTimeout(() => {
            formMessage.style.display = 'none';
        }, 5000);
    }

    // Puedes añadir más funcionalidades aquí, como:
    // - Carrusel de imágenes en la página de inicio
    // - Animaciones con scroll
    // - Efectos visuales al pasar el ratón por los productos
    // - Filtrado de productos en la página de menú
});