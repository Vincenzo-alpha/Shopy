<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Seller extends Authenticatable
{
    use Notifiable;

    protected $table = 'sk_seller_master';
    protected $primaryKey = 'seller_id_pk';

    protected $fillable = [
        'seller_unique_no',
        'seller_name',
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

    public function cities()
    {
        return $this->hasMany(SellerCity::class, 'seller_id_fk', 'seller_id_pk');
    }

    public function products()
    {
        return $this->hasMany(ProductService::class, 'seller_id_fk', 'seller_id_pk');
    }

    public function wallet()
    {
        return $this->hasOne(SellerWallet::class, 'seller_id_fk', 'seller_id_pk');
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'seller_id_fk', 'seller_id_pk');
    }

    public function interests()
    {
        return $this->hasMany(Interest::class, 'seller_id_fk', 'seller_id_pk');
    }

    public function activeDeals()
    {
        return $this->hasMany(DealArchive::class, 'seller_id_fk', 'seller_id_pk');
    }

    public function completedDeals()
    {
        return $this->hasMany(DealMaster::class, 'seller_id_fk', 'seller_id_pk');
    }
}
