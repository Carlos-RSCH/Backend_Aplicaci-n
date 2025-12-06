<?php
// backend/api/helpers/auth.php

// Importa el archivo jwt.php, que contiene funciones para codificar/decodificar tokens JWT
require_once __DIR__ . "/jwt.php";

// -----------------------------
// Función: getBearerToken()
// -----------------------------
// Su objetivo es obtener el token JWT que el cliente envía en la cabecera "Authorization"
function getBearerToken() {
    $headers = null;

    // Busca la cabecera Authorization en diferentes posibles ubicaciones
    if (isset($_SERVER["Authorization"])) {
        $headers = trim($_SERVER["Authorization"]);
    } elseif (isset($_SERVER["HTTP_AUTHORIZATION"])) {
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists("apache_request_headers")) {
        $requestHeaders = apache_request_headers();
        if (isset($requestHeaders["Authorization"])) {
            $headers = trim($requestHeaders["Authorization"]);
        }
    }

    // Si encontró la cabecera, intenta extraer el token con formato "Bearer <token>"
    if (!empty($headers)) {
        if (preg_match("/Bearer\\s(\\S+)/", $headers, $matches)) {
            return $matches[1]; // Devuelve solo el token
        }
    }
    return null; // Si no hay token, devuelve null
}

// -----------------------------
// Función: require_user()
// -----------------------------
// Su objetivo es validar que el usuario esté autenticado mediante un token JWT
function require_user() {
    // Obtiene el token desde la cabecera
    $token = getBearerToken();
    if (!$token) {
        // Si no se envió token, devuelve error 401 (no autorizado)
        http_response_code(401);
        echo json_encode(["ok" => false, "message" => "Token no enviado."]);
        exit;
    }

    // Decodifica el token para obtener la información del usuario
    $payload = jwt_decode($token);
    if (!$payload || !isset($payload["user_id"])) {
        // Si el token es inválido o expiró, devuelve error 401
        http_response_code(401);
        echo json_encode(["ok" => false, "message" => "Token inválido o expirado."]);
        exit;
    }

    // Si todo está bien, devuelve los datos del usuario (payload)
    return $payload;
}

//Este archivo asegura que cada petición al backend esté hecha por un usuario autenticado.
//Lo hace buscando el token JWT en la cabecera Authorization y validándolo.
