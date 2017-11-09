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
   		'email'
   	);

   	protected $hidden = array(
   		'password',
   		'remember_token'
   	);

    public function guerrillas() {
      return $this->hasMany('App\Models\Guerrilla', 'player_id', 'id');
    }

    public function attackerReports() {
      return $this->hasMany('App\Models\AssaultReport', 'attacker_id', 'id');
    }

    public function targetReports() {
      return $this->hasMany('App\Models\AssaultReport', 'target_id', 'id');
    }
}
