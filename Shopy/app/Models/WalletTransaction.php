<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $table = 'sk_wallet_transactions';
    protected $primaryKey = 'transaction_id_pk';

    protected $fillable = [
        'seller_id_fk',
        'deal_id_fk',
        'transaction_type',
        'amount',
        'transaction_status',
        'reference_no',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id_fk', 'seller_id_pk');
    }

    public function deal()
    {
        return $this->belongsTo(DealMaster::class, 'deal_id_fk', 'deal_id_pk');
    }
}
