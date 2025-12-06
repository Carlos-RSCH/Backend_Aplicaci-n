<?php
// backend/api/contactos/index.php

// Importa configuraciones necesarias:
// - headers.php: cabeceras HTTP (JSON, CORS)
// - db.php: conexión a la base de datos
// - auth.php: funciones de autenticación (validar usuario)
require_once __DIR__ . "/../config/headers.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../helpers/auth.php";

// Obtiene los datos del usuario autenticado (ej. su ID)
$payload = require_user();
$user_id = $payload["user_id"];

// Prepara una consulta SQL para obtener todos los contactos
// que pertenecen al usuario autenticado, ordenados por fecha de creación (más recientes primero)
$sql = "SELECT * FROM contactos WHERE usuario_id = :uid ORDER BY fecha_creacion DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":uid", $user_id, PDO::PARAM_INT);
$stmt->execute();

// Recupera todos los resultados de la consulta en un arreglo
$contactos = $stmt->fetchAll();

// Devuelve la respuesta en formato JSON con:
// - ok: true (indica éxito)
// - data: lista de contactos del usuario
echo json_encode(["ok" => true, "data" => $contactos]);