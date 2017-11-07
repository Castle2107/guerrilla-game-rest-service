<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guerrilla extends Model
{
    protected $table = 'guerrillas';

    protected $fillable = array(
    	'name',
        'player_username',
    	'attack_rate',
    	'defense_rate',
    	'points',
        'player_id',
    	'guerrilla_type_id'
    );

    public function player() {
        return $this->belongsTo('App\Models\Player', 'player_id', 'id');
    }

    public function guerrillaType() {
        return $this->belongsTo('App\Models\GuerrillaType', 'guerrilla_type_id', 'id')
    }

    public function battleUnits() {
        return $this->belongsToMany('App\Models\BattleUnit')
            ->withPivot('total');
    }

    public function battleResources() {
        return $this->belongsToMany('App\Models\BattleResource')
            ->withPivot('value');
    }
}
