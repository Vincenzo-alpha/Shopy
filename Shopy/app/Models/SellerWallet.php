<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerWallet extends Model
{
    protected $table = 'sk_seller_wallet';
    protected $primaryKey = 'wallet_id_pk';

    protected $fillable = [
        'seller_id_fk',
        'available_balance',
        'pending_balance',
    ];

    protected $casts = [
        'available_balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id_fk', 'seller_id_pk');
    }
}
