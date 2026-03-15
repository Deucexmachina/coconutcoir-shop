<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\StorefrontSettingModel;

class SellerController extends BaseController
{
    public function storefront()
    {
        if ($guard = $this->requireSeller()) {
            return $guard;
        }

        $settingModel = new StorefrontSettingModel();

        return view('seller/storefront', [
            'title' => 'Storefront Management',
            'setting' => $settingModel->find(1),
        ]);
    }

    public function updateStorefront()
    {
        if ($guard = $this->requireSeller()) {
            return $guard;
        }

        $settingModel = new StorefrontSettingModel();
        $setting = $settingModel->find(1);

        $data = [
            'hero_title' => (string) $this->request->getPost('hero_title'),
            'hero_subtitle' => (string) $this->request->getPost('hero_subtitle'),
            'announcement' => (string) $this->request->getPost('announcement'),
        ];

        if ($setting) {
            $settingModel->update(1, $data);
        } else {
            $settingModel->insert($data);
        }

        return redirect()->to('/seller/storefront')->with('success', 'Storefront updated.');
    }

    public function inventory()
    {
        if ($guard = $this->requireSeller()) {
            return $guard;
        }

        $productModel = new ProductModel();

        return view('seller/inventory', [
            'title' => 'Inventory',
            'products' => $productModel->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function createProduct()
    {
        if ($guard = $this->requireSeller()) {
            return $guard;
        }

        $productModel = new ProductModel();
        $productModel->insert([
            'name' => (string) $this->request->getPost('name'),
            'description' => (string) $this->request->getPost('description'),
            'price' => (float) $this->request->getPost('price'),
            'stock' => (int) $this->request->getPost('stock'),
            'image_url' => (string) $this->request->getPost('image_url'),
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'is_trending' => $this->request->getPost('is_trending') ? 1 : 0,
            'is_best_seller' => $this->request->getPost('is_best_seller') ? 1 : 0,
            'is_active' => 1,
        ]);

        return redirect()->to('/seller/inventory')->with('success', 'Product created.');
    }

    public function updateProduct(int $id)
    {
        if ($guard = $this->requireSeller()) {
            return $guard;
        }

        $productModel = new ProductModel();
        $productModel->update($id, [
            'name' => (string) $this->request->getPost('name'),
            'description' => (string) $this->request->getPost('description'),
            'price' => (float) $this->request->getPost('price'),
            'stock' => (int) $this->request->getPost('stock'),
            'image_url' => (string) $this->request->getPost('image_url'),
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'is_trending' => $this->request->getPost('is_trending') ? 1 : 0,
            'is_best_seller' => $this->request->getPost('is_best_seller') ? 1 : 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/seller/inventory')->with('success', 'Product updated.');
    }

    public function reports()
    {
        if ($guard = $this->requireSeller()) {
            return $guard;
        }

        $orderModel = new OrderModel();
        $productModel = new ProductModel();

        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $monthStart = date('Y-m-01 00:00:00');

        $dailySales = $orderModel
            ->selectSum('total_amount')
            ->where('created_at >=', $todayStart)
            ->where('created_at <=', $todayEnd)
            ->first();

        $monthlySales = $orderModel
            ->selectSum('total_amount')
            ->where('created_at >=', $monthStart)
            ->first();

        $inventoryRows = $productModel->findAll();

        return view('seller/reports', [
            'title' => 'Reports',
            'dailyTotal' => (float) ($dailySales['total_amount'] ?? 0),
            'monthlyTotal' => (float) ($monthlySales['total_amount'] ?? 0),
            'inventoryRows' => $inventoryRows,
        ]);
    }
}
