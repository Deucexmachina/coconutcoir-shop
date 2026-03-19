<?php

namespace App\Controllers;

use App\Models\CartItemModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\ProductReviewModel;
use App\Models\StorefrontSettingModel;
use App\Models\UserModel;
use App\Models\VoucherModel;

class BuyerController extends BaseController
{
    private function hasColumn(string $table, string $column): bool
    {
        return \Config\Database::connect()->fieldExists($column, $table);
    }

    private function hasTable(string $table): bool
    {
        return \Config\Database::connect()->tableExists($table);
    }

    private function isBuyerLoggedIn(): bool
    {
        $user = $this->currentUser();
        return $user !== null && ($user['role'] ?? null) === 'buyer';
    }

    private function guestCart(): array
    {
        $guestCart = $this->session->get('guest_cart');
        return is_array($guestCart) ? $guestCart : [];
    }

    private function saveGuestCart(array $cart): void
    {
        $clean = [];
        foreach ($cart as $pid => $qty) {
            $pid = (int) $pid;
            $qty = (int) $qty;
            if ($pid > 0 && $qty > 0) {
                $clean[$pid] = $qty;
            }
        }
        $this->session->set('guest_cart', $clean);
    }

    private function guestCartWithProducts(): array
    {
        $productModel = new ProductModel();
        $guestCart = $this->guestCart();
        if ($guestCart === []) {
            return [];
        }

        $ids = array_map('intval', array_keys($guestCart));
        $products = $productModel->whereIn('id', $ids)->where('is_active', 1)->findAll();
        $byId = [];
        foreach ($products as $product) {
            $byId[(int) $product['id']] = $product;
        }

        $items = [];
        foreach ($guestCart as $productId => $qty) {
            $productId = (int) $productId;
            $qty = (int) $qty;
            if (! isset($byId[$productId])) {
                continue;
            }
            $product = $byId[$productId];
            $items[] = [
                'id' => $productId,
                'product_id' => $productId,
                'quantity' => min($qty, (int) $product['stock']),
                'name' => $product['name'],
                'price' => $product['price'],
                'image_url' => $product['image_url'],
                'stock' => $product['stock'],
                'sold_count' => $product['sold_count'] ?? 0,
            ];
        }

        return $items;
    }

    private function cartWithProducts(int $userId): array
    {
        $cartModel = new CartItemModel();
        $select = 'cart_items.*, products.name, products.price, products.image_url, products.stock';
        if ($this->hasColumn('products', 'sold_count')) {
            $select .= ', products.sold_count';
        }

        return $cartModel
            ->select($select)
            ->join('products', 'products.id = cart_items.product_id')
            ->where('cart_items.user_id', $userId)
            ->findAll();
    }

    private function withLatestReviews(array $products): array
    {
        if ($products === []) {
            return $products;
        }

        $productIds = array_map(static fn(array $row): int => (int) $row['id'], $products);
        $reviewModel = new ProductReviewModel();
        $rows = $reviewModel
            ->select('product_reviews.product_id, product_reviews.rating, product_reviews.comment, product_reviews.created_at, users.full_name')
            ->join('users', 'users.id = product_reviews.user_id')
            ->whereIn('product_reviews.product_id', $productIds)
            ->orderBy('product_reviews.created_at', 'DESC')
            ->findAll();

        $latestByProduct = [];
        foreach ($rows as $row) {
            $pid = (int) $row['product_id'];
            if (! isset($latestByProduct[$pid])) {
                $latestByProduct[$pid] = $row;
            }
        }

        foreach ($products as &$product) {
            $pid = (int) $product['id'];
            $product['latest_review'] = $latestByProduct[$pid] ?? null;
        }

        return $products;
    }

