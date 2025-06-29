<?php
namespace App\Models;

use App\Models\Tempat;
use Illuminate\Database\Eloquent\Model;

class SerapanSampah extends Model
{
    protected $guarded = ['id'];
    public function tempat()
    {
        return $this->belongsTo(Tempat::class);
    }
}
