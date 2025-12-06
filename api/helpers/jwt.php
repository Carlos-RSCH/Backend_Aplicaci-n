<?php
// backend/api/helpers/jwt.php

// Clave secreta usada para firmar y validar los tokens JWT.
// Solo el servidor la conoce, y garantiza que nadie pueda falsificar un token.
const JWT_SECRET = "9uN8dj93JAlsmk23_@#kasmd9123kjhASD--9012xm";


// -----------------------------
// Función: base64url_encode()
// -----------------------------
// Convierte datos a Base64URL (una variante de Base64 segura para URLs).
// Se usa para codificar el header y el payload del JWT.
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), "+/", "-_"), "="); //Así se pueden enviar dentro del token JWT sin que el navegador o el servidor se confundan.
}


// -----------------------------
// Función: base64url_decode()
// -----------------------------
// Su objetivo es tomar un texto que está codificado en Base64URL
// (la versión "segura para URLs" de Base64) y devolverlo a su forma original.
// Esto se usa para leer el header y el payload de un JWT.

// Recibe como parámetro $data (el texto codificado).
function base64url_decode($data) {
    // Calcula si la longitud del texto es múltiplo de 4.
    // En Base64, los datos suelen terminar con "=" para completar bloques de 4 caracteres.
    $remainder = strlen($data) % 4;

    // Si no es múltiplo de 4, agrega el número necesario de "=" al final
    // para que la decodificación funcione correctamente.
    if ($remainder) {
        $padlen = 4 - $remainder;
        $data .= str_repeat("=", $padlen);
    }

    // Convierte los caracteres especiales de Base64URL a los de Base64 normal:
    // "-" se convierte en "+"
    // "_" se convierte en "/"
    // Luego aplica base64_decode para obtener los datos originales.
    return base64_decode(strtr($data, "-_", "+/"));
}



// -----------------------------
// Función: jwt_encode()
// -----------------------------
// Crea un token JWT a partir de un payload (datos del usuario).
// - Añade un header con tipo y algoritmo.
// - Añade fecha de expiración.
// - Codifica header y payload.
// - Genera la firma con la clave secreta.
// - Devuelve el token completo en formato header.payload.signature.
function jwt_encode($payload, $expSeconds = 3600) {
    $header = ["typ" => "JWT", "alg" => "HS256"];
    $payload["exp"] = time() + $expSeconds; // Expira en X segundos

    $header_encoded  = base64url_encode(json_encode($header));
    $payload_encoded = base64url_encode(json_encode($payload));
    $signature       = hash_hmac("sha256", "$header_encoded.$payload_encoded", JWT_SECRET, true);
    $signature_encoded = base64url_encode($signature);

    return "$header_encoded.$payload_encoded.$signature_encoded";
}


// -----------------------------
// Función: jwt_decode()
// -----------------------------
// Valida y decodifica un token JWT.
// - Separa las tres partes (header, payload, signature).
// - Recalcula la firma y la compara con la recibida.
// - Decodifica el payload.
// - Verifica que no esté expirado.
// - Devuelve los datos del usuario si es válido, o false si no lo es.
function jwt_decode($jwt) {
    $parts = explode(".", $jwt);
    if (count($parts) !== 3) {
        return false; // Token mal formado
    }

    list($header_encoded, $payload_encoded, $signature_encoded) = $parts;

    $signature = base64url_decode($signature_encoded);
    $expected  = hash_hmac("sha256", "$header_encoded.$payload_encoded", JWT_SECRET, true);

    if (!hash_equals($expected, $signature)) {
        return false; // Firma inválida
    }

    $payload = json_decode(base64url_decode($payload_encoded), true);
    if (!$payload || !isset($payload["exp"]) || $payload["exp"] < time()) {
        return false; // Token expirado o inválido
    }

    return $payload; // Devuelve los datos del usuario
}

//Un JSON Web Token (JWT) es como un pase digital que el servidor entrega a un usuario cuando inicia sesión correctamente.
//Ese pase contiene información (ej. ) y una firma que garantiza que no fue alterado.
