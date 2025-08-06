<?php
require 'db.php';

// Variables de filtro
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin    = $_GET['fecha_fin'] ?? '';
$metodo_pago  = $_GET['metodo_pago'] ?? '';

// Construcción dinámica de la consulta
$sql = "SELECT * FROM pedidos WHERE 1=1";
$params = [];

if ($fecha_inicio && $fecha_fin) {
    $sql .= " AND DATE(fecha) BETWEEN ? AND ?";
    $params[] = $fecha_inicio;
    $params[] = $fecha_fin;
}
if ($metodo_pago) {
    $sql .= " AND metodo_pago = ?";
    $params[] = $metodo_pago;
}

$sql .= " ORDER BY fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrador - Ver Pedidos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: left; }
        th { background: #f0f0f0; }
        h2 { color: #333; }
        .detalle { margin-left: 20px; }
        .filtros { margin-bottom: 20px; padding: 10px; background: #fff; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h2>📦 Pedidos Registrados</h2>

    <!-- Formulario de filtros -->
    <div class="filtros">
        <form method="GET" action="">
            <label>Fecha inicio:</label>
            <input type="date" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">

            <label>Fecha fin:</label>
            <input type="date" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">

            <label>Método de pago:</label>
            <select name="metodo_pago">
                <option value="">Todos</option>
                <option value="Efectivo" <?php if ($metodo_pago == "Efectivo") echo "selected"; ?>>Efectivo</option>
                <option value="Tarjeta" <?php if ($metodo_pago == "Tarjeta") echo "selected"; ?>>Tarjeta</option>
            </select>

            <button type="submit">Filtrar</button>
            <a href="ver_pedidos.php">Limpiar</a>
        </form>
    </div>

    <?php if ($pedidos): ?>
        <?php foreach ($pedidos as $pedido): ?>
            <table>
                <tr>
                    <th>ID Pedido</th>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th>Método de Pago</th>
                    <th>Total</th>
                    <th>Fecha</th>
                </tr>
                <tr>
                    <td><?php echo $pedido['id']; ?></td>
                    <td><?php echo htmlspecialchars($pedido['cliente_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($pedido['cliente_email']); ?></td>
                    <td><?php echo htmlspecialchars($pedido['metodo_pago']); ?></td>
                    <td>$<?php echo number_format($pedido['total'], 2); ?> MXN</td>
                    <td><?php echo $pedido['fecha']; ?></td>
                </tr>
            </table>

            <div class="detalle">
                <h3>Detalle del Pedido:</h3>
                <table>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                    <?php
                    $detalle_stmt = $pdo->prepare("SELECT pd.cantidad, pd.subtotal, p.nombre 
                                                   FROM pedido_detalle pd 
                                                   JOIN productos p ON pd.producto_id = p.id 
                                                   WHERE pd.pedido_id = ?");
                    $detalle_stmt->execute([$pedido['id']]);
                    $detalles = $detalle_stmt->fetchAll();

                    foreach ($detalles as $detalle):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detalle['nombre']); ?></td>
                            <td><?php echo $detalle['cantidad']; ?></td>
                            <td>$<?php echo number_format($detalle['subtotal'], 2); ?> MXN</td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay pedidos con los filtros aplicados.</p>
    <?php endif; ?>

    <a href="menu.html">Volver al Menú</a>
</body>
</html>

