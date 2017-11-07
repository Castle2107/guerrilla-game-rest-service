<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuerrillaType extends Model
{
	protected $table = 'guerrilla_types';

   	protected $fillable = array(
   		'name',
   		'description'
   	);

   	public function guerrillas() {
        return $this->hasMany('App\Models\Guerrilla', 'guerrilla_type_id', 'id')
    }
   	
}
