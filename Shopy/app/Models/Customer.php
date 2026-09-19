<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $table = 'sk_customer_master';
    protected $primaryKey = 'customer_id_pk';

    protected $fillable = [
        'customer_unique_no',
        'customer_name',
        'email',
        'password',
        'contact_no',
        'address',
        'city',
        'state',
        'account_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function interests()
    {
        return $this->hasMany(Interest::class, 'customer_id_fk', 'customer_id_pk');
    }

    public function activeDeals()
    {
        return $this->hasMany(DealArchive::class, 'customer_id_fk', 'customer_id_pk');
    }

    public function completedDeals()
    {
        return $this->hasMany(DealMaster::class, 'customer_id_fk', 'customer_id_pk');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'customer_id_fk', 'customer_id_pk');
    }
}
