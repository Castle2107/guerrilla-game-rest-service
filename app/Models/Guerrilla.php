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

    public function decreaseResources($resources)
    {
        $this->reduceOil($resources['oil']);
        $this->reduceMoney($resources['money']);

        $this->save();
    }

    public function increaseResources($resources)
    {
        $this->increaseOil($resources['oil']);
        $this->increaseMoney($resources['money']);

        $this->save();
    }

    public function reduceOil($reduction)
    {
        $this->oil -= $reduction;
    }

    public function reduceMoney($reduction)
    {
        $this->money -= $reduction;
    }

    public function reducePeople($reduction)
    {
        $this->people -= $reduction;
    }

    public function hasOil($quantity)
    {
        return ($this->oil >= $quantity);
    }

    public function hasMoney($quantity)
    {
        return ($this->money >= $quantity);
    }

    public function hasPeople($quantity)
    {
        return ($this->people >= $quantity);
    }

    public function increaseBattleUnit($battleUnit, $quantity)
    {
        $this[$battleUnit] += $quantity;
    }

    public function increaseRankingScore($points)
    {
        $this->ranking_score += $points;
    }

    public function increaseOil($increase)
    {
        $this->oil += $increase;
    }

    public function increaseMoney($increase)
    {
        $this->money += $increase;
    }

    public function updateBattleUnits($defenseUnits, $offenseUnits)
    {
        $assault  = $this->reduceAssault($defenseUnits['assault']);
        $engineer = $this->reduceEngineer($defenseUnits['engineers']);
        $tank     = $this->reduceTank($defenseUnits['tanks']);
        $bunker   = $this->reduceBunker($offenseUnits['bunkers']);

        $this->save();
        
        return array(
            'defense_lost' => array(
                'assault' => $assault,
                'engineers' => $engineer,
                'tanks' => $tank,
            ),
            'offense_lost' => array(
                'bunkers' => $bunker,
            )
        );
    }

    public function reduceAssault($reduction)
    {
        $assault = ($this->assault >= $reduction) ? $reduction : $this->assault;
        $this->assault = ($reduction > $this->assault) ? 0 : $this->assault - $reduction;

        return $assault;
    }

    public function reduceEngineer($reduction)
    {
        $engineer = ($this->engineer >= $reduction) ? $reduction : $this->engineer;
        $this->engineer = ($reduction > $this->engineer) ? 0 : $this->engineer - $reduction;

        return $engineer;
    }

    public function reduceTank($reduction)
    {
        $tank = ($this->tank >= $reduction) ? $reduction : $this->tank;
        $this->tank = ($reduction > $this->tank) ? 0 : $this->tank - $reduction;

        return $tank;
    }

    public function reduceBunker($reduction)
    {
        $bunker = ($this->bunker >= $reduction) ? $reduction : $this->bunker;
        $this->bunker = ($reduction > $this->bunker) ? 0 : $this->bunker - $reduction;

        return $bunker;
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

    public function defense() {
        return array(
            'bunkers' => $this->bunker
        );
    }

    public function offense() {
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
            'timestamp' => $this->updated_at->getTimestamp(),
            'email' => $this->email,
            'resources' => $this->resources(),
            'defense' => $this->defense(),
            'offense' => $this->offense()
        ];
    }

}
