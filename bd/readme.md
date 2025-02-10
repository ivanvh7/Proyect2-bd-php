# 📌 Documentación de la Base de Datos y Configuración del Proyecto

## 📂 Estructura de la Base de Datos
El proyecto utiliza una base de datos relacional en MySQL. Se recomienda ejecutar los scripts en el siguiente orden para configurar correctamente la base de datos:

1. **tablas.sql** (Define la estructura de las tablas)
2. **inserts.sql** (Inserta datos de prueba, si es necesario)

### 📋 Tablas Principales

#### 🔹 `usuarios`
Almacena la información de los usuarios registrados.

| Campo      | Tipo           | Descripción                        |
|------------|--------------|--------------------------------|
| id         | INT (PK, AI) | Identificador único del usuario |
| nombre     | VARCHAR(100) | Nombre del usuario             |
| email      | VARCHAR(100) | Correo electrónico (debe ser único) |
| password   | VARCHAR(255) | Contraseña encriptada          |
| rol        | ENUM('admin', 'usuario') | Determina si el usuario es administrador o usuario normal |

#### 🔹 `productos`
Almacena la información de los productos disponibles.

| Campo      | Tipo           | Descripción                        |
|------------|--------------|--------------------------------|
| id         | INT (PK, AI) | Identificador único del producto |
| nombre     | VARCHAR(100) | Nombre del producto             |
| descripcion| TEXT         | Descripción del producto        |
| precio     | DECIMAL(10,2)| Precio del producto            |
| imagen     | VARCHAR(255) | URL de la imagen del producto  |

## 🔑 Creación de un Administrador

Para acceder a la interfaz administrativa (`listado_admin.php`), debe existir un usuario con el rol de `admin`. La web solo permite la existencia de un único administrador. Si ya hay uno registrado, no se podrá crear otro desde la interfaz.

### 🔹 Creación del Administrador desde la Web
Si no existe un administrador en la base de datos, se debe crear desde la propia página web a través del formulario de registro. Una vez se crea un usuario con rol `admin`, la opción de registrar otro administrador desaparece de la interfaz de inicio de sesión.

> **Nota:** La contraseña se codifica automáticamente mediante un algoritmo seguro en la base de datos, garantizando la protección de las credenciales.

### 📌 Importante
- Solo se permite un administrador. Si ya existe, la opción de registrar otro administrador no estará disponible en la web.
- Si necesitas cambiar la configuración de la base de datos, revisa el archivo `config.php` dentro de la carpeta `config/`.

---

## 🚀 Configuración del Proyecto

### 📦 Instalación
1. Clonar el repositorio o descargar el proyecto.
2. Importar la base de datos en MySQL usando los archivos SQL dentro de `db/scripts/`.
3. Configurar la conexión a la base de datos en `config/config.php`.
4. Asegurar que el servidor Apache y MySQL estén activos.
5. Acceder a la página principal en el navegador (`index.php`).


**¡Listo! Ahora puedes comenzar a usar el sistema.** 🎉

