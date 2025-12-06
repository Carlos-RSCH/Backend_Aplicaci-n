<?php
// backend/api/auth/registrar.php

// Importa configuraciones necesarias:
// - headers.php: define cabeceras HTTP (ej. JSON, CORS)
// - db.php: conexión a la base de datos
require_once __DIR__ . "/../config/headers.php";
require_once __DIR__ . "/../config/db.php";

// Lee los datos enviados por el cliente en formato JSON (usuario y contraseña)
$data = json_decode(file_get_contents("php://input"), true);
$username = $data["username"] ?? null;
$password = $data["password"] ?? null;

// Verifica que se hayan enviado ambos datos
// Si falta alguno, devuelve error 400 (solicitud incorrecta)
if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(["ok" => false, "message" => "Usuario y contraseña requeridos."]);
    exit;
}

// Evita duplicados: busca si ya existe un usuario con ese nombre
$sql = "SELECT id FROM usuarios WHERE nombre_de_usuario = :username LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":username", $username);
$stmt->execute();

// Si encuentra coincidencia, devuelve error 409 (conflicto: nombre ya ocupado)
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(["ok" => false, "message" => "El nombre de usuario ya existe."]);
    exit;
}

// Encripta la contraseña antes de guardarla en la base de datos
$hash = password_hash($password, PASSWORD_DEFAULT);

// Inserta un nuevo usuario en la tabla con su nombre y contraseña encriptada
$sql = "INSERT INTO usuarios (nombre_de_usuario, password) VALUES (:username, :password)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":username", $username);
$stmt->bindParam(":password", $hash);

// Si la inserción fue exitosa, devuelve código 201 (creado) y mensaje de éxito
if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode(["ok" => true, "message" => "Usuario registrado correctamente."]);
} else {
    // Si ocurre un error en la base de datos, devuelve error 500 (interno)
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Error al registrar usuario."]);
}