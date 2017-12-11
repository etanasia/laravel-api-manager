<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Apiauth extends Authenticatable
{
    use Notifiable;
    protected $table = "api_manager";
}
