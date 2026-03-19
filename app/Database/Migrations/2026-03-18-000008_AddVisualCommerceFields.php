<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisualCommerceFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'additional_details' => ['type' => 'TEXT', 'null' => true, 'after' => 'description'],
            'release_date' => ['type' => 'DATE', 'null' => true, 'after' => 'image_url'],
        ]);

        $this->forge->addColumn('storefront_settings', [
            'title' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true, 'after' => 'id'],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'title'],
            'hero_background_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'announcement'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', ['additional_details', 'release_date']);
        $this->forge->dropColumn('storefront_settings', ['title', 'description', 'hero_background_image']);
    }
}
