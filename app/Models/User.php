<?php


namespace App\Models;


use Jenssegers\Mongodb\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'username', 'password', 'api_token'];

    protected $hidden = ['password', 'api_token'];
}
