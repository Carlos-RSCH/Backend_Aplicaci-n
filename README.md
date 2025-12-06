# 🔧 Agenda de Contactos – Backend (PHP)

Este es el Backend de la aplicación **Agenda de Contactos**, construido con **PHP 8+**, **MySQL** y arquitectura estilo **API REST**.

Funciones principales:

- Registro de usuarios  
- Inicio de sesión con generación de JWT  
- Validación del token en cada petición  
- CRUD completo de contactos  
- Protección para que cada usuario solo acceda a sus propios datos  

Incluye:

- Endpoints separados por módulos (`auth`, `contactos`)  
- Conexión segura mediante PDO  
- Generación y validación de tokens sin librerías externas  

Este proyecto funciona como servidor para el frontend hecho en Vue, permitiendo comunicación real entre ambos mediante JSON.
