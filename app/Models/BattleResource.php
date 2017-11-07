<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BattleResource extends Model
{
    protected $table = 'battle_resources';

    protected $fillable = array(
    	'name',
    	'description'
    );

    public function guerrillas() {
        return $this->belongsToMany('App\Models\Guerrilla')
            ->withPivot('value');
    }
}
