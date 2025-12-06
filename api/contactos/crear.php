<?php
// backend/api/contactos/crear.php

// Importa configuraciones necesarias:
// - headers.php: cabeceras HTTP (JSON, CORS)
// - db.php: conexión a la base de datos
// - auth.php: funciones de autenticación (validar usuario)
require_once __DIR__ . "/../config/headers.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../helpers/auth.php";

// Verifica que la petición sea de tipo POST (crear recurso)
// Si no lo es, devuelve error 405 (método no permitido)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["ok" => false, "message" => "Método no permitido."]);
    exit;
}

// Obtiene los datos del usuario autenticado (ej. su ID)
$payload = require_user();
$user_id = $payload["user_id"];

// Lee los datos enviados en el cuerpo de la petición (JSON con información del contacto)
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

// Prepara la consulta SQL para insertar un nuevo contacto en la base de datos
$sql = "INSERT INTO contactos (usuario_id, nombre, apellido, telefono, email, direccion, notas)
        VALUES (:usuario_id, :nombre, :apellido, :telefono, :email, :direccion, :notas)";
$stmt = $conn->prepare($sql);

// Asocia los valores recibidos a los parámetros de la consulta
$stmt->bindParam(":usuario_id", $user_id);
$stmt->bindParam(":nombre", $nombre);
$stmt->bindParam(":apellido", $apellido);
$stmt->bindParam(":telefono", $telefono);
$stmt->bindParam(":email", $email);
$stmt->bindParam(":direccion", $direccion);
$stmt->bindParam(":notas", $notas);

// Ejecuta la inserción
// Si todo sale bien, responde con éxito (201 Created)
// Si ocurre un error, devuelve 500 (error interno)
if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode(["ok" => true, "message" => "Contacto creado correctamente."]);
} else {
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Error al crear contacto."]);
}