    private function withSoldCounts(array $products): array
    {
        if ($products === []) {
            return $products;
        }

        $productIds = array_map(static fn(array $row): int => (int) $row['id'], $products);
        $baselineByProduct = $this->soldBaselines($productIds);

        if ($this->hasColumn('products', 'sold_count')) {
            foreach ($products as &$product) {
                $pid = (int) $product['id'];
                $actual = (int) ($product['sold_count'] ?? 0);
                $baseline = (int) ($baselineByProduct[$pid] ?? 1);
                $product['sold_count'] = $this->capSoldCount($actual > 0 ? $actual : $baseline);
            }
            unset($product);
            return $products;
        }

        if (! $this->hasTable('order_items')) {
            foreach ($products as &$product) {
                $pid = (int) $product['id'];
                $product['sold_count'] = $this->capSoldCount((int) ($baselineByProduct[$pid] ?? 1));
            }
            unset($product);
            return $products;
        }

        $rows = \Config\Database::connect()->table('order_items')
            ->select('product_id, SUM(quantity) AS sold_total')
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id')
            ->get()
            ->getResultArray();

        $soldByProduct = [];
        foreach ($rows as $row) {
            $soldByProduct[(int) $row['product_id']] = (int) ($row['sold_total'] ?? 0);
        }

        foreach ($products as &$product) {
            $pid = (int) $product['id'];
            $fromOrders = $soldByProduct[$pid] ?? 0;
            $baseline = (int) ($baselineByProduct[$pid] ?? 1);
            $product['sold_count'] = $this->capSoldCount($fromOrders > 0 ? $baseline + $fromOrders : $baseline);
        }
        unset($product);

        return $products;
    }

    private function capSoldCount(int $value): int
    {
        return min(49, max(0, $value));
    }

    private function soldBaselines(array $productIds): array
    {
        $baselines = [];
        foreach ($productIds as $pid) {
            $baselines[(int) $pid] = 1;
        }

        if ($productIds === [] || ! $this->hasTable('product_reviews')) {
            return $baselines;
        }

        $rows = \Config\Database::connect()->table('product_reviews')
            ->select('product_id, COUNT(*) AS review_count')
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $pid = (int) ($row['product_id'] ?? 0);
            $reviewCount = (int) ($row['review_count'] ?? 0);
            if ($pid > 0 && $reviewCount > 0) {
                $baselines[$pid] = max(3, $reviewCount * 2);
            }
        }

