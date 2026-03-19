<?php

namespace App\Models;

use CodeIgniter\Model;

class StorefrontSettingModel extends Model
{
    protected $table = 'storefront_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'title',
        'description',
        'hero_title',
        'hero_subtitle',
        'announcement',
        'hero_background_image',
    ];
}
