/**
 * Añade un efecto de animación a las tarjetas cuando la página carga.
 */
function animateCardsOnLoad() {
    const cards = document.querySelectorAll('.card');
    
    // 1. Ocultar tarjetas inicialmente (esto se hace en CSS para evitar parpadeos)
    
    // 2. Mostrar tarjetas con una transición escalonada
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('visible');
        }, index * 150); // El retraso entre cada tarjeta
    });
}

// Escuchar el evento 'DOMContentLoaded' que es más eficiente que 'load'
window.addEventListener('DOMContentLoaded', animateCardsOnLoad);

/**
 * Esta función estaba en tu código original pero no se usaba.
 * La guardo aquí por si la necesitas en el futuro.
 * * @param {string} oficio - El nombre del oficio seleccionado.
 */
function selectOficio(oficio) {
    alert(`Has seleccionado: ${oficio}`);
    console.log(`Oficio seleccionado: ${oficio}`);
}