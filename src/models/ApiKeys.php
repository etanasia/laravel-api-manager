<?php

/**
 * @Author: ahmadnorin
 * @Date:   2017-11-28 00:17:29
 * @Last Modified by:   ahmadnorin
 * @Last Modified time: 2017-11-28 09:44:35
 */

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ApiKeys extends Model
{
    use Notifiable;
    use SoftDeletes;
    protected $table = "api_keys";
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'client', 'api_key', 'created_at', 'updated_at'
    ];

    public $hidden = ['created_at', 'updated_at'];

    public function getUserName()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
