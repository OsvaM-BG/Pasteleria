let carrito = [];

function agregarAlCarrito(nombre, precio) {
    const producto = carrito.find(item => item.nombre === nombre);
    if (producto) {
        producto.cantidad++;
    } else {
        carrito.push({ nombre, precio, cantidad: 1 });
    }
    mostrarCarrito();
}

function mostrarCarrito() {
    let carritoDiv = document.getElementById("carrito");
    carritoDiv.innerHTML = "";

    let total = 0;
    carrito.forEach(item => {
        total += item.precio * item.cantidad;
        carritoDiv.innerHTML += `
            <p>${item.nombre} x${item.cantidad} - $${item.precio * item.cantidad} 
            <button onclick="eliminarDelCarrito('${item.nombre}')">X</button></p>
        `;
    });

    carritoDiv.innerHTML += `<h4>Total: $${total}</h4>
    <button onclick="abrirPago(${total})">Proceder al Pago</button>`;
}

function eliminarDelCarrito(nombre) {
    carrito = carrito.filter(item => item.nombre !== nombre);
    mostrarCarrito();
}

function abrirPago(total) {
    document.getElementById("modalPago").style.display = "block";
    document.getElementById("totalPago").value = total;
}
