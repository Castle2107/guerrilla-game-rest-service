<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Player extends Authenticatable
{
    use Notifiable;

    protected $table = 'players';

   	protected $guard = 'player';

   	protected $fillable = array(
   		'username',
   		'email',
   		'password'
   	);

   	protected $hidden = array(
   		'password',
   		'remember_token'
   	);

    public function guerrillas() {
      return $this->hasMany('App\Models\Guerrilla', 'player_id', 'id');
    }
}
