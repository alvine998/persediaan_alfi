<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expired extends Model
{
    use HasFactory;

    protected $table = 'expired';
    protected $fillable = ['tanggal_rekam', 'is_seen'];
    public $timestamps = false;
    protected $primaryKey = 'expired_id';
}
