<?php
/**
 * Gestión de pagos con Stripe
 */
class Payment {
    public static function createPaymentIntent($amount, $currency = 'eur', $metadata = []) {
        if (empty(STRIPE_SECRET_KEY) || strpos(STRIPE_SECRET_KEY, 'sk_test') === false) {
            throw new Exception('Stripe no está configurado correctamente');
        }

        $url = 'https://api.stripe.com/v1/payment_intents';
        $data = [
            'amount' => intval($amount * 100), // Stripe usa centavos
            'currency' => $currency,
            'metadata' => $metadata
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . STRIPE_SECRET_KEY
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $error = json_decode($response, true);
            throw new Exception($error['error']['message'] ?? 'Error al crear pago');
        }

        return json_decode($response, true);
    }

    public static function createOrder($cart, $shippingData, $paymentIntentId = null) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // Generar número de orden
        $orderNumber = 'FRM-' . date('Ymd') . '-' . strtoupper(substr(md5(time() . rand()), 0, 8));
        
        $userId = Auth::isLoggedIn() ? $_SESSION['user_id'] : null;
        $subtotal = Cart::getTotal();
        $shipping = 0; // Gratis por ahora
        $tax = 0;
        $total = $subtotal + $shipping + $tax;
        
        $stmt = $conn->prepare("INSERT INTO orders (order_number, user_id, total, subtotal, shipping, tax, payment_method, payment_status, payment_id, shipping_name, shipping_email, shipping_phone, shipping_address, shipping_city, shipping_postal_code) VALUES (?, ?, ?, ?, ?, ?, 'stripe', 'paid', ?, ?, ?, ?, ?, ?, ?)");
        
        $paymentMethod = 'stripe';
        $stmt->bind_param('siddddsssssss',
            $orderNumber,
            $userId,
            $total,
            $subtotal,
            $shipping,
            $tax,
            $paymentIntentId,
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
        
        // Limpiar carrito
        Cart::clear();
        
        return $orderNumber;
    }
}


