<?php
// backend/api/contactos/actualizar.php

// Importa configuraciones necesarias:
// - headers.php: cabeceras HTTP (JSON, CORS)
// - db.php: conexión a la base de datos
// - auth.php: funciones de autenticación (validar usuario)
require_once __DIR__ . "/../config/headers.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../helpers/auth.php";

// Verifica que la petición sea de tipo PUT (actualización)
// Si no lo es, devuelve error 405 (método no permitido)
if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    http_response_code(405);
    echo json_encode(["ok" => false, "message" => "Método no permitido."]);
    exit;
}

// Obtiene los datos del usuario autenticado (ej. su ID)
$payload = require_user();
$user_id = $payload["user_id"];

// Obtiene el ID del contacto a actualizar desde la URL (?id=...)
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

// Lee los datos enviados en el cuerpo de la petición (JSON con nuevos valores)
$data = json_decode(file_get_contents("php://input"), true);

// Extrae los campos del contacto (nombre, teléfono, apellido, email, dirección, notas)
$nombre   = $data["nombre"]   ?? null;
$telefono = $data["telefono"] ?? null;
$apellido = $data["apellido"] ?? null;
$email    = $data["email"]    ?? null;
$direccion= $data["direccion"]?? null;
$notas    = $data["notas"]    ?? null;

// Valida que al menos nombre y teléfono estén presentes
// Si faltan, devuelve error 400 (datos obligatorios)
if (!$nombre || !$telefono) {
    http_response_code(400);
    echo json_encode(["ok" => false, "message" => "Nombre y teléfono son obligatorios."]);
    exit;
}

// Prepara la consulta SQL para actualizar el contacto con los nuevos datos
$sql = "UPDATE contactos
        SET nombre = :nombre,
            apellido = :apellido,
            telefono = :telefono,
            email = :email,
            direccion = :direccion,
            notas = :notas
        WHERE id = :id AND usuario_id = :uid";
$stmt = $conn->prepare($sql);

// Asocia los valores recibidos a los parámetros de la consulta
$stmt->bindParam(":nombre", $nombre);
$stmt->bindParam(":apellido", $apellido);
$stmt->bindParam(":telefono", $telefono);
$stmt->bindParam(":email", $email);
$stmt->bindParam(":direccion", $direccion);
$stmt->bindParam(":notas", $notas);
$stmt->bindParam(":id", $id);
$stmt->bindParam(":uid", $user_id);

// Ejecuta la actualización
// Si todo sale bien, responde con éxito
// Si ocurre un error, devuelve 500 (error interno)
if ($stmt->execute()) {
    echo json_encode(["ok" => true, "message" => "Contacto actualizado correctamente."]);
} else {
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Error al actualizar contacto."]);
}