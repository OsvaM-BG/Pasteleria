<?php
session_start();
require 'db.php';

if (isset($_POST['producto_id'])) {
    $producto_id = (int)$_POST['producto_id'];

    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch();

    if ($producto) {
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
