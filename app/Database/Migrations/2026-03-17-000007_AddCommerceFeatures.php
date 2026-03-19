<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCommerceFeatures extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('sold_count', 'products')) {
            $this->forge->addColumn('products', [
                'sold_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'stock'],
            ]);
        }

        if (! $this->db->fieldExists('subtotal_amount', 'orders')) {
            $this->forge->addColumn('orders', [
                'subtotal_amount' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0, 'after' => 'user_id'],
            ]);
        }

        if (! $this->db->fieldExists('shipping_fee', 'orders')) {
            $this->forge->addColumn('orders', [
                'shipping_fee' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0, 'after' => 'subtotal_amount'],
            ]);
        }

        if (! $this->db->fieldExists('voucher_code', 'orders')) {
            $this->forge->addColumn('orders', [
                'voucher_code' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true, 'after' => 'shipping_fee'],
            ]);
        }

        if (! $this->db->fieldExists('voucher_type', 'orders')) {
            $this->forge->addColumn('orders', [
                'voucher_type' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true, 'after' => 'voucher_code'],
            ]);
        }

        if (! $this->db->fieldExists('voucher_discount_amount', 'orders')) {
            $this->forge->addColumn('orders', [
                'voucher_discount_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0, 'after' => 'voucher_type'],
            ]);
        }

        if (! $this->db->fieldExists('shipping_address', 'orders')) {
            $this->forge->addColumn('orders', [
                'shipping_address' => ['type' => 'TEXT', 'null' => true, 'after' => 'total_amount'],
            ]);
        }

        if (! $this->db->tableExists('vouchers')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'code' => ['type' => 'VARCHAR', 'constraint' => 40],
                'type' => ['type' => 'VARCHAR', 'constraint' => 40],
                'value' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
                'description' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('code');
            $this->forge->createTable('vouchers');
        }

        if (! $this->db->tableExists('product_reviews')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'rating' => ['type' => 'TINYINT', 'constraint' => 1],
                'comment' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['user_id', 'product_id']);
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('product_reviews');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('product_reviews')) {
            $this->forge->dropTable('product_reviews');
        }

        if ($this->db->tableExists('vouchers')) {
            $this->forge->dropTable('vouchers');
        }

        if ($this->db->fieldExists('shipping_address', 'orders')) {
            $this->forge->dropColumn('orders', 'shipping_address');
        }

        if ($this->db->fieldExists('voucher_discount_amount', 'orders')) {
            $this->forge->dropColumn('orders', 'voucher_discount_amount');
        }

        if ($this->db->fieldExists('voucher_type', 'orders')) {
            $this->forge->dropColumn('orders', 'voucher_type');
        }

        if ($this->db->fieldExists('voucher_code', 'orders')) {
            $this->forge->dropColumn('orders', 'voucher_code');
        }

        if ($this->db->fieldExists('shipping_fee', 'orders')) {
            $this->forge->dropColumn('orders', 'shipping_fee');
        }

        if ($this->db->fieldExists('subtotal_amount', 'orders')) {
            $this->forge->dropColumn('orders', 'subtotal_amount');
        }

        if ($this->db->fieldExists('sold_count', 'products')) {
            $this->forge->dropColumn('products', 'sold_count');
        }
    }
}
