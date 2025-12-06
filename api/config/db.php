<?php
// backend/api/config/db.php

// Datos de conexión a la base de datos
$host = "bdf92alcsn8mf8okr6eq-mysql.services.clever-cloud.com";       // Servidor donde está la base de datos
$db_name = "bdf92alcsn8mf8okr6eq";    // Nombre de la base de datos
$username = "ukwopldsfmnymb8x";        // Usuario de la base de datos
$password = "42t2oHCABpJ31ookbiWV";        // Contraseña del usuario

try {
    // Construye el DSN (Data Source Name) con los parámetros de conexión
    $dsn = "mysql:host={$host};dbname={$db_name};charset=utf8mb4";

    // Opciones de configuración para PDO:
    // - ERRMODE_EXCEPTION: lanza excepciones si ocurre un error
    // - FETCH_ASSOC: devuelve resultados como arreglos asociativos
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    // Crea la conexión a la base de datos usando PDO
    $conn = new PDO($dsn, $username, $password, $options);

} catch (PDOException $e) {
    // Si ocurre un error al conectar, devuelve código 500 (error interno)
    // y un mensaje en formato JSON
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Error de conexión a la base de datos."]);
    exit;
}