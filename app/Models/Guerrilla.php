<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guerrilla extends Model
{
    protected $table = 'guerrillas';

    protected $fillable = array(
        'username',
    	'email',
        'attack_rate',
    	'defense_rate',
    	'ranking_score',
    	'guerrilla_type',
        'oil',
        'money',
        'people',
        'tank',
        'assault',
        'engineer',
        'bunker',
    );

    public function attackerReport() {
        return $this->hasMany('App\Models\AssaultReport', 'attacker_id', 'id');
    }

    public function targetReport() {
        return $this->hasMany('App\Models\AssaultReport', 'target_id', 'id');
    }

}
