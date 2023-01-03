<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpiredDetail extends Model
{
    use HasFactory;

    protected $table = 'expired_detail';
    protected $fillable = ['expired_id', 'stock_id', 'sku_id'];
    public $timestamps = false;
    protected $primaryKey = 'expired_detail_id';
}
