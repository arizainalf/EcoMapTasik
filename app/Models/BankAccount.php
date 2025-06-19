<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankAccount extends Model
{
    use HasFactory;

    protected $guarded = [
       'id'
    ];
    public function orders()
    {
        return $this->hasMany(Order::class, 'bank_acount_id');
    }
}
