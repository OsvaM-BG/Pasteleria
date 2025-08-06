<?php
// db.php - conexión PDO a PostgreSQL en Railway
try {
    $dsn = "pgsql:host=" . getenv("DB_HOST") .
           ";port=" . getenv("DB_PORT") .
           ";dbname=" . getenv("DB_NAME");
    $pdo = new PDO($dsn, getenv("DB_USER"), getenv("DB_PASS"), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
