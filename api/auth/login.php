<?php
// backend/api/auth/login.php

// Importa configuraciones necesarias:
// - headers.php: define cabeceras HTTP (ej. JSON, CORS)
// - db.php: conexión a la base de datos
// - jwt.php: funciones para crear y manejar tokens JWT
require_once __DIR__ . "/../config/headers.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../helpers/jwt.php";

// Lee los datos enviados por el cliente en formato JSON (usuario y contraseña)
$data = json_decode(file_get_contents("php://input"), true);
$username = $data["username"] ?? null;
$password = $data["password"] ?? null;

// Verifica que se hayan enviado ambos datos (usuario y contraseña)
// Si falta alguno, devuelve error 400 (solicitud incorrecta)
if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(["ok" => false, "message" => "Usuario y contraseña requeridos."]);
    exit;
}

// Busca al usuario en la base de datos usando el nombre de usuario
$sql = "SELECT id, nombre_de_usuario, password FROM usuarios WHERE nombre_de_usuario = :username LIMIT 1";
$stmt = $conn->prepare($sql);//prepara la consulta
$stmt->bindParam(":username", $username, PDO::PARAM_STR);//reemplaza :username por el nombre de usuario que el cliente envió.
$stmt->execute();//el servidor busca si existe un usuario con ese nombre.
$user = $stmt->fetch();//Recupera el resultado de la consulta.

// Verifica que el usuario exista y que la contraseña sea correcta
// Si no coincide, devuelve error 401 (no autorizado)
if (!$user || !password_verify($password, $user["password"])) {
    http_response_code(401);
    echo json_encode(["ok" => false, "message" => "Credenciales incorrectas."]);
    exit;
}

// Si las credenciales son válidas, genera un token JWT
// Este token servirá para autenticar al usuario en futuras peticiones
$token = jwt_encode(["user_id" => $user["id"], "username" => $user["nombre_de_usuario"]], 3600);

// Devuelve una respuesta JSON con:
// - Mensaje de éxito
// - El token generado
// - Datos básicos del usuario (id y nombre de usuario)
echo json_encode([
    "ok" => true,
    "message" => "Login exitoso",
    "token" => $token,
    "user" => [
        "id" => $user["id"],
        "nombre_de_usuario" => $user["nombre_de_usuario"]
    ]
]);