<?php
session_start();
$conexion = new mysqli("localhost", "root", "", "pasteleria");
if ($conexion->connect_error) {
    http_response_code(500);
    echo "Error de conexión";
    exit;
}

if (isset($_POST['producto_id'])) {
    $producto_id = (int)$_POST['producto_id'];

    $resultado = $conexion->query("SELECT * FROM productos WHERE id = $producto_id");
    if ($producto = $resultado->fetch_assoc()) {
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
