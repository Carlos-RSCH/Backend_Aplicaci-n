<?php
// backend/api/config/db.php

// Si existen variables de entorno (Render), se usan.
// Si NO existen (localhost), se usan las credenciales locales.

$host = getenv("DB_HOST") ?: "bdf92alcsn8mf8okr6eq-mysql.services.clever-cloud.com";
$db_name = getenv("DB_NAME") ?: "bdf92alcsn8mf8okr6eq";
$username = getenv("DB_USER") ?: "ukwopldsfmnymb8x";
$password = getenv("DB_PASS") ?: "42t2oHCABpJ31ookbiWV";

try {
    $dsn = "mysql:host={$host};dbname={$db_name};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $conn = new PDO($dsn, $username, $password, $options);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Error de conexión a la base de datos."]);
    exit;
}
