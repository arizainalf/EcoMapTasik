<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    protected static function booted()
    {
        static::creating(function ($order) {
            // Ambil prefix dari settings, atau fallback ke 'INV-'
            $prefix = getSetting()->invoice_prefix . '-' ?? 'INV-';

            // Gunakan timestamp saat ini (format: YYYYMMDDHHMMSS)
            $timestamp = now()->format('YmdHis');

            $order->invoice_number = $prefix . $timestamp;
        });
    }
}
