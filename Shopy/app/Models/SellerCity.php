<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerCity extends Model
{
    protected $table = 'sk_seller_cities';
    protected $primaryKey = 'city_id_pk';

    protected $fillable = [
        'seller_id_fk',
        'city_name',
        'state',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id_fk', 'seller_id_pk');
    }
}
