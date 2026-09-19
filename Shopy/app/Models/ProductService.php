<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductService extends Model
{
    protected $table = 'sk_product_sevice_master';
    protected $primaryKey = 'prod_service_id_pk';

    protected $fillable = [
        'prod_servics_unique_no',
        'seller_id_fk',
        'prod_service_name',
        'description',
        'category',
        'item_type',
        'listed_price',
        'minimum_rate',
        'maximum_rate',
        'image_path',
        'availability_status',
    ];

    protected $casts = [
        'listed_price' => 'decimal:2',
        'minimum_rate' => 'decimal:2',
        'maximum_rate' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id_fk', 'seller_id_pk');
    }

    public function interests()
    {
        return $this->hasMany(Interest::class, 'prod_service_id_fk', 'prod_service_id_pk');
    }

    public function deals()
    {
        return $this->hasMany(DealArchive::class, 'prod_service_id_fk', 'prod_service_id_pk');
    }
}
