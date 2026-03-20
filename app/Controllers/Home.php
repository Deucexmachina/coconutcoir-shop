<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\StorefrontSettingModel;

class Home extends BaseController
{
    public function index(): string
    {
        $setting = [];
        $featured = [];

        try {
            $productModel = new ProductModel();
            $settingModel = new StorefrontSettingModel();

            $setting = $settingModel->find(1) ?? [];
            $featured = $productModel->where('is_active', 1)
                ->where('is_featured', 1)
                ->orderBy('id', 'DESC')
                ->findAll(6);
        } catch (\Throwable $e) {
            log_message('error', 'Home page database query failed: {message}', ['message' => $e->getMessage()]);
        }

        return view('home/index', [
            'title' => 'Home',
            'setting' => $setting,
            'featured' => $featured,
        ]);
    }
}
