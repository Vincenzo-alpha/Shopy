<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealArchive extends Model
{
    protected $table = 'sk_deal_archive';
    protected $primaryKey = 'deal_id_pk';

    protected $fillable = [
        'deal_unique_no',
        'interest_id_fk',
        'seller_id_fk',
        'customer_id_fk',
        'prod_service_id_fk',
        'active_status',
        'neg_id_fk',
        'agreed_amount',
        'platform_fee_percent',
        'platform_fee',
        'seller_net_amount',
        'fulfillment_method',
        'payment_status',
        'deal_status',
    ];

    protected $casts = [
        'agreed_amount' => 'decimal:2',
        'platform_fee_percent' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'seller_net_amount' => 'decimal:2',
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

    public function interest()
    {
        return $this->belongsTo(Interest::class, 'interest_id_fk', 'interest_id_pk');
    }

    public function negotiation()
    {
        return $this->hasOne(Negotiation::class, 'deal_id_fk', 'deal_id_pk');
    }

    public function completion()
    {
        return $this->hasOne(DealCompletion::class, 'deal_id_fk', 'deal_id_pk');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'deal_id_fk', 'deal_id_pk');
    }
}
