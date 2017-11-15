<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\GlobalRules as Rules;

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

    public function updateResources($resources)
    {
        $this->reduceOil($resources['oil']);
        $this->reduceMoney($resources['money']);

        $this->save();
    }

    public function reduceOil($reduction)
    {
        $this->oil = ($reduction > $this->oil) ? 0 : $this->oil - $reduction;
    }

    public function reduceMoney($reduction)
    {
        $this->money = ($reduction > $this->money) ? 0 : $this->money - $reduction;
    }

    public function updateBattleUnits($defenseUnits, $offenseUnits)
    {
        $this->reduceAssault($defenseUnits['assault']);
        $this->reduceEngineer($defenseUnits['engineers']);
        $this->reduceTank($defenseUnits['tanks']);
        $this->reduceBunker($offenseUnits['bunkers']);

        $this->save();
    }

    public function reduceAssault($reduction)
    {
        $this->assault = ($reduction > $this->assault) ? 0 : $this->assault - $reduction;
    }

    public function reduceEngineer($reduction)
    {
        $this->engineer = ($reduction > $this->engineer) ? 0 : $this->engineer - $reduction;
    }

    public function reduceTank($reduction)
    {
        $this->tank = ($reduction > $this->tank) ? 0 : $this->tank - $reduction;
    }

    public function reduceBunker($reduction)
    {
        $this->bunker = ($reduction > $this->bunker) ? 0 : $this->bunker - $reduction;
    }

    public function updatePoints()
    {
        $this->ranking_score = (
            $this->assault * Rules::ASSAULT_UNIT['points'] +
            $this->engineer * Rules::ENGINEER_UNIT['points'] +
            $this->tank * Rules::TANK_UNIT['points'] +
            $this->bunker * Rules::BUNKER_UNIT['points']
        );

        $this->save();
    }

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
    public function guerrillaJsonFormat() {
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
