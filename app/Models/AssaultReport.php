<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssaultReport extends Model
{
    protected $table = 'assault_reports';

    protected $fillable = array(
    	'target_id',
    	'attacker_id',
    	'attacker_result_url',
    	'target_result_url'
    );

    public function attacker() {
        return $this->belongsTo('App\Models\Player', 'attacker_id', 'id');
    }

    public function target() {
        return $this->belongsTo('App\Models\Player', 'target_id', 'id');
    }

}
