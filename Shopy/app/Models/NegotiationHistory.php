<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegotiationHistory extends Model
{
    protected $table = 'sk_negotiation_history';
    protected $primaryKey = 'history_id_pk';

    protected $fillable = [
        'neg_id_fk',
        'offered_by',
        'amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function negotiation()
    {
        return $this->belongsTo(Negotiation::class, 'neg_id_fk', 'neg_id_pk');
    }
}
