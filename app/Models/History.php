<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $table = 'history';
    protected $fillable = ['type', 'status', 'error_count', 'created_at', 'updated_at'];
    public $timestamps = true;
    protected $primaryKey = 'history_id';
}
