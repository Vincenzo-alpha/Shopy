<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negotiation extends Model
{
    protected $table = 'sk_negotiation';
    protected $primaryKey = 'neg_id_pk';

    protected $fillable = [
        'deal_id_fk',
        'seller_negotiation_amt',
        'customer_negotiation_amt',
        'current_offer_by',
        'negotiation_status',
    ];

    protected $casts = [
        'seller_negotiation_amt' => 'decimal:2',
        'customer_negotiation_amt' => 'decimal:2',
    ];

    public function deal()
    {
        return $this->belongsTo(DealArchive::class, 'deal_id_fk', 'deal_id_pk');
    }

    public function history()
    {
        return $this->hasMany(NegotiationHistory::class, 'neg_id_fk', 'neg_id_pk')->orderBy('created_at', 'desc');
    }
}
