<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BattleUnit extends Model
{
    protected $table = 'battle_units';

    protected $fillable = array(
    	'name',
    	'description',
    	'type',
        'ranking_value'
    );

    public function guerrillas() {
        return $this->belongsToMany('App\Models\Guerrilla')
            ->withPivot('total');
    }
}
