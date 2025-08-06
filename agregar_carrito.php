<?php
session_start();

// Conexión a PostgreSQL con variables de entorno (Render/Railway)
$conn = pg_connect("host=" . getenv("DB_HOST") . 
                   " dbname=" . getenv("DB_NAME") . 
                   " user=" . getenv("DB_USER") . 
                   " password=" . getenv("DB_PASS") . 
                   " port=" . getenv("DB_PORT"));

if (!$conn) {
    http_response_code(500);
    echo "Error de conexión: " . pg_last_error();
    exit;
}

if (isset($_POST['producto_id'])) {
    $producto_id = (int)$_POST['producto_id'];

    // Consulta segura con parámetros
    $resultado = pg_query_params($conn, "SELECT * FROM productos WHERE id = $1", [$producto_id]);

    if ($producto = pg_fetch_assoc($resultado)) {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

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

        echo "Producto agregado";
        exit;
    }
}

http_response_code(400);
echo "Producto no válido";
?>
