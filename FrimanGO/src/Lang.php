<?php
/**
 * Sistema de gestión de idiomas
 * Soporta español (es) y catalán (ca)
 */
class Lang {
    private static $translations = [
        'es' => [
            // Navbar
            'nav.home' => 'Inicio',
            'nav.products' => 'Productos',
            'nav.login' => 'Iniciar sesión',
            'nav.logout' => 'Salir',
            'nav.cart' => 'Carrito',
            'nav.help' => 'Ayuda',
            'nav.search' => 'Buscar',
            
            // Home
            'home.featured' => 'Productos destacados',
            'home.how_works' => '¿CÓMO FUNCIONA?',
            'home.how_1' => 'Haz tu compra cómodamente desde la <span class="accent">web</span>',
            'home.how_2' => 'Preparamos tu pedido <span class="accent">rápidamente</span>',
            'home.how_3' => 'En menos de dos horas puedes <span class="accent">recogerlo</span>',
            
            // Productos
            'product.add_to_cart' => 'Añadir al carrito',
            'product.view' => 'Ver',
            'product.add' => 'Añadir',
            'product.category' => 'Categoría',
            'product.subcategory' => 'Subcategoría',
            'product.stock' => 'Stock disponible',
            'product.units' => 'unidades',
            
            // Carrito
            'cart.title' => 'Tu carrito',
            'cart.empty' => 'Tu carrito está vacío',
            'cart.empty_desc' => 'Añade productos al carrito para continuar con la compra.',
            'cart.explore' => 'Explorar productos',
            'cart.product' => 'Producto',
            'cart.price' => 'Precio',
            'cart.quantity' => 'Cantidad',
            'cart.total' => 'Total',
            'cart.remove' => 'Eliminar',
            'cart.summary' => 'Resumen del pedido',
            'cart.subtotal' => 'Subtotal',
            'cart.shipping' => 'Envío',
            'cart.shipping_free' => 'Gratis',
            'cart.go_to_checkout' => 'Ir al pago',
            
            // Checkout
            'checkout.title' => 'Completar pedido',
            'checkout.contact' => 'Datos de contacto',
            'checkout.full_name' => 'Nombre completo',
            'checkout.email' => 'Email',
            'checkout.phone' => 'Teléfono',
            'checkout.delivery' => 'Dirección de entrega',
            'checkout.address' => 'Dirección',
            'checkout.city' => 'Ciudad',
            'checkout.postal' => 'Código postal',
            'checkout.payment' => 'Método de pago',
            'checkout.card' => 'Tarjeta de crédito/débito (Stripe)',
            'checkout.cash' => 'Efectivo a la entrega',
            'checkout.card_data' => 'Datos de la tarjeta',
            'checkout.complete' => 'Completar pedido',
            'checkout.processing' => 'Procesando pago...',
            'checkout.order_summary' => 'Resumen del pedido',
            
            // Login
            'login.title' => 'Iniciar sesión',
            'login.password' => 'Contraseña',
            'login.enter' => 'Entrar',
            'login.no_account' => '¿No tienes cuenta?',
            'login.register' => 'Regístrate',
            'login.with' => 'O inicia sesión con:',
            
            // General
            'filter.price' => 'FILTRAR POR PRECIO',
            'filter.apply' => 'Aplicar',
            'db.not_configured' => 'Base de datos no configurada',
            'db.install' => 'Instalar base de datos',
            'db.no_products' => 'No hay productos disponibles',
        ],
        'ca' => [
            // Navbar
            'nav.home' => 'Inici',
            'nav.products' => 'Productes',
            'nav.login' => 'Iniciar sessió',
            'nav.logout' => 'Sortir',
            'nav.cart' => 'Carret',
            'nav.help' => 'Ajuda',
            'nav.search' => 'Buscar',
            
            // Home
            'home.featured' => 'Productes destacats',
            'home.how_works' => 'COM FUNCIONA?',
            'home.how_1' => 'Fes la teva compra còmodament des del <span class="accent">web</span>',
            'home.how_2' => 'Preparem la comanda <span class="accent">ràpidament</span>',
            'home.how_3' => 'En menys de dues hores pots <span class="accent">recollir-la</span>',
            
            // Productos
            'product.add_to_cart' => 'Afegir al carret',
            'product.view' => 'Veure',
            'product.add' => 'Afegir',
            'product.category' => 'Categoria',
            'product.subcategory' => 'Subcategoria',
            'product.stock' => 'Estoc disponible',
            'product.units' => 'unitats',
            
            // Carrito
            'cart.title' => 'El teu carret',
            'cart.empty' => 'El teu carret està buit',
            'cart.empty_desc' => 'Afegix productes al carret per continuar amb la compra.',
            'cart.explore' => 'Explorar productes',
            'cart.product' => 'Producte',
            'cart.price' => 'Preu',
            'cart.quantity' => 'Quantitat',
            'cart.total' => 'Total',
            'cart.remove' => 'Eliminar',
            'cart.summary' => 'Resum de la comanda',
            'cart.subtotal' => 'Subtotal',
            'cart.shipping' => 'Enviament',
            'cart.shipping_free' => 'Gratis',
            'cart.go_to_checkout' => 'Anar al pagament',
            
            // Checkout
            'checkout.title' => 'Completar comanda',
            'checkout.contact' => 'Dades de contacte',
            'checkout.full_name' => 'Nom complet',
            'checkout.email' => 'Email',
            'checkout.phone' => 'Telèfon',
            'checkout.delivery' => 'Adreça d\'entrega',
            'checkout.address' => 'Adreça',
            'checkout.city' => 'Ciutat',
            'checkout.postal' => 'Codi postal',
            'checkout.payment' => 'Mètode de pagament',
            'checkout.card' => 'Targeta de crèdit/dèbit (Stripe)',
            'checkout.cash' => 'Efectiu a l\'entrega',
            'checkout.card_data' => 'Dades de la targeta',
            'checkout.complete' => 'Completar comanda',
            'checkout.processing' => 'Procesant pagament...',
            'checkout.order_summary' => 'Resum de la comanda',
            
            // Login
            'login.title' => 'Iniciar sessió',
            'login.password' => 'Contrasenya',
            'login.enter' => 'Entrar',
            'login.no_account' => 'No tens compte?',
            'login.register' => 'Registra\'t',
            'login.with' => 'O inicia sessió amb:',
            
            // General
            'filter.price' => 'FILTRAR PER PREU',
            'filter.apply' => 'Aplicar',
            'db.not_configured' => 'Base de dades no configurada',
            'db.install' => 'Instalar base de dades',
            'db.no_products' => 'No hi ha productes disponibles',
        ]
    ];
    
    /**
     * Obtener traducción por clave
     */
    public static function get($key, $lang = null) {
        $lang = $lang ?? CURRENT_LANG;
        return self::$translations[$lang][$key] ?? $key;
    }
    
    /**
     * Obtener nombre de producto según idioma
     */
    public static function getProductName($product, $lang = null) {
        $lang = $lang ?? CURRENT_LANG;
        if ($lang === 'ca') {
            return $product['name_ca'] ?? $product['name'] ?? '';
        }
        return $product['name'] ?? $product['name_ca'] ?? '';
    }
    
    /**
     * Obtener nombre de categoría según idioma
     */
    public static function getCategoryName($category, $lang = null) {
        $lang = $lang ?? CURRENT_LANG;
        if ($lang === 'ca') {
            return $category['name_ca'] ?? $category['name'] ?? '';
        }
        return $category['name'] ?? $category['name_ca'] ?? '';
    }
    
    /**
     * Obtener idioma actual
     */
    public static function current() {
        return CURRENT_LANG;
    }
    
    /**
     * Cambiar idioma
     */
    public static function set($lang) {
        if (in_array($lang, ['es', 'ca'])) {
            $_SESSION['lang'] = $lang;
            return true;
        }
        return false;
    }
}

