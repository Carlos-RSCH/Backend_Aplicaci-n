<?php
// backend/api/auth/perfil.php

// Importa configuraciones necesarias:
// - headers.php: define cabeceras HTTP (ej. JSON, CORS)
// - db.php: conexión a la base de datos
// - auth.php: funciones de autenticación para validar al usuario
require_once __DIR__ . "/../config/headers.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../helpers/auth.php";

// Obtiene los datos del usuario autenticado (ej. su ID) a través de la función require_user()
$payload = require_user();
$user_id = $payload["user_id"];

// Prepara una consulta SQL para buscar al usuario en la base de datos por su ID
$sql = "SELECT id, nombre_de_usuario, fecha_registro FROM usuarios WHERE id = :id LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch();

// Si no se encuentra el usuario, devuelve error 404 (no encontrado)
if (!$user) {
    http_response_code(404);
    echo json_encode(["ok" => false, "message" => "Usuario no encontrado."]);
    exit;
}

// Si se encuentra, devuelve los datos del usuario en formato JSON
echo json_encode(["ok" => true, "user" => $user]);