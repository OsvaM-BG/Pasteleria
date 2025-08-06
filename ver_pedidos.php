<?php
// Conexión a PostgreSQL con variables de entorno
$conn = pg_connect("host=" . getenv("DB_HOST") . 
                   " dbname=" . getenv("DB_NAME") . 
                   " user=" . getenv("DB_USER") . 
                   " password=" . getenv("DB_PASS") . 
                   " port=" . getenv("DB_PORT"));

if (!$conn) {
    die("Error en la conexión: " . pg_last_error());
}

// Variables de filtro
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$metodo_pago = isset($_GET['metodo_pago']) ? $_GET['metodo_pago'] : '';

// Construir consulta SQL con filtros
$sql = "SELECT * FROM pedidos WHERE 1=1";
$params = [];
$i = 1;

if ($fecha_inicio && $fecha_fin) {
    $sql .= " AND DATE(fecha) BETWEEN $" . $i++ . " AND $" . $i++;
    $params[] = $fecha_inicio;
    $params[] = $fecha_fin;
}
if ($metodo_pago) {
    $sql .= " AND metodo_pago = $" . $i++;
    $params[] = $metodo_pago;
}

$sql .= " ORDER BY fecha DESC";
$resultado = pg_query_params($conn, $sql, $params);
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
            <input type="date" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>">

            <label>Fecha fin:</label>
            <input type="date" name="fecha_fin" value="<?php echo $fecha_fin; ?>">

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

    <?php if (pg_num_rows($resultado) > 0): ?>
        <?php while ($pedido = pg_fetch_assoc($resultado)): ?>
            <table>
                <tr>
                    <th>ID Pedido</th>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th>Método de Pago</th>
                    <th>Fecha</th>
                </tr>
                <tr>
                    <td><?php echo $pedido['id']; ?></td>
                    <td><?php echo htmlspecialchars($pedido['cliente_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($pedido['cliente_email']); ?></td>
                    <td><?php echo $pedido['metodo_pago']; ?></td>
                    <td><?php echo $pedido['fecha']; ?></td>
                </tr>
            </table>

            <div class="detalle">
                <h3>Detalle del Pedido:</h3>
                <table>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                    </tr>
                    <?php
                    $pedido_id = $pedido['id'];
                    $detalle_sql = "SELECT pd.cantidad, p.nombre 
                                    FROM pedido_detalle pd 
                                    JOIN productos p ON pd.producto_id = p.id 
                                    WHERE pd.pedido_id = $1";
                    $detalle_resultado = pg_query_params($conn, $detalle_sql, [$pedido_id]);

                    while ($detalle = pg_fetch_assoc($detalle_resultado)):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detalle['nombre']); ?></td>
                            <td><?php echo $detalle['cantidad']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No hay pedidos con los filtros aplicados.</p>
    <?php endif; ?>

    <a href="menu.html">Volver al Menú</a>
</body>
</html>
