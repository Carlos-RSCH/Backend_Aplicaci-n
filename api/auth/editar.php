<?php
// backend/api/auth/editar.php

// Importa configuraciones necesarias: cabeceras HTTP, conexión a la base de datos y funciones de autenticación
require_once __DIR__ . "/../config/headers.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../helpers/auth.php";

// Verifica que la petición sea de tipo PUT (solo se permite actualizar con este método)
if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    http_response_code(405); // Devuelve error "Método no permitido"
    echo json_encode(["ok" => false, "message" => "Método no permitido."]);
    exit;
}

// Obtiene los datos del usuario autenticado (por ejemplo, su ID)
$payload = require_user();//helpers/auth.php
$user_id = $payload["user_id"];//permite la actualizacion solamente del usuario

// Lee el cuerpo de la petición (JSON enviado por el cliente) y extrae el nuevo nombre de usuario
$data = json_decode(file_get_contents("php://input"), true);
$nuevo_username = $data["username"] ?? null;

// Si no se envió un nombre de usuario, devuelve error
if (!$nuevo_username) {
    http_response_code(400); // Error de solicitud incorrecta
    echo json_encode(["ok" => false, "message" => "Nombre de usuario requerido."]);
    exit;
}

// Comprueba si el nuevo nombre de usuario ya está ocupado por otro usuario distinto
$sql = "SELECT id FROM usuarios WHERE nombre_de_usuario = :username AND id != :id LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":username", $nuevo_username);
$stmt->bindParam(":id", $user_id);
$stmt->execute();

// Si encuentra coincidencia, devuelve error de conflicto (409)
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(["ok" => false, "message" => "Ese nombre de usuario ya está en uso."]);
    exit;
}

// Si no hay conflicto, actualiza el nombre de usuario en la base de datos
$sql = "UPDATE usuarios SET nombre_de_usuario = :username WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":username", $nuevo_username);
$stmt->bindParam(":id", $user_id);

// Si la actualización se ejecuta correctamente, devuelve mensaje de éxito
if ($stmt->execute()) {
    echo json_encode(["ok" => true, "message" => "Perfil actualizado correctamente."]);
} else {
    // Si ocurre un error en la base de datos, devuelve error interno (500)
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Error al actualizar perfil."]);
}