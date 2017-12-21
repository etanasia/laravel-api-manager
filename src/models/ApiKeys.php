<?php

/**
 * @Author: bantenprov
 * @Date:   2017-11-28 00:17:29
 * @Last Modified by:   bantenprov
 * @Last Modified time: 2017-11-28 09:44:35
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiKeys extends Model
{
    protected $table = "api_keys";

    public $hidden = ['created_at', 'updated_at'];



}
