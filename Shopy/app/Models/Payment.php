<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'sk_payments';
    protected $primaryKey = 'payment_id_pk';

    protected $fillable = [
        'deal_id_fk',
        'customer_id_fk',
        'amount',
        'payment_method',
        'payment_status',
        'payment_reference',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id_fk', 'customer_id_pk');
    }

    public function dealArchive()
    {
        return $this->belongsTo(DealArchive::class, 'deal_id_fk', 'deal_id_pk');
    }

    public function dealMaster()
    {
        return $this->belongsTo(DealMaster::class, 'deal_id_fk', 'deal_id_pk');
    }
}
