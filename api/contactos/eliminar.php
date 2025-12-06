<?php
// backend/api/contactos/eliminar.php

// Importa configuraciones necesarias:
// - headers.php: cabeceras HTTP (JSON, CORS)
// - db.php: conexión a la base de datos
// - auth.php: funciones de autenticación (validar usuario)
require_once __DIR__ . "/../config/headers.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../helpers/auth.php";

// Verifica que la petición sea de tipo DELETE (eliminar recurso)
// Si no lo es, devuelve error 405 (método no permitido)
if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    http_response_code(405);
    echo json_encode(["ok" => false, "message" => "Método no permitido."]);
    exit;
}

// Obtiene los datos del usuario autenticado (ej. su ID)
$payload = require_user();
$user_id = $payload["user_id"];

// Obtiene el ID del contacto a eliminar desde la URL (?id=...)
// Si no se envía, devuelve error 400 (dato requerido)
$id = $_GET["id"] ?? null;
if (!$id) {
    http_response_code(400);
    echo json_encode(["ok" => false, "message" => "ID de contacto requerido."]);
    exit;
}

// Verifica que el contacto exista y pertenezca al usuario autenticado
$sql = "SELECT id FROM contactos WHERE id = :id AND usuario_id = :uid LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":id", $id);
$stmt->bindParam(":uid", $user_id);
$stmt->execute();

// Si no existe, devuelve error 404 (no encontrado)
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(["ok" => false, "message" => "Contacto no encontrado."]);
    exit;
}

// Prepara la consulta SQL para eliminar el contacto
$sql = "DELETE FROM contactos WHERE id = :id AND usuario_id = :uid";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":id", $id);
$stmt->bindParam(":uid", $user_id);

// Ejecuta la eliminación
// Si todo sale bien, responde con éxito
// Si ocurre un error, devuelve 500 (error interno)
if ($stmt->execute()) {
    echo json_encode(["ok" => true, "message" => "Contacto eliminado correctamente."]);
} else {
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Error al eliminar contacto."]);
}