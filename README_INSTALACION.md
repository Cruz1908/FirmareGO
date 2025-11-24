# Guía de Instalación FrimanGO - XAMPP

## Pasos de Instalación

### 1. Verificar que XAMPP esté corriendo
- Abre el Panel de Control de XAMPP
- Inicia **Apache** y **MySQL**
- Verifica que ambos estén en verde (funcionando)

### 2. Instalar la Base de Datos

**Opción A - Instalador Web (Recomendado):**
1. Abre tu navegador
2. Ve a: `http://localhost/FrimanGO/install/install.php`
3. Completa el formulario:
   - Host: `localhost`
   - Usuario: `root`
   - Contraseña: (vacío por defecto en XAMPP)
   - Base de datos: `frimango`
4. Haz clic en "Instalar Base de Datos"

**Opción B - phpMyAdmin:**
1. Ve a: `http://localhost/phpmyadmin`
2. Crea una nueva base de datos llamada `frimango`
3. Selecciona la base de datos
4. Ve a la pestaña "Importar"
5. Selecciona el archivo `install/database.sql`
6. Haz clic en "Continuar"

### 3. Verificar Instalación

**Opción A - Script de Debug:**
1. Ve a: `http://localhost/FrimanGO/debug.php`
2. Verifica que todas las conexiones estén correctas

**Opción B - Verificar en phpMyAdmin:**
1. Ve a: `http://localhost/phpmyadmin`
2. Selecciona la base de datos `frimango`
3. Verifica que existan las tablas:
   - `categories`
   - `products`
   - `users`
   - `orders`
   - `order_items`

### 4. Acceder al Sitio

1. Ve a: `http://localhost/FrimanGO/`
2. Deberías ver la página de inicio con productos

## Problemas Comunes

### Error 404 al acceder a /login

**Solución:** Asegúrate de que el módulo `mod_rewrite` esté habilitado en Apache:
1. Abre `httpd.conf` en XAMPP (normalmente en `C:\xampp\apache\conf\httpd.conf`)
2. Busca la línea: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Quita el `#` para descomentarla: `LoadModule rewrite_module modules/mod_rewrite.so`
4. Reinicia Apache

### Página vacía sin productos

**Causas posibles:**
1. La base de datos no está instalada
2. Las tablas no tienen datos
3. Error de conexión a MySQL

**Solución:**
1. Verifica en phpMyAdmin que la base de datos existe
2. Verifica que las tablas tengan datos (deberían tener categorías y productos de ejemplo)
3. Si las tablas están vacías, ejecuta manualmente el SQL de `install/database.sql`

### Error de conexión a MySQL

**Solución:**
1. Verifica que MySQL esté corriendo en XAMPP
2. Verifica las credenciales en `config/database.php`:
   - Por defecto: usuario `root`, contraseña vacía
   - Si cambiaste la contraseña de root, actualízala en `config/database.php`

## Estructura de URLs

Una vez instalado, puedes acceder a:
- Inicio: `http://localhost/FrimanGO/`
- Login: `http://localhost/FrimanGO/login`
- Registro: `http://localhost/FrimanGO/register`
- Categorías: `http://localhost/FrimanGO/category`
- Carrito: `http://localhost/FrimanGO/cart`
- Checkout: `http://localhost/FrimanGO/checkout`

## Usuario Administrador

Después de instalar, puedes iniciar sesión con:
- Email: `admin@frimango.com`
- Contraseña: `admin123`

**⚠️ IMPORTANTE:** Cambia esta contraseña después del primer inicio de sesión.

