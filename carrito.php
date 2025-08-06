<?php
session_start();

// Conexión a PostgreSQL con variables de entorno
$conn = pg_connect("host=" . getenv("DB_HOST") . 
                   " dbname=" . getenv("DB_NAME") . 
                   " user=" . getenv("DB_USER") . 
                   " password=" . getenv("DB_PASS") . 
                   " port=" . getenv("DB_PORT"));

if (!$conn) {
    die("Error de conexión: " . pg_last_error());
}

// Inicializar carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto si viene de menu.html
if (isset($_POST['producto_id']) && !isset($_POST['eliminar'])) {
    $producto_id = (int)$_POST['producto_id'];
    $resultado = pg_query_params($conn, "SELECT * FROM productos WHERE id = $1", [$producto_id]);

    if ($producto = pg_fetch_assoc($resultado)) {
        $existe = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id'] == $producto_id) {
                $item['cantidad']++;
                $existe = true;
                break;
            }
        }

        if (!$existe) {
            $_SESSION['carrito'][] = [
                "id" => $producto['id'],
                "nombre" => $producto['nombre'],
                "precio" => $producto['precio'],
                "cantidad" => 1
            ];
        }
    }
}

// Eliminar producto
if (isset($_POST['eliminar'])) {
    $id_eliminar = (int)$_POST['eliminar'];
    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['id'] == $id_eliminar) {
            unset($_SESSION['carrito'][$key]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reordenar
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <h1>🛒 Carrito de Compras</h1>
        </div>
        <nav>
            <ul>
                <li><a href="menu.html">Menú</a></li>
                <li><a href="checkout.php">Pagar</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="content-page">
    <div class="container">
        <h2>Tu Carrito</h2>

        <?php if (!empty($_SESSION['carrito'])): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['carrito'] as $item): 
                        $subtotal = $item['precio'] * $item['cantidad'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                        <td>$<?php echo number_format($item['precio'], 2); ?> MXN</td>
                        <td><?php echo $item['cantidad']; ?></td>
                        <td>$<?php echo number_format($subtotal, 2); ?> MXN</td>
                        <td>
                            <form method="POST" action="carrito.php" onsubmit="return confirm('¿Eliminar este producto del carrito?');">
                                <input type="hidden" name="eliminar" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn-eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>Total: $<?php echo number_format($total, 2); ?> MXN</h3>
            <a href="checkout.php" class="btn">Proceder al Pago</a>
            <a href="menu.html" class="btn-secondary">Seguir Comprando</a>

        <?php else: ?>
            <p>Tu carrito está vacío.</p>
            <a href="menu.html" class="btn">Volver al Menú</a>
        <?php endif; ?>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; 2025 Pastelería Delicias. Todos los derechos reservados.</p>
    </div>
</footer>
</body>
</html>
