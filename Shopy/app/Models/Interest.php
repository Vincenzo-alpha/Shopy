<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    protected $table = 'sk_interest_master';
    protected $primaryKey = 'interest_id_pk';

    protected $fillable = [
        'interest_unique_no',
        'seller_id_fk',
        'customer_id_fk',
        'prod_service_id_fk',
        'interest_status',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id_fk', 'seller_id_pk');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id_fk', 'customer_id_pk');
    }

    public function product()
    {
        return $this->belongsTo(ProductService::class, 'prod_service_id_fk', 'prod_service_id_pk');
    }

    public function deal()
    {
        return $this->hasOne(DealArchive::class, 'interest_id_fk', 'interest_id_pk');
    }
}
