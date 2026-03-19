<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->tableExists('cart_items')) {
            $this->db->table('cart_items')->emptyTable();
        }
        if ($this->db->tableExists('order_items')) {
            $this->db->table('order_items')->emptyTable();
        }
        if ($this->db->tableExists('product_reviews')) {
            $this->db->table('product_reviews')->emptyTable();
        }
        if ($this->db->tableExists('products')) {
            $this->db->table('products')->emptyTable();
        }
        if ($this->db->tableExists('vouchers')) {
            $this->db->table('vouchers')->emptyTable();
        }
        if ($this->db->tableExists('storefront_settings')) {
            $this->db->table('storefront_settings')->emptyTable();
        }

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
                'full_name' => 'buyer_quezon',
                'address' => 'Bagumbayan, Quezon City',
                'mobile_number' => '09991234567',
                'role' => 'buyer',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'email' => 'mara@coircraft.local',
                'password_hash' => password_hash('Buyer123!', PASSWORD_DEFAULT),
                'full_name' => 'mara_garden',
                'address' => 'Poblacion, Makati City',
                'mobile_number' => '09995671234',
                'role' => 'buyer',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'email' => 'leo@coircraft.local',
                'password_hash' => password_hash('Buyer123!', PASSWORD_DEFAULT),
                'full_name' => 'leo_planter',
                'address' => 'Loyola Heights, Quezon City',
                'mobile_number' => '09992345678',
                'role' => 'buyer',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($userData as $userRow) {
            $existing = $this->db->table('users')->select('id')->where('email', $userRow['email'])->get()->getRowArray();
            if ($existing) {
                $this->db->table('users')->where('id', (int) $existing['id'])->update($userRow);
            } else {
                $this->db->table('users')->insert($userRow);
            }
        }

        $productData = [
            ['name' => 'Coco Coir Pot 4-inch', 'description' => 'Biodegradable coir pot for herbs and seedling starts.', 'additional_details' => "Material: Coir fiber and natural binder\nDiameter: 4-inch\nColor: Natural brown\nUse: Seedling nursery and transplanting\nCare: Keep evenly moist", 'price' => 58.00, 'stock' => 180, 'sold_count' => 47, 'image_url' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-01-15', 'is_featured' => 1, 'is_trending' => 1, 'is_best_seller' => 1, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coco Coir Pot 6-inch', 'description' => 'Medium-sized coir pot for leafy vegetable starts.', 'additional_details' => "Material: Coir fiber\nDiameter: 6-inch\nColor: Warm brown\nUse: Nursery pots\nCare: Place on tray before watering", 'price' => 78.00, 'stock' => 140, 'sold_count' => 39, 'image_url' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-01-25', 'is_featured' => 1, 'is_trending' => 0, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Classic Coir Doormat', 'description' => 'Dense coir doormat for entrance dirt trapping.', 'additional_details' => "Material: Coir with anti-slip backing\nSize: 18x30 inches\nColor: Dark tan\nUse: Main entryway\nCare: Shake and brush", 'price' => 320.00, 'stock' => 72, 'sold_count' => 59, 'image_url' => 'https://images.unsplash.com/photo-1623854767648-e7bb8009f0db?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-02-01', 'is_featured' => 1, 'is_trending' => 1, 'is_best_seller' => 1, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Printed Coir Welcome Mat', 'description' => 'Decorative welcome mat made with coir fibers.', 'additional_details' => "Material: Coir and rubber\nSize: 24x36 inches\nColor: Brown and black\nUse: Outdoor doorstep\nCare: Dry brush cleaning", 'price' => 385.00, 'stock' => 51, 'sold_count' => 28, 'image_url' => 'https://images.unsplash.com/photo-1556170717-a3f5f11664bb?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-02-10', 'is_featured' => 0, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Grow Pole 2ft', 'description' => 'Plant support pole wrapped in coconut coir.', 'additional_details' => "Material: Coir and PVC core\nHeight: 2ft\nColor: Brown\nUse: Climbing plants\nCare: Mist regularly", 'price' => 210.00, 'stock' => 88, 'sold_count' => 34, 'image_url' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-02-18', 'is_featured' => 0, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Grow Pole 3ft', 'description' => 'Extended coir pole for mature monstera and philodendrons.', 'additional_details' => "Material: Coir rope wrap\nHeight: 3ft\nColor: Earth brown\nUse: Indoor climbing plants\nCare: Keep fiber moist", 'price' => 260.00, 'stock' => 62, 'sold_count' => 25, 'image_url' => 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-02-24', 'is_featured' => 1, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Geotextile Roll 400gsm', 'description' => 'Biodegradable coir netting for slope stabilization.', 'additional_details' => "Material: Woven coir yarn\nWeight: 400gsm\nSize: 1m x 10m\nUse: Erosion control\nCare: Secure with pins", 'price' => 590.00, 'stock' => 29, 'sold_count' => 17, 'image_url' => 'https://images.unsplash.com/photo-1472145246862-b24cf25c4a36?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-03-02', 'is_featured' => 0, 'is_trending' => 0, 'is_best_seller' => 1, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Geotextile Roll 700gsm', 'description' => 'Heavier coir blanket for steeper land applications.', 'additional_details' => "Material: Dense woven coir\nWeight: 700gsm\nSize: 2m x 10m\nUse: Heavy slope protection\nCare: Lay over compacted surface", 'price' => 740.00, 'stock' => 22, 'sold_count' => 12, 'image_url' => 'https://images.unsplash.com/photo-1464146072230-91cabc968266?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-03-10', 'is_featured' => 0, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Rope Bundle 10m', 'description' => 'Natural coir rope for general garden tying.', 'additional_details' => "Material: 100% coir fiber\nLength: 10m\nThickness: 6mm\nUse: Plant tie support\nCare: Keep dry", 'price' => 142.00, 'stock' => 120, 'sold_count' => 44, 'image_url' => 'https://images.unsplash.com/photo-1616628182505-4f6fb9f2b805?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-03-18', 'is_featured' => 1, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Twine 3mm', 'description' => 'Fine twine ideal for tying stems and bundles.', 'additional_details' => "Material: Spun coir yarn\nLength: 50m\nGauge: 3mm\nUse: Home gardening\nCare: Store sealed", 'price' => 88.00, 'stock' => 170, 'sold_count' => 66, 'image_url' => 'https://images.unsplash.com/photo-1516822003754-cca485356ecb?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-03-25', 'is_featured' => 0, 'is_trending' => 0, 'is_best_seller' => 1, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Dust Brick 650g', 'description' => 'Compressed coir pith brick for potting soil mix.', 'additional_details' => "Material: Coir dust\nWeight: 650g\nExpansion: Up to 8L\nUse: Potting mix base\nCare: Soak before use", 'price' => 72.00, 'stock' => 240, 'sold_count' => 95, 'image_url' => 'https://images.unsplash.com/photo-1459156212016-c812468e2115?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-04-08', 'is_featured' => 1, 'is_trending' => 1, 'is_best_seller' => 1, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Buffered Coir Grow Bag', 'description' => 'Buffered coir medium bag for hydroponic use.', 'additional_details' => "Material: Washed coir pith\nWeight: 1kg\npH: 5.8 to 6.5\nUse: Hydroponics\nCare: Flush before feeding", 'price' => 225.00, 'stock' => 74, 'sold_count' => 23, 'image_url' => 'https://images.unsplash.com/photo-1446071103084-c257b5f70672?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-04-14', 'is_featured' => 1, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Potting Mix 5L', 'description' => 'Ready-to-use coir blend for indoor plants.', 'additional_details' => "Material: Coir pith and chips\nVolume: 5L\nUse: Houseplants\nDrainage: High\nCare: Water when top layer dries", 'price' => 190.00, 'stock' => 86, 'sold_count' => 42, 'image_url' => 'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-04-20', 'is_featured' => 0, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Seed Starter Mat', 'description' => 'Water-retentive coir mat for germination trays.', 'additional_details' => "Material: Fine coir felt\nSize: Tray fit 10x20\nUse: Germination\nWater hold: High\nCare: Keep consistently damp", 'price' => 128.00, 'stock' => 128, 'sold_count' => 38, 'image_url' => 'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-04-28', 'is_featured' => 1, 'is_trending' => 0, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Nursery Tray Liner Pack', 'description' => 'Pack of coir liners for nursery trays.', 'additional_details' => "Material: Coir felt\nPack size: 10 liners\nUse: Nursery propagation\nColor: Natural brown\nCare: Rinse and air dry", 'price' => 118.00, 'stock' => 104, 'sold_count' => 27, 'image_url' => 'https://images.unsplash.com/photo-1463320726281-696a485928c7?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-05-03', 'is_featured' => 0, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Hanging Basket Liner 12-inch', 'description' => 'Coconut liner designed for hanging planters.', 'additional_details' => "Material: Needle-punched coir\nDiameter: 12-inch\nUse: Hanging baskets\nColor: Dark brown\nCare: Replace seasonally", 'price' => 96.00, 'stock' => 160, 'sold_count' => 72, 'image_url' => 'https://images.unsplash.com/photo-1463320726281-696a485928c7?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-05-10', 'is_featured' => 0, 'is_trending' => 1, 'is_best_seller' => 1, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Basket Planter 12-inch', 'description' => 'Metal basket with replaceable coir liner.', 'additional_details' => "Material: Steel frame and coir liner\nSize: 12-inch\nUse: Balcony plants\nDrainage: Excellent\nCare: Re-line when thin", 'price' => 290.00, 'stock' => 53, 'sold_count' => 21, 'image_url' => 'https://images.unsplash.com/photo-1463936575829-25148e1db1b8?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-05-16', 'is_featured' => 1, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Mulch Ring', 'description' => 'Pre-cut mulch ring for potted trees and shrubs.', 'additional_details' => "Material: Pressed coir\nDiameter: 10-inch\nUse: Moisture retention\nColor: Cocoa\nCare: Replace every 8-12 months", 'price' => 82.00, 'stock' => 118, 'sold_count' => 49, 'image_url' => 'https://images.unsplash.com/photo-1461354464878-ad92f492a5a0?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-05-24', 'is_featured' => 0, 'is_trending' => 0, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Mulch Block 5kg', 'description' => 'Compressed coir mulch block for beds and pots.', 'additional_details' => "Material: Coir pith\nWeight: 5kg\nExpansion: High\nUse: Moisture and weed control\nCare: Hydrate before spreading", 'price' => 180.00, 'stock' => 92, 'sold_count' => 36, 'image_url' => 'https://images.unsplash.com/photo-1461354464878-ad92f492a5a0?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-06-01', 'is_featured' => 0, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Floor Runner Mat', 'description' => 'Long coir runner mat for hallway use.', 'additional_details' => "Material: Coir and rubber backing\nSize: 22x48\nUse: Hallway and mudroom\nGrip: Anti-slip\nCare: Vacuum weekly", 'price' => 430.00, 'stock' => 36, 'sold_count' => 19, 'image_url' => 'https://images.unsplash.com/photo-1571597438372-540dd3521f62?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-06-08', 'is_featured' => 1, 'is_trending' => 0, 'is_best_seller' => 1, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Scrub Brush', 'description' => 'Household scrub brush with natural coir bristles.', 'additional_details' => "Material: Coir bristles and wood\nLength: 9-inch\nUse: Sink and tile cleaning\nGrip: Ergonomic\nCare: Air dry after use", 'price' => 145.00, 'stock' => 96, 'sold_count' => 41, 'image_url' => 'https://images.unsplash.com/photo-1527515545081-5db817172677?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-06-15', 'is_featured' => 0, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Deck Brush Head', 'description' => 'Rigid coir brush head for deck cleaning.', 'additional_details' => "Material: Coir and composite mount\nWidth: 12-inch\nUse: Deck and patio\nBristle: Stiff\nCare: Rinse and dry", 'price' => 170.00, 'stock' => 67, 'sold_count' => 29, 'image_url' => 'https://images.unsplash.com/photo-1563453392212-326f5e854473?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-06-22', 'is_featured' => 0, 'is_trending' => 0, 'is_best_seller' => 1, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Garden Net', 'description' => 'Woven coir net for vertical garden installations.', 'additional_details' => "Material: Coir twine\nSize: 1m x 2m\nUse: Vertical green walls\nWeave: Medium\nCare: Secure to frame", 'price' => 265.00, 'stock' => 43, 'sold_count' => 16, 'image_url' => 'https://images.unsplash.com/photo-1472145246862-b24cf25c4a36?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-07-01', 'is_featured' => 0, 'is_trending' => 0, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Orchid Chips', 'description' => 'Chunky coir husk chips for orchids and aroids.', 'additional_details' => "Material: Coir husk chips\nVolume: 3L\nUse: Orchid medium\nTexture: Chunky\nCare: Pre-soak before use", 'price' => 168.00, 'stock' => 79, 'sold_count' => 33, 'image_url' => 'https://images.unsplash.com/photo-1463320726281-696a485928c7?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-07-08', 'is_featured' => 0, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Coir Compostable Plate Set', 'description' => 'Disposable plate set reinforced with coir pulp.', 'additional_details' => "Material: Coir pulp composite\nSet: 20 pieces\nUse: Events and parties\nColor: Beige\nCare: Compost after use", 'price' => 205.00, 'stock' => 65, 'sold_count' => 22, 'image_url' => 'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=900&q=80', 'release_date' => '2025-07-24', 'is_featured' => 0, 'is_trending' => 1, 'is_best_seller' => 0, 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        if (! $this->db->fieldExists('sold_count', 'products')) {
            foreach ($productData as &$row) {
                unset($row['sold_count']);
            }
            unset($row);
        }

        $this->db->table('products')->insertBatch($productData);

        $usersByEmail = [];
        $users = $this->db->table('users')->select('id, email')->whereIn('email', ['buyer@coircraft.local', 'mara@coircraft.local', 'leo@coircraft.local'])->get()->getResultArray();
        foreach ($users as $row) {
            $usersByEmail[$row['email']] = (int) $row['id'];
        }

        $productsByName = [];
        $products = $this->db->table('products')->select('id, name')->whereIn('name', array_column($productData, 'name'))->get()->getResultArray();
        foreach ($products as $row) {
            $productsByName[$row['name']] = (int) $row['id'];
        }

        if ($this->db->tableExists('product_reviews')) {
            $reviewData = [
                ['email' => 'buyer@coircraft.local', 'name' => 'Classic Coir Doormat', 'rating' => 5, 'comment' => 'buyer_quezon: Super kapit and catches dust very well.'],
                ['email' => 'mara@coircraft.local', 'name' => 'Coco Coir Pot 4-inch', 'rating' => 5, 'comment' => 'mara_garden: Seedlings transferred smoothly without root shock.'],
                ['email' => 'leo@coircraft.local', 'name' => 'Coir Grow Pole 3ft', 'rating' => 4, 'comment' => 'leo_planter: Good height and easy to secure to my monstera.'],
                ['email' => 'buyer@coircraft.local', 'name' => 'Coir Geotextile Roll 400gsm', 'rating' => 4, 'comment' => 'buyer_quezon: Used this on a sloped patch, held soil in place after rain.'],
                ['email' => 'mara@coircraft.local', 'name' => 'Coir Dust Brick 650g', 'rating' => 5, 'comment' => 'mara_garden: Expands fast and texture is perfect for my potting mix.'],
                ['email' => 'leo@coircraft.local', 'name' => 'Coir Hanging Basket Liner 12-inch', 'rating' => 5, 'comment' => 'leo_planter: Fit my basket exactly and stays moist longer.'],
                ['email' => 'buyer@coircraft.local', 'name' => 'Coir Potting Mix 5L', 'rating' => 4, 'comment' => 'buyer_quezon: Nice drainage and no weird smell.'],
                ['email' => 'mara@coircraft.local', 'name' => 'Coir Floor Runner Mat', 'rating' => 4, 'comment' => 'mara_garden: Looks premium and easy to clean.'],
                ['email' => 'leo@coircraft.local', 'name' => 'Coir Scrub Brush', 'rating' => 5, 'comment' => 'leo_planter: Bristles are sturdy and natural, great for outdoor cleaning.'],
                ['email' => 'buyer@coircraft.local', 'name' => 'Coir Orchid Chips', 'rating' => 5, 'comment' => 'buyer_quezon: My orchids rooted better after repotting with this.'],
            ];

            $reviewRows = [];
            foreach ($reviewData as $item) {
                $uid = $usersByEmail[$item['email']] ?? null;
                $pid = $productsByName[$item['name']] ?? null;
                if ($uid && $pid) {
                    $reviewRows[] = [
                        'user_id' => $uid,
                        'product_id' => $pid,
                        'rating' => $item['rating'],
                        'comment' => $item['comment'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
            }

            if ($reviewRows !== []) {
                $this->db->table('product_reviews')->insertBatch($reviewRows);
            }
        }

        $this->db->table('storefront_settings')->insert([
            'title' => 'CoirCraft Coconut Coir Products',
            'description' => 'Sustainable products for homes, farms, and construction.',
            'hero_title' => 'CoirCraft Coconut Coir Products',
            'hero_subtitle' => 'Sustainable products for homes, farms, and construction',
            'announcement' => 'Now shipping nationwide with pickup and delivery options.',
            'hero_background_image' => 'https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?auto=format&fit=crop&w=1400&q=80',
        ]);

        $this->db->table('vouchers')->insertBatch([
            [
                'code' => 'FREESHIP',
                'type' => 'free_shipping',
                'value' => 0,
                'description' => 'Free shipping voucher',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'LESS5',
                'type' => 'percent_discount',
                'value' => 5,
                'description' => '5% off voucher',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'LESS10',
                'type' => 'percent_discount',
                'value' => 10,
                'description' => '10% off voucher',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
