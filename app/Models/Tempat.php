<?php
namespace App\Models;

use App\Models\SerapanSampah;
use Illuminate\Database\Eloquent\Model;

class Tempat extends Model
{
    protected $guarded = ['id'];
    public function serapanSampahs()
    {
        return $this->hasMany(SerapanSampah::class);
    }
}