        return $baselines;
    }

    private function currentCartCount(): int
    {
        if ($this->isBuyerLoggedIn()) {
            $user = $this->currentUser();
            $row = \Config\Database::connect()->table('cart_items')
                ->selectSum('quantity', 'qty')
                ->where('user_id', (int) $user['id'])
                ->get()
                ->getRowArray();
            return (int) ($row['qty'] ?? 0);
        }

        $guestCart = $this->guestCart();
        $total = 0;
        foreach ($guestCart as $qty) {
            $total += (int) $qty;
        }

        return $total;
    }

    private function purchasedProductIds(int $userId): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('order_items')
            ->select('order_items.product_id')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('orders.user_id', $userId)
            ->groupBy('order_items.product_id')
            ->get()
            ->getResultArray();

        return array_map(static fn(array $row): int => (int) $row['product_id'], $rows);
    }

    private function resolveVoucher(string $rawCode, float $subtotal, float $shippingFee): array
    {
        $code = strtoupper(trim($rawCode));
        if ($code === '') {
            return [
                'code' => null,
                'type' => null,
                'label' => null,
                'discount_amount' => 0.0,
                'invalid_message' => null,
            ];
        }

        $voucherModel = new VoucherModel();
        $voucher = $voucherModel
            ->where('code', $code)
            ->where('is_active', 1)
            ->first();

        if (! $voucher) {
            return [
                'code' => null,
                'type' => null,
                'label' => null,
                'discount_amount' => 0.0,
                'invalid_message' => 'Invalid voucher code.',
            ];
        }

        $discountAmount = 0.0;
        $label = null;

        if ($voucher['type'] === 'free_shipping') {
            $discountAmount = $shippingFee;
            $label = 'Free Shipping';
        } elseif ($voucher['type'] === 'percent_discount') {
            $percent = (float) $voucher['value'];
            $discountAmount = round($subtotal * ($percent / 100), 2);
            $label = number_format($percent, 0) . '% Discount';
        }

        return [
            'code' => $voucher['code'],
            'type' => $voucher['type'],
            'label' => $label,
            'discount_amount' => $discountAmount,
            'invalid_message' => null,
        ];
    }

    public function storefront()
    {
        $productModel = new ProductModel();
        $settingModel = new StorefrontSettingModel();
        $featured = $productModel->where('is_active', 1)->where('is_featured', 1)->orderBy('id', 'DESC')->findAll();
        $trending = $productModel->where('is_active', 1)->where('is_trending', 1)->orderBy('id', 'DESC')->findAll();
        $bestSellers = $productModel->where('is_active', 1)->where('is_best_seller', 1)->orderBy('id', 'DESC')->findAll();

        return view('buyer/storefront', [
            'title' => 'Storefront',
            'setting' => $settingModel->find(1),
            'featured' => $this->withLatestReviews($this->withSoldCounts($featured)),
            'trending' => $this->withLatestReviews($this->withSoldCounts($trending)),
            'bestSellers' => $this->withLatestReviews($this->withSoldCounts($bestSellers)),
        ]);
    }

    public function products()
    {
        $productModel = new ProductModel();
        $settingModel = new StorefrontSettingModel();
        $search = trim((string) $this->request->getGet('q'));
        $sort = (string) $this->request->getGet('sort');
        $filters = $this->request->getGet('filters');
        $filters = is_array($filters) ? $filters : [];

        $builder = $productModel->where('is_active', 1);
        if ($search !== '') {
            $builder = $builder->groupStart()
                ->like('name', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }

        if (in_array('featured', $filters, true)) {
            $builder = $builder->where('is_featured', 1);
        }
        if (in_array('trending', $filters, true)) {
            $builder = $builder->where('is_trending', 1);
        }
        if (in_array('best_seller', $filters, true)) {
            $builder = $builder->where('is_best_seller', 1);
        }
        if (in_array('in_stock', $filters, true)) {
            $builder = $builder->where('stock >', 0);
        }

        if ($sort === 'price_asc') {
            $builder = $builder->orderBy('price', 'ASC');
        } elseif ($sort === 'price_desc') {
            $builder = $builder->orderBy('price', 'DESC');
        } elseif ($sort === 'alpha_asc') {
            $builder = $builder->orderBy('name', 'ASC');
        } elseif ($sort === 'alpha_desc') {
            $builder = $builder->orderBy('name', 'DESC');
        } elseif ($sort === 'release_desc' && $this->hasColumn('products', 'release_date')) {
            $builder = $builder->orderBy('release_date', 'DESC');
        } else {
            $builder = $builder->orderBy('id', 'DESC');
        }

        $products = $builder->findAll();

        $user = $this->currentUser();
        $purchasedProductIds = [];
        if ($user !== null && ($user['role'] ?? null) === 'buyer') {
            $purchasedProductIds = $this->purchasedProductIds((int) $user['id']);
        }

        return view('buyer/products', [
            'title' => 'Products',
            'setting' => $settingModel->find(1),
            'products' => $this->withLatestReviews($this->withSoldCounts($products)),
            'search' => $search,
            'sort' => $sort,
            'filters' => $filters,
            'purchasedProductIds' => $purchasedProductIds,
        ]);
    }

    public function product(int $productId)
    {
        $productModel = new ProductModel();
        $product = $productModel->where('id', $productId)->where('is_active', 1)->first();
        if (! $product) {
            return redirect()->to('/products')->with('error', 'Product not found.');
        }

        $withReview = $this->withLatestReviews($this->withSoldCounts([$product]));

        return view('buyer/product_detail', [
            'title' => $product['name'],
            'product' => $withReview[0],
        ]);
    }

    public function addToCart(int $productId)
    {
        $wantsJson = $this->request->isAJAX() || stripos((string) $this->request->getHeaderLine('Accept'), 'application/json') !== false;
        $productModel = new ProductModel();
        $product = $productModel->find($productId);
        if (! $product || (int) $product['is_active'] !== 1) {
            if ($wantsJson) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Product not found.',
                    'csrf' => ['tokenName' => csrf_token(), 'hash' => csrf_hash()],
                ]);
            }
            return redirect()->back()->with('error', 'Product not found.');
        }

        $qty = max(1, (int) $this->request->getPost('quantity'));
        $currentQty = 0;
        if ($this->isBuyerLoggedIn()) {
            $user = $this->currentUser();
            $cartModel = new CartItemModel();
            $existing = $cartModel->where('user_id', $user['id'])->where('product_id', $productId)->first();
            $currentQty = $existing ? (int) $existing['quantity'] : 0;
        } else {
            $guestCart = $this->guestCart();
            $currentQty = (int) ($guestCart[$productId] ?? 0);
        }

        if ($currentQty + $qty > (int) $product['stock']) {
            if ($wantsJson) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Quantity exceeds available stock.',
                    'csrf' => ['tokenName' => csrf_token(), 'hash' => csrf_hash()],
                ]);
            }
            return redirect()->back()->with('error', 'Quantity exceeds available stock.');
        }

        if ($this->isBuyerLoggedIn()) {
            $user = $this->currentUser();
            $cartModel = new CartItemModel();
            $existing = $cartModel->where('user_id', $user['id'])->where('product_id', $productId)->first();
            if ($existing) {
                $cartModel->update($existing['id'], ['quantity' => (int) $existing['quantity'] + $qty]);
            } else {
                $cartModel->insert(['user_id' => $user['id'], 'product_id' => $productId, 'quantity' => $qty]);
            }
        } else {
            $guestCart = $this->guestCart();
            $guestCart[$productId] = ((int) ($guestCart[$productId] ?? 0)) + $qty;
            $this->saveGuestCart($guestCart);
        }

        $redirectTo = (string) $this->request->getPost('redirect_to');
        if ($redirectTo === '') {
            $redirectTo = (string) previous_url();
        }
        if ($redirectTo === '') {
            $redirectTo = '/storefront';
        }

        if ($wantsJson) {
            return $this->response->setJSON([
                'success' => true,
                'name' => $product['name'] ?? '',
                'message' => 'Item added to cart!',
                'cart_count' => $this->currentCartCount(),
                'csrf' => ['tokenName' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        return redirect()
            ->to($redirectTo)
            ->with('cart_added', [
                'name' => $product['name'] ?? '',
            ]);
    }

    public function cart()
    {
        $items = $this->isBuyerLoggedIn()
            ? $this->cartWithProducts((int) $this->currentUser()['id'])
            : $this->guestCartWithProducts();
        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += (float) $item['price'] * (int) $item['quantity'];
        }

        $addresses = [];
        $defaultAddress = '';
        $vouchers = [];
        if ($this->isBuyerLoggedIn()) {
            $user = $this->currentUser();
            $orderModel = new OrderModel();
            $userModel = new UserModel();
            if ($this->hasColumn('orders', 'shipping_address')) {
                $previousOrders = $orderModel
                    ->select('shipping_address')
                    ->where('user_id', $user['id'])
                    ->where('shipping_address !=', '')
                    ->orderBy('id', 'DESC')
                    ->findAll();
                foreach ($previousOrders as $order) {
                    $address = trim((string) ($order['shipping_address'] ?? ''));
                    if ($address !== '' && ! in_array($address, $addresses, true)) {
                        $addresses[] = $address;
                    }
                }
            }

            $userRecord = $userModel->find($user['id']);
            $defaultAddress = trim((string) ($userRecord['address'] ?? ''));
            if ($defaultAddress !== '' && ! in_array($defaultAddress, $addresses, true)) {
                array_unshift($addresses, $defaultAddress);
            }

            if ($this->hasTable('vouchers')) {
                $voucherModel = new VoucherModel();
                $vouchers = $voucherModel->where('is_active', 1)->findAll();
            }
        }

        return view('buyer/cart', [
            'title' => 'Cart',
            'items' => $items,
            'subtotal' => $subtotal,
            'shippingEstimate' => 120.0,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'vouchers' => $vouchers,
            'canCheckout' => $this->isBuyerLoggedIn(),
        ]);
    }

    public function updateCart(int $cartId)
    {
        $qty = max(1, (int) $this->request->getPost('quantity'));
        if ($this->isBuyerLoggedIn()) {
            $cartModel = new CartItemModel();
            $user = $this->currentUser();
            $item = $cartModel->where('id', $cartId)->where('user_id', $user['id'])->first();
            if (! $item) {
                return redirect()->to('/cart')->with('error', 'Cart item not found.');
            }

            $cartModel->update($cartId, ['quantity' => $qty]);
            return redirect()->to('/cart')->with('success', 'Cart updated.');
        }

        $guestCart = $this->guestCart();
        if (! isset($guestCart[$cartId])) {
            return redirect()->to('/cart')->with('error', 'Cart item not found.');
        }
        $guestCart[$cartId] = $qty;
        $this->saveGuestCart($guestCart);
        return redirect()->to('/cart')->with('success', 'Cart updated.');
    }

    public function removeCart(int $cartId)
    {
        if ($this->isBuyerLoggedIn()) {
            $cartModel = new CartItemModel();
            $user = $this->currentUser();
            $cartModel->where('id', $cartId)->where('user_id', $user['id'])->delete();
        } else {
            $guestCart = $this->guestCart();
            unset($guestCart[$cartId]);
            $this->saveGuestCart($guestCart);
        }

        return redirect()->to('/cart')->with('success', 'Item removed from cart.');
    }

    public function checkout()
    {
        if (! $this->isBuyerLoggedIn()) {
            return redirect()->to('/login?redirect_to=' . rawurlencode((string) base_url('/checkout')))
                ->with('error', 'Please log in to continue checkout. Your cart is saved.');
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

        $orderModel = new OrderModel();
        $userModel = new UserModel();
        $vouchers = [];
        if ($this->hasTable('vouchers')) {
            $voucherModel = new VoucherModel();
            $vouchers = $voucherModel->where('is_active', 1)->findAll();
        }
        $addresses = [];
        if ($this->hasColumn('orders', 'shipping_address')) {
            $previousOrders = $orderModel
                ->select('shipping_address')
                ->where('user_id', $user['id'])
                ->where('shipping_address !=', '')
                ->orderBy('id', 'DESC')
                ->findAll();
            foreach ($previousOrders as $order) {
                $address = trim((string) ($order['shipping_address'] ?? ''));
                if ($address !== '' && ! in_array($address, $addresses, true)) {
                    $addresses[] = $address;
                }
            }
        }

        $userRecord = $userModel->find($user['id']);
        $defaultAddress = trim((string) ($userRecord['address'] ?? ''));
        if ($defaultAddress !== '' && ! in_array($defaultAddress, $addresses, true)) {
            array_unshift($addresses, $defaultAddress);
        }

        return view('buyer/checkout', [
            'title' => 'Checkout',
            'items' => $items,
            'subtotal' => $subtotal,
            'shippingEstimate' => 120.0,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'vouchers' => $vouchers,
        ]);
    }

    public function placeOrder()
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $paymentMethod = (string) $this->request->getPost('payment_method');
        $deliveryMethod = (string) $this->request->getPost('delivery_method');
        $shippingAddress = trim((string) $this->request->getPost('shipping_address'));
        $voucherCode = (string) $this->request->getPost('voucher_code');

        if (! in_array($paymentMethod, ['Cash on Delivery', 'Credit/Debit Card', 'E-Wallet'], true)) {
            return redirect()->back()->with('error', 'Invalid payment method.');
        }
        if (! in_array($deliveryMethod, ['Pickup', 'Delivery'], true)) {
            return redirect()->back()->with('error', 'Invalid delivery method.');
        }
        if ($shippingAddress === '') {
            return redirect()->back()->with('error', 'Please provide a shipping address.');
        }

        $user = $this->currentUser();
        $items = $this->cartWithProducts((int) $user['id']);
        if ($items === []) {
            return redirect()->to('/products')->with('error', 'Cart is empty.');
        }

        $subtotal = 0.0;
        foreach ($items as $item) {
            if ((int) $item['quantity'] > (int) $item['stock']) {
                return redirect()->to('/cart')->with('error', 'Insufficient stock for ' . $item['name']);
            }
            $subtotal += (float) $item['price'] * (int) $item['quantity'];
        }

        $shippingFee = $deliveryMethod === 'Delivery' ? 120.0 : 0.0;
        $voucher = ['code' => null, 'type' => null, 'label' => null, 'discount_amount' => 0.0, 'invalid_message' => null];
        if ($this->hasTable('vouchers')) {
            $voucher = $this->resolveVoucher($voucherCode, $subtotal, $shippingFee);
        }
        if ($voucher['invalid_message']) {
            return redirect()->back()->with('error', $voucher['invalid_message']);
        }
        $total = max(0, $subtotal + $shippingFee - (float) $voucher['discount_amount']);

        $db = \Config\Database::connect();
        $db->transStart();

        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $cartModel = new CartItemModel();

        $orderData = [
            'user_id' => $user['id'],
            'total_amount' => $total,
            'payment_method' => $paymentMethod,
            'delivery_method' => $deliveryMethod,
            'status' => 'Placed',
        ];
        if ($this->hasColumn('orders', 'subtotal_amount')) {
            $orderData['subtotal_amount'] = $subtotal;
        }
        if ($this->hasColumn('orders', 'shipping_fee')) {
            $orderData['shipping_fee'] = $shippingFee;
        }
        if ($this->hasColumn('orders', 'voucher_code')) {
            $orderData['voucher_code'] = $voucher['code'];
        }
        if ($this->hasColumn('orders', 'voucher_type')) {
            $orderData['voucher_type'] = $voucher['label'];
        }
        if ($this->hasColumn('orders', 'voucher_discount_amount')) {
            $orderData['voucher_discount_amount'] = $voucher['discount_amount'];
        }
        if ($this->hasColumn('orders', 'shipping_address')) {
            $orderData['shipping_address'] = $shippingAddress;
        }

        $orderId = $orderModel->insert($orderData, true);

        foreach ($items as $item) {
            $lineTotal = (float) $item['price'] * (int) $item['quantity'];
            $orderItemModel->insert([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price_each' => $item['price'],
                'line_total' => $lineTotal,
            ]);

            $qty = (int) $item['quantity'];
            $productTable = $db->table('products');
            $productTable->set('stock', 'stock - ' . $qty, false);
            if ($this->hasColumn('products', 'sold_count')) {
                $productTable->set('sold_count', 'sold_count + ' . $qty, false);
            }
            $productTable->where('id', (int) $item['product_id'])->update();
        }

        $cartModel->where('user_id', $user['id'])->delete();

        $db->transComplete();

        return redirect()->to('/profile#my-orders')->with('success', 'Order placed successfully.');
    }

    private function transactionDataByUserId(int $userId): array
    {
        $orderModel = new OrderModel();
        $reviewModel = new ProductReviewModel();

        $orders = $orderModel->where('user_id', $userId)->orderBy('id', 'DESC')->findAll();
        $orderIds = array_map(static fn(array $row): int => (int) $row['id'], $orders);

        $orderItemsByOrder = [];
        if ($orderIds !== []) {
            $db = \Config\Database::connect();
            $orderItems = $db->table('order_items')
                ->select('order_items.*, products.name, products.image_url')
                ->join('products', 'products.id = order_items.product_id')
                ->whereIn('order_items.order_id', $orderIds)
                ->orderBy('order_items.order_id', 'DESC')
                ->get()
                ->getResultArray();

            foreach ($orderItems as $item) {
                $oid = (int) $item['order_id'];
                if (! isset($orderItemsByOrder[$oid])) {
                    $orderItemsByOrder[$oid] = [];
                }
                $orderItemsByOrder[$oid][] = $item;
            }
        }

        $reviewRows = $reviewModel->where('user_id', $userId)->findAll();
        $reviewsByProduct = [];
        foreach ($reviewRows as $row) {
            $reviewsByProduct[(int) $row['product_id']] = $row;
        }

        return [
            'orders' => $orders,
            'orderItemsByOrder' => $orderItemsByOrder,
            'reviewsByProduct' => $reviewsByProduct,
        ];
    }

    public function transactions()
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        return redirect()->to('/profile#my-orders');
    }

    public function submitReview(int $productId)
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $user = $this->currentUser();
        $rating = (int) $this->request->getPost('rating');
        $comment = trim((string) $this->request->getPost('comment'));

        if ($rating < 1 || $rating > 5) {
            return redirect()->back()->with('error', 'Please select a rating from 1 to 5.');
        }

        $db = \Config\Database::connect();
        $purchase = $db->table('order_items')
            ->select('order_items.id')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('orders.user_id', $user['id'])
            ->where('order_items.product_id', $productId)
            ->get()
            ->getRowArray();

        if (! $purchase) {
            return redirect()->back()->with('error', 'You can only review products you have purchased.');
        }

        $reviewModel = new ProductReviewModel();
        $existing = $reviewModel
            ->where('user_id', $user['id'])
            ->where('product_id', $productId)
            ->first();

        $payload = [
            'user_id' => $user['id'],
            'product_id' => $productId,
            'rating' => $rating,
            'comment' => $comment !== '' ? $comment : null,
        ];

        if ($existing) {
            $reviewModel->update($existing['id'], $payload);
        } else {
            $reviewModel->insert($payload);
        }

        return redirect()->back()->with('success', 'Review submitted.');
    }

    public function profile()
    {
        if ($guard = $this->requireBuyer()) {
            return $guard;
        }

        $user = $this->currentUser();
        $userModel = new UserModel();
        $transactionData = $this->transactionDataByUserId((int) $user['id']);

        return view('buyer/profile', [
            'title' => 'Profile',
            'user' => $userModel->find($user['id']),
            'orders' => $transactionData['orders'],
            'orderItemsByOrder' => $transactionData['orderItemsByOrder'],
            'reviewsByProduct' => $transactionData['reviewsByProduct'],
        ]);
    }
}
