<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $userData = [
            [
                'email' => 'seller@coircraft.local',
                'password_hash' => password_hash('Seller123!', PASSWORD_DEFAULT),
                'full_name' => 'CoirCraft Seller',
                'address' => 'Main Warehouse, Davao City',
                'mobile_number' => '09171234567',
                'role' => 'seller',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'email' => 'buyer@coircraft.local',
                'password_hash' => password_hash('Buyer123!', PASSWORD_DEFAULT),
                'full_name' => 'Default Buyer',
                'address' => 'Sample Street, Tagum City',
                'mobile_number' => '09991234567',
                'role' => 'buyer',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($userData);

        $productData = [
            [
                'name' => 'Coir Rope Bundle',
                'description' => 'Durable coconut coir rope bundle for gardening and crafts.',
                'price' => 145.00,
                'stock' => 80,
                'image_url' => 'https://images.unsplash.com/photo-1616628182505-4f6fb9f2b805?auto=format&fit=crop&w=900&q=80',
                'is_featured' => 1,
                'is_trending' => 1,
                'is_best_seller' => 0,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Coir Door Mat',
                'description' => 'Eco-friendly and water-absorbent coir doormat for home entrances.',
                'price' => 320.00,
                'stock' => 54,
                'image_url' => 'https://images.unsplash.com/photo-1623854767648-e7bb8009f0db?auto=format&fit=crop&w=900&q=80',
                'is_featured' => 1,
                'is_trending' => 0,
                'is_best_seller' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Coir Grow Pole',
                'description' => 'Support pole made from coconut fibers, ideal for climbing plants.',
                'price' => 210.00,
                'stock' => 70,
                'image_url' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=900&q=80',
                'is_featured' => 0,
                'is_trending' => 1,
                'is_best_seller' => 0,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Coir Geotextile Roll',
                'description' => 'Biodegradable coir net roll used for erosion control in landscaping.',
                'price' => 590.00,
                'stock' => 35,
                'image_url' => 'https://images.unsplash.com/photo-1472145246862-b24cf25c4a36?auto=format&fit=crop&w=900&q=80',
                'is_featured' => 0,
                'is_trending' => 0,
                'is_best_seller' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('products')->insertBatch($productData);

        $this->db->table('storefront_settings')->insert([
            'hero_title' => 'CoirCraft Coconut Coir Products',
            'hero_subtitle' => 'Sustainable products for homes, farms, and construction',
            'announcement' => 'Now shipping nationwide with pickup and delivery options.',
        ]);
    }
}
