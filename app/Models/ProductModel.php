<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'description',
        'additional_details',
        'price',
        'stock',
        'sold_count',
        'image_url',
        'release_date',
        'is_featured',
        'is_trending',
        'is_best_seller',
        'is_active',
    ];
}
