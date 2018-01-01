<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hostkeys extends Model
{
    use Notifiable;

    protected $table = 'host_keys';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('hostname', 'keys', 'state', 'transition', 'user_id');

    public function getState()
    {
        return $this->belongsTo('Bantenprov\Workflow\Models\WorkflowState', 'state', 'id');
    }

    public function getTransition()
    {
        return $this->belongsTo('Bantenprov\Workflow\Models\WorkflowTransition', 'transition', 'id');
    }

    public function getUserName()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

}
