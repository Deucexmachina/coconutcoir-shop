<?php

namespace App\Controllers;

use App\Models\CartItemModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\StorefrontSettingModel;
use App\Models\UserModel;

class BuyerController extends BaseController
{
    private function cartWithProducts(int $userId): array
    {
        $cartModel = new CartItemModel();

        return $cartModel
            ->select('cart_items.*, products.name, products.price, products.image_url, products.stock')
            ->join('products', 'products.id = cart_items.product_id')
            ->where('cart_items.user_id', $userId)
            ->findAll();
    }

    public function storefront()
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $productModel = new ProductModel();
        $settingModel = new StorefrontSettingModel();

        return view('buyer/storefront', [
            'title' => 'Storefront',
            'setting' => $settingModel->find(1),
            'newProducts' => $productModel->where('is_active', 1)->orderBy('id', 'DESC')->findAll(4),
            'trending' => $productModel->where('is_active', 1)->where('is_trending', 1)->findAll(4),
            'bestSellers' => $productModel->where('is_active', 1)->where('is_best_seller', 1)->findAll(4),
        ]);
    }

    public function products()
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $productModel = new ProductModel();

        return view('buyer/products', [
            'title' => 'Products',
            'products' => $productModel->where('is_active', 1)->findAll(),
        ]);
    }

    public function addToCart(int $productId)
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $productModel = new ProductModel();
        $product = $productModel->find($productId);
        if (! $product || (int) $product['is_active'] !== 1) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $qty = max(1, (int) $this->request->getPost('quantity'));
        $user = $this->currentUser();

        $cartModel = new CartItemModel();
        $existing = $cartModel->where('user_id', $user['id'])->where('product_id', $productId)->first();

        if ($existing) {
            $cartModel->update($existing['id'], ['quantity' => $existing['quantity'] + $qty]);
        } else {
            $cartModel->insert(['user_id' => $user['id'], 'product_id' => $productId, 'quantity' => $qty]);
        }

        $redirectTo = (string) $this->request->getPost('redirect_to');
        if ($redirectTo === '') {
            $redirectTo = (string) previous_url();
        }
        if ($redirectTo === '') {
            $redirectTo = '/storefront';
        }

        return redirect()
            ->to($redirectTo)
            ->with('cart_added', [
                'name' => $product['name'] ?? '',
            ]);
    }

    public function cart()
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $user = $this->currentUser();
        $items = $this->cartWithProducts((int) $user['id']);
        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += (float) $item['price'] * (int) $item['quantity'];
        }

        return view('buyer/cart', [
            'title' => 'Cart',
            'items' => $items,
            'subtotal' => $subtotal,
        ]);
    }

    public function updateCart(int $cartId)
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $qty = max(1, (int) $this->request->getPost('quantity'));
        $cartModel = new CartItemModel();
        $user = $this->currentUser();
        $item = $cartModel->where('id', $cartId)->where('user_id', $user['id'])->first();
        if (! $item) {
            return redirect()->to('/cart')->with('error', 'Cart item not found.');
        }

        $cartModel->update($cartId, ['quantity' => $qty]);
        return redirect()->to('/cart')->with('success', 'Cart updated.');
    }

    public function removeCart(int $cartId)
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $cartModel = new CartItemModel();
        $user = $this->currentUser();
        $cartModel->where('id', $cartId)->where('user_id', $user['id'])->delete();

        return redirect()->to('/cart')->with('success', 'Item removed from cart.');
    }

    public function checkout()
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $user = $this->currentUser();
        $items = $this->cartWithProducts((int) $user['id']);

        if ($items === []) {
            return redirect()->to('/products')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += (float) $item['price'] * (int) $item['quantity'];
        }

        return view('buyer/checkout', [
            'title' => 'Checkout',
            'items' => $items,
            'subtotal' => $subtotal,
        ]);
    }

    public function placeOrder()
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $paymentMethod = (string) $this->request->getPost('payment_method');
        $deliveryMethod = (string) $this->request->getPost('delivery_method');
        if (! in_array($paymentMethod, ['Cash on Delivery', 'GCash', 'Bank Transfer'], true)) {
            return redirect()->back()->with('error', 'Invalid payment method.');
        }
        if (! in_array($deliveryMethod, ['Pickup', 'Delivery'], true)) {
            return redirect()->back()->with('error', 'Invalid delivery method.');
        }

        $user = $this->currentUser();
        $items = $this->cartWithProducts((int) $user['id']);
        if ($items === []) {
            return redirect()->to('/products')->with('error', 'Cart is empty.');
        }

        $total = 0.0;
        foreach ($items as $item) {
            if ((int) $item['quantity'] > (int) $item['stock']) {
                return redirect()->to('/cart')->with('error', 'Insufficient stock for ' . $item['name']);
            }
            $total += (float) $item['price'] * (int) $item['quantity'];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $productModel = new ProductModel();
        $cartModel = new CartItemModel();

        $orderId = $orderModel->insert([
            'user_id' => $user['id'],
            'total_amount' => $total,
            'payment_method' => $paymentMethod,
            'delivery_method' => $deliveryMethod,
            'status' => 'Placed',
        ], true);

        foreach ($items as $item) {
            $lineTotal = (float) $item['price'] * (int) $item['quantity'];
            $orderItemModel->insert([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price_each' => $item['price'],
                'line_total' => $lineTotal,
            ]);

            $productModel->update($item['product_id'], [
                'stock' => (int) $item['stock'] - (int) $item['quantity'],
            ]);
        }

        $cartModel->where('user_id', $user['id'])->delete();

        $db->transComplete();

        return redirect()->to('/transactions')->with('success', 'Order placed successfully.');
    }

    public function transactions()
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $user = $this->currentUser();
        $orderModel = new OrderModel();

        $orders = $orderModel->where('user_id', $user['id'])->orderBy('id', 'DESC')->findAll();

        return view('buyer/transactions', [
            'title' => 'Transaction History',
            'orders' => $orders,
        ]);
    }

    public function profile()
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $user = $this->currentUser();
        $userModel = new UserModel();

        return view('buyer/profile', [
            'title' => 'Profile',
            'user' => $userModel->find($user['id']),
        ]);
    }
}
