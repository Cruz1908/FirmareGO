<?php
/**
 * Gestión del carrito de compras - MySQL
 */
class Cart {
    private static function getCartKey() {
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        return $userId ? "cart_user_{$userId}" : 'cart_' . session_id();
    }

    public static function get() {
        $key = self::getCartKey();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : [];
    }

    public static function add($productId, $quantity = 1) {
        $product = Product::getById($productId);
        if (!$product) return false;

        $key = self::getCartKey();
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }

        $exists = false;
        foreach ($_SESSION[$key] as &$item) {
            if ($item['id'] == $productId) {
                $item['quantity'] += $quantity;
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $_SESSION[$key][] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'name_ca' => $product['name_ca'] ?? $product['name'],
                'price' => floatval($product['price']),
                'quantity' => intval($quantity),
                'unit' => $product['unit'],
                'image' => $product['image'] ?? null,
                'slug' => $product['slug'] ?? null
            ];
        }

        return true;
    }

    public static function update($productId, $quantity) {
        $key = self::getCartKey();
        if (!isset($_SESSION[$key])) return false;

        foreach ($_SESSION[$key] as &$item) {
            if ($item['id'] == $productId) {
                if ($quantity <= 0) {
                    return self::remove($productId);
                }
                $item['quantity'] = intval($quantity);
                return true;
            }
        }
        return false;
    }

    public static function remove($productId) {
        $key = self::getCartKey();
        if (!isset($_SESSION[$key])) return false;

        $_SESSION[$key] = array_filter($_SESSION[$key], function($item) use ($productId) {
            return $item['id'] != $productId;
        });
        $_SESSION[$key] = array_values($_SESSION[$key]);
        return true;
    }

    public static function clear() {
        $key = self::getCartKey();
        unset($_SESSION[$key]);
    }

    public static function getTotal() {
        $cart = self::get();
        $total = 0;
        foreach ($cart as $item) {
            $total += floatval($item['price']) * intval($item['quantity']);
        }
        return round($total, 2);
    }

    public static function getCount() {
        $cart = self::get();
        $count = 0;
        foreach ($cart as $item) {
            $count += intval($item['quantity']);
        }
        return $count;
    }
}
