<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ContactPerson extends Model
{
    protected $fillable = ['username', 'name', 'email'];
}
