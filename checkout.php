<?php
session_start();
require 'db.php';

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit;
}

$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Compra</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>💳 Finalizar Compra</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="menu.html">Menú</a></li>
                    <li><a href="carrito.php">Carrito</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="content-page">
        <div class="container">
            <h2>Datos de Pago</h2>
            <p>Total a pagar: <strong>$<?php echo number_format($total, 2); ?> MXN</strong></p>

            <form action="procesar_pago.php" method="POST" class="checkout-form">
                <label>Nombre completo:</label>
                <input type="text" name="nombre_cliente" required>

                <label>Email de contacto:</label>
                <input type="email" name="email_cliente" required>

                <label>Método de pago:</label>
                <select name="metodo_pago" id="metodo_pago" required>
                    <option value="">Seleccionar</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta">Tarjeta</option>
                </select>

                <div id="datos_tarjeta" style="display: none;">
                    <label>Número de Tarjeta:</label>
                    <input type="text" name="numero_tarjeta" maxlength="16">

                    <label>Fecha de Expiración:</label>
                    <input type="month" name="expiracion">

                    <label>CVV:</label>
                    <input type="text" name="cvv" maxlength="3">
                </div>

                <input type="hidden" name="total" value="<?php echo $total; ?>">

                <button type="submit" class="btn">Confirmar Pedido</button>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Pastelería Delicias. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        // Mostrar campos de tarjeta solo si elige "Tarjeta"
        document.getElementById('metodo_pago').addEventListener('change', function() {
            var datosTarjeta = document.getElementById('datos_tarjeta');
            datosTarjeta.style.display = this.value === 'Tarjeta' ? 'block' : 'none';
        });
    </script>
</body>
</html>

