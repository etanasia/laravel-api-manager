<?php

/**
 * @Author: ahmadnorin
 * @Date:   2017-11-28 00:17:29
 * @Last Modified by:   ahmadnorin
 * @Last Modified time: 2017-11-28 00:17:43
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class ApiManager extends Model
{
    protected $table = "api_manager";

    public $hidden = ['created_at', 'updated_at'];

}
