<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStorefrontSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'hero_title' => ['type' => 'VARCHAR', 'constraint' => 190],
            'hero_subtitle' => ['type' => 'VARCHAR', 'constraint' => 255],
            'announcement' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('storefront_settings');
    }

    public function down()
    {
        $this->forge->dropTable('storefront_settings');
    }
}
