<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductHistory extends Model
{
    use HasFactory;

    protected $table = 'product_history';
    protected $fillable = ['sku_id', 'history_id', 'type', 'status', 'reset', 'message'];
    public $timestamps = false;
    protected $primaryKey = 'product_history_id';
}
