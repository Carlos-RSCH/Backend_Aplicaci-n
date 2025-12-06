<?php
// backend/api/config/headers.php

// Permite que cualquier origen (dominio) pueda acceder a la API
// Esto se conoce como CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: *");

// Define que todas las respuestas de la API serán en formato JSON con codificación UTF-8
header("Content-Type: application/json; charset=UTF-8");

// Indica qué métodos HTTP acepta la API (lectura, creación, actualización, borrado, y prevalidación)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Especifica qué cabeceras pueden enviarse en las peticiones (ej. tipo de contenido, autorización, etc.) Cabecera usada comúnmente por frameworks JavaScript (como jQuery) para identificar peticiones AJAX.
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Maneja las peticiones de tipo OPTIONS (preflight request en CORS)
// Si el navegador pregunta "¿puedo usar este método/cabecera?", la API responde con 204 (sin contenido)
// y termina la ejecución sin procesar más lógica
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);//Acepta la peticion
    exit;
}

//- Es el archivo que prepara las reglas de comunicación entre tu API y el cliente.