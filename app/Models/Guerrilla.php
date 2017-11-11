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

    public static function listGuerrillas() {
        $guerrillas = (new static)->orderByRankingScore();
        foreach ($guerrillas as $key => $guerrilla) {
            $guerrilla->rank = $key + 1;
            $guerrilla = $guerrilla->only(['id', 'username', 'rank']);
        }
        return $guerrillas;
    }

    public function getRankingPosition() {
        $guerrillas = (new static)->orderByRankingScore();
        foreach ($guerrillas as $key => $guerrilla) {
            if ($this->id === $guerrilla->id) {
                return $key + 1;
            }
        }
    }

    private function orderByRankingScore() {
        return self::orderBy('ranking_score', 'desc')
            ->select(['id', 'username']) // in order to make the query lighter
            ->get();
    }

    public function resources() {
        return array(
            'oil' => $this->oil,
            'money' => $this->money,
            'people' => $this->people
        );
    }

    public function offense() {
        return array(
            'bunkers' => $this->bunker
        );
    }

    public function defense() {
        return array(
            'assault' => $this->assault,
            'engineers' => $this->engineer,
            'tanks' => $this->tank
        );
    }

    // This method could be either here or in the controller
    public function guerrillaJSONFormat() {
        return [
            'id' => $this->id,
            'faction' => $this->guerrilla_type,
            'username' => $this->username,
            'ranking' => $this->getRankingPosition(),
            'points' => $this->ranking_score,
            'email' => $this->email,
            'resources' => $this->resources(),
            'defense' => $this->defense(),
            'offense' => $this->offense()
        ];
    }

}
