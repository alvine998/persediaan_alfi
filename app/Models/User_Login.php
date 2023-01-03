<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Login extends Model
{
    use HasFactory;

    protected $table = 'user_login';
    protected $fillable = ['username', 'password', 'name'];
    public $timestamps = true;

}
