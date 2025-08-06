<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit;
}

$nombre_cliente = $_POST['nombre_cliente'];
$email_cliente  = $_POST['email_cliente'];
$metodo_pago    = $_POST['metodo_pago'];
$total          = $_POST['total'];

// Insertar pedido
$stmt = $pdo->prepare("INSERT INTO pedidos (cliente_nombre, cliente_email, metodo_pago, fecha) 
                       VALUES (?, ?, ?, NOW()) RETURNING id");
$stmt->execute([$nombre_cliente, $email_cliente, $metodo_pago]);
$pedido_id = $stmt->fetch()['id'];

// Insertar detalles
foreach ($_SESSION['carrito'] as $item) {
    $stmt_detalle = $pdo->prepare("INSERT INTO pedido_detalle (pedido_id, producto_id, cantidad) 
                                   VALUES (?, ?, ?)");
    $stmt_detalle->execute([$pedido_id, $item['id'], $item['cantidad']]);
}

$productos_comprados = $_SESSION['carrito'];
$_SESSION['carrito'] = [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido Confirmado</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <h1>✅ Pedido Confirmado</h1>
        </div>
        <nav>
            <ul>
                <li><a href="menu.html">Menú</a></li>
                <li><a href="ver_pedidos.php">Panel Admin</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="content-page">
    <div class="container">
        <h2>¡Gracias por tu compra, <?php echo htmlspecialchars($nombre_cliente); ?>!</h2>
        <p>Tu pedido ha sido registrado con éxito.</p>
        <p><strong>Método de pago:</strong> <?php echo $metodo_pago; ?></p>
        <p><strong>Total:</strong> $<?php echo number_format($total, 2); ?> MXN</p>
        <p>En breve recibirás un correo de confirmación en <strong><?php echo htmlspecialchars($email_cliente); ?></strong>.</p>

        <h3>Detalle de tu compra:</h3>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos_comprados as $prod): ?>
                <tr>
                    <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                    <td><?php echo $prod['cantidad']; ?></td>
                    <td>$<?php echo number_format($prod['precio'] * $prod['cantidad'], 2); ?> MXN</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="menu.html" class="btn">Seguir Comprando</a>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; 2025 Pastelería Delicias. Todos los derechos reservados.</p>
    </div>
</footer>
</body>
</html>


