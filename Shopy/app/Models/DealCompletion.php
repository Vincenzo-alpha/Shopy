<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealCompletion extends Model
{
    protected $table = 'sk_deal_completion';
    protected $primaryKey = 'completion_id_pk';

    protected $fillable = [
        'deal_id_fk',
        'completion_key_hash',
        'completion_status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function dealArchive()
    {
        return $this->belongsTo(DealArchive::class, 'deal_id_fk', 'deal_id_pk');
    }

    public function dealMaster()
    {
        return $this->belongsTo(DealMaster::class, 'deal_id_fk', 'deal_id_pk');
    }
}
