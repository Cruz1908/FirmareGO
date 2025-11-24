# FrimanGO - Mercado Online de Pescados y Productos Frescos

Proyecto PHP completo para replicar el diseño de Figma de un mercado online especializado en pescados y productos frescos, con integración MySQL, OAuth (Google, Facebook, Apple) y pagos con Stripe.

## 🚀 Características

- ✅ **Landing page** con hero de productos frescos según diseño Figma
- ✅ **Sistema de categorías** (CONGELAT, AMBIENT, REFRIGERATS)
- ✅ **Menú lateral desplegable** según diseño Figma
- ✅ **Búsqueda de productos** funcional
- ✅ **Carrito de compras** completo
- ✅ **Checkout** con integración Stripe
- ✅ **Autenticación** tradicional y OAuth (Google, Facebook, Apple)
- ✅ **Base de datos MySQL** (compatible XAMPP)
- ✅ **Diseño responsive** según Figma
- ✅ **Header amarillo (#FFD200)** según diseño

## 📋 Requisitos

- PHP 7.4 o superior
- MySQL 5.7+ o MariaDB 10.2+
- XAMPP (recomendado) o servidor web con PHP y MySQL
- Extensiones PHP: `mysqli`, `curl`, `json`
- Claves API para OAuth y Stripe (opcional para desarrollo)

## 🔧 Instalación

### 1. Clonar o descargar el proyecto

```bash
cd D:\Adriiii\FrimanGO
```

### 2. Configurar base de datos MySQL

#### Opción A: Instalación automática (recomendado)

1. Iniciar XAMPP y asegurarse de que MySQL esté corriendo
2. Abrir en el navegador: `http://localhost:8000/install/install.php`
3. Completar el formulario con:
   - Host: `localhost`
   - Usuario: `root`
   - Contraseña: (vacío por defecto en XAMPP)
   - Base de datos: `frimango`
4. Hacer clic en "Instalar Base de Datos"

#### Opción B: Instalación manual

1. Abrir phpMyAdmin (http://localhost/phpmyadmin)
2. Crear una nueva base de datos llamada `frimango`
3. Importar el archivo `install/database.sql`
4. Crear archivo `config/database.php` con tus credenciales:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'frimango');
define('DB_CHARSET', 'utf8mb4');
```

### 3. Configurar OAuth y Pagos (Opcional)

1. Copiar `config/env.example.php` a `.env.php` (o configurar directamente en `config/config.php`)
2. Obtener credenciales:

#### Google OAuth:
- Ir a [Google Cloud Console](https://console.cloud.google.com/)
- Crear proyecto y habilitar Google+ API
- Crear credenciales OAuth 2.0
- Agregar URI de redirección: `http://localhost:8000/api/oauth/google-callback.php`

#### Facebook OAuth:
- Ir a [Facebook Developers](https://developers.facebook.com/)
- Crear aplicación
- Agregar URI de redirección: `http://localhost:8000/api/oauth/facebook-callback.php`

#### Stripe:
- Registrarse en [Stripe](https://stripe.com/)
- Obtener claves de API (Test mode)
- Configurar webhook (opcional)

3. Actualizar `config/config.php` con tus credenciales:

```php
define('GOOGLE_CLIENT_ID', 'tu_client_id');
define('GOOGLE_CLIENT_SECRET', 'tu_client_secret');
define('FACEBOOK_APP_ID', 'tu_app_id');
define('FACEBOOK_APP_SECRET', 'tu_app_secret');
define('STRIPE_PUBLIC_KEY', 'pk_test_...');
define('STRIPE_SECRET_KEY', 'sk_test_...');
```

### 4. Iniciar servidor

#### Con XAMPP:
1. Iniciar Apache y MySQL desde el panel de control de XAMPP
2. Abrir: `http://localhost/FrimanGO` (ajustar según configuración)

#### Con servidor PHP incorporado:
```bash
php -S localhost:8000
```

Luego abrir: `http://localhost:8000`

## 📁 Estructura del Proyecto

```
FrimanGO/
├── api/                  # Endpoints API
│   ├── oauth/            # OAuth callbacks
│   ├── payment/          # Stripe payment intents
│   ├── cart-*.php        # Gestión del carrito
│   ├── checkout.php      # Procesar checkout
│   ├── login.php         # Login
│   ├── register.php      # Registro
│   └── logout.php        # Logout
├── assets/               # Recursos estáticos
│   └── images/           # Imágenes de Figma
│       ├── logos/        # Logos
│       ├── categories/   # Imágenes de categorías
│       └── products/     # Imágenes de productos
├── config/               # Configuración
│   ├── config.php        # Config principal
│   ├── database.php      # Config MySQL
│   └── env.example.php   # Ejemplo de variables de entorno
├── data/                 # Base de datos SQLite (fallback)
├── install/              # Scripts de instalación
│   ├── install.php       # Instalador web
│   └── database.sql      # Script SQL
├── src/                  # Modelos PHP
│   ├── Database.php      # Conexión DB
│   ├── Product.php       # Modelo productos
│   ├── Cart.php          # Modelo carrito
│   ├── Auth.php          # Modelo autenticación
│   ├── OAuth.php         # OAuth handlers
│   └── Payment.php       # Stripe integration
├── views/                # Vistas PHP
│   ├── partials/         # Componentes
│   │   └── navbar.php    # Navbar
│   ├── home.php          # Landing page
│   ├── category.php      # Página categorías
│   ├── product.php       # Página producto
│   ├── cart.php          # Carrito
│   ├── checkout.php      # Checkout
│   ├── login.php         # Login
│   ├── register.php      # Registro
│   ├── order_complete.php # Confirmación
│   ├── 404.php           # Error 404
│   └── layout.php        # Layout principal
├── index.php             # Punto de entrada
├── app.js                # JavaScript frontend
├── styles.css            # Estilos principales
└── README.md             # Esta documentación
```

## 🗄️ Base de Datos

### Estructura de tablas

- **users**: Usuarios (con soporte OAuth)
- **categories**: Categorías de productos
- **products**: Productos del catálogo
- **orders**: Órdenes de compra
- **order_items**: Items de cada orden

### Usuario administrador por defecto

- Email: `admin@frimango.com`
- Contraseña: `admin123` (¡cambiar después!)

## 🔐 Autenticación

### Login tradicional
- Formulario de email/contraseña
- Registro de nuevos usuarios

### OAuth
- **Google**: Login con cuenta de Google
- **Facebook**: Login con cuenta de Facebook
- **Apple**: Preparado para Apple Sign In (requiere configuración adicional)

## 💳 Pagos

### Stripe
- Integración completa con Stripe Elements
- Procesamiento de tarjetas de crédito/débito
- Validación en tiempo real

### Pago en efectivo
- Opción de pago contra entrega

## 🎨 Diseño

El proyecto replica fielmente los diseños de Figma:

- **Header amarillo** (#FFD200) con logo, búsqueda y carrito
- **Menú lateral** (hamburguesa) con categorías organizadas
- **Hero de categoría** con fondo de productos frescos
- **Sección "Cómo funciona"** con diseño oscuro
- **Grid de productos** responsive
- **Páginas de carrito y checkout** según diseño

## 📝 Notas de Desarrollo

### Migración a PrestaShop

Este proyecto está preparado para migrar a PrestaShop:

1. **Productos**: Estructura compatible con PrestaShop
2. **Categorías**: Sistema similar a PrestaShop
3. **Usuarios**: Compatible con sistema de usuarios PrestaShop
4. **Órdenes**: Estructura compatible con órdenes PrestaShop

### Variables de Entorno

Para producción, usar variables de entorno o archivo `.env.php` (no subir a Git).

### Seguridad

- ✅ Contraseñas hasheadas con `password_hash()`
- ✅ Protección contra SQL injection con prepared statements
- ✅ Validación de datos en formularios
- ✅ Sesiones seguras configuradas

## 🚧 Próximos Pasos

- [ ] Panel de administración
- [ ] Gestión de productos desde admin
- [ ] Sistema de notificaciones por email
- [ ] Webhooks de Stripe
- [ ] Integración completa con Apple Sign In
- [ ] Optimización de imágenes
- [ ] Cache de productos
- [ ] Migración a PrestaShop

## 📄 Licencia

Este proyecto es privado y propietario.

## 🆘 Soporte

Para problemas o preguntas:
1. Verificar que MySQL esté corriendo
2. Verificar permisos de la carpeta `data/`
3. Revisar logs de PHP en XAMPP
4. Verificar credenciales de OAuth y Stripe

## 📧 Contacto

Para más información sobre el proyecto, contactar al equipo de desarrollo.
