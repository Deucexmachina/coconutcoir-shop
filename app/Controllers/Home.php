<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\StorefrontSettingModel;

class Home extends BaseController
{
    public function index(): string
    {
        $productModel = new ProductModel();
        $settingModel = new StorefrontSettingModel();

        $setting = $settingModel->find(1);

        return view('home/index', [
            'title' => 'Home',
            'setting' => $setting,
            'featured' => $productModel->where('is_active', 1)->where('is_featured', 1)->orderBy('id', 'DESC')->findAll(6),
        ]);
    }
}
