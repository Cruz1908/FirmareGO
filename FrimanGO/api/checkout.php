<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /checkout');
    exit;
}

// Validar que hay productos en el carrito
$cart = Cart::get();
if (empty($cart)) {
    header('Location: /cart');
    exit;
}

// Validar datos del formulario
$required = ['name', 'email', 'phone', 'address', 'city', 'postal_code'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        header('Location: /checkout?error=' . urlencode('Todos los campos son obligatorios'));
        exit;
    }
}

$paymentMethod = $_POST['payment_method'] ?? 'card';
$paymentIntentId = $_POST['payment_intent_id'] ?? null;

// Si es pago con tarjeta, verificar payment intent
if ($paymentMethod === 'card' && empty($paymentIntentId)) {
    header('Location: /checkout?error=' . urlencode('Error en el pago'));
    exit;
}

try {
    $shippingData = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'city' => $_POST['city'],
        'postal_code' => $_POST['postal_code']
    ];
    
    if ($paymentMethod === 'card') {
        // Crear orden con Stripe
        $orderNumber = Payment::createOrder($cart, $shippingData, $paymentIntentId);
    } else {
        // Pago en efectivo
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $orderNumber = 'FRM-' . date('Ymd') . '-' . strtoupper(substr(md5(time() . rand()), 0, 8));
        $userId = Auth::isLoggedIn() ? $_SESSION['user_id'] : null;
        $subtotal = Cart::getTotal();
        $shipping = 0;
        $tax = 0;
        $total = $subtotal + $shipping + $tax;
        
        $stmt = $conn->prepare("INSERT INTO orders (order_number, user_id, total, subtotal, shipping, tax, payment_method, payment_status, shipping_name, shipping_email, shipping_phone, shipping_address, shipping_city, shipping_postal_code) VALUES (?, ?, ?, ?, ?, ?, 'cash', 'pending', ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param('siddddsssssss',
            $orderNumber,
            $userId,
            $total,
            $subtotal,
            $shipping,
            $tax,
            $shippingData['name'],
            $shippingData['email'],
            $shippingData['phone'],
            $shippingData['address'],
            $shippingData['city'],
            $shippingData['postal_code']
        );
        
        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception('Error al crear la orden');
        }
        
        $orderId = $conn->insert_id;
        
        // Insertar items
        foreach ($cart as $item) {
            $itemTotal = floatval($item['price']) * intval($item['quantity']);
            $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, total) VALUES (?, ?, ?, ?, ?, ?)");
            $itemStmt->bind_param('iisidi',
                $orderId,
                $item['id'],
                $item['name'],
                $item['price'],
                $item['quantity'],
                $itemTotal
            );
            $itemStmt->execute();
            $itemStmt->close();
        }
        
        $stmt->close();
        Cart::clear();
    }
    
    header('Location: /order-complete?order=' . urlencode($orderNumber));
    
} catch (Exception $e) {
    header('Location: /checkout?error=' . urlencode($e->getMessage()));
}

exit;
