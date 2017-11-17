<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guerrilla;
use App\Models\AssaultReport;
use App\Helpers\GlobalRules as Rules;
use App\Http\Resources\Guerrilla as GuerrillaResource;
use App\Http\Requests\GuerrillaRequest;
use App\Exceptions\ExceptionTrait;

class GuerrillaController extends Controller
{
    use ExceptionTrait;

    public function index()
    {
        return response()->json(
            GuerrillaResource::collection(Guerrilla::all()), 200
        );   
    }

    public function store(GuerrillaRequest $request)
    {
        $guerrilla = new Guerrilla();
        $guerrilla->email = $request->email;
        $guerrilla->username = $request->username;
        $guerrilla->guerrilla_type = $request->faction;
        $guerrilla->save();

        return response()->json([
            'username' => $guerrilla->username, 
            'id' => $guerrilla->id
        ], 200);
    }

    public function show($id)
    {
        $guerrilla = Guerrilla::where('id', '=', $id)
            ->orWhere('username', '=', $id)
            ->firstOrFail();
        return response()->json(
            $guerrilla->guerrillaJsonFormat()
        );
    }

    public function destroy($id)
    {
        $guerrilla = Guerrilla::findOrFail($id);
        if ($guerrilla->delete()) {
            return response()->json(['message' => 'Guerrilla deleted'], 200);
        } else {
            return response()->json(['errors' => 'Error at deleting model'], 500);
        }
    }

    /**
    * Buys the battle units specified in the $request
    *
    * @param Request $request   contains username, defense and ofense battle units
    * @return json
    */
    public function buyGuerrilla(Request $request)
    {
        $guerrilla = Guerrilla::where('username', $request->username)->firstOrFail();
        $battleUnits = array_merge($request->defense, $request->offense);
        $info = array();

        foreach ($battleUnits as $key => $value) {
            if ($value) {
                switch ($key) {
                    case 'bunkers':
                        $info['bunkers'] = $this->buyBattleUnit(Rules::BUNKER_UNIT, $value, $guerrilla);
                        break;
                    case 'assault':
                        $info['assault'] = $this->buyBattleUnit(Rules::ASSAULT_UNIT, $value, $guerrilla);
                        break;
                    case 'engineers':
                        $info['engineers'] = $this->buyBattleUnit(Rules::ENGINEER_UNIT, $value, $guerrilla);
                        break;
                    case 'tanks':
                        $info['tanks'] = $this->buyBattleUnit(Rules::TANK_UNIT, $value, $guerrilla);
                        break;
                    default:
                        return response()->json('The battle unit ' . $key . ' is not valid', 403);
                        break;
                }
            }
        }

        $guerrilla->save();
        
        return response()->json([
            'status' => 'ok',
            'info' => $info
        ], 200);
    }

    /**
    * If the guerrilla passed has enough resources, reduces resources and
    * add the points and battle unit
    *
    * @param const Array $battleUnit      Variable from GlobalRules. 
    * @param int $quantity                Needed quantity of battle units
    * @param Guerrilla $guerrilla         Guerrilla which needs the battle units
    * @return json
    */
    public function buyBattleUnit($battleUnit = [], $quantity, $guerrilla) 
    {
        if ($this->hasResources($battleUnit, $quantity, $guerrilla)) {

            $this->reduceResources($battleUnit, $quantity, $guerrilla);
            $guerrilla->increaseRankingScore(($battleUnit['points'] * $quantity));
            $guerrilla->increaseBattleUnit($battleUnit['name'], $quantity);

            return $this->validPurchaseResponse($battleUnit, $quantity);

        } else {

            return $this->invalidPurchaseResponse($battleUnit, $quantity);
        }
    }

    /**
    * Verifies if the guerrillas has the enough resources to buy 
    * the specified battle unit
    * 
    * @param const Array $battleUnit    Variable from GlobalRules
    * @param int $quantity              Needed quantity of battle units
    * @param Guerrilla $guerrilla       Guerrilla which needs the battle units
    * @return boolean
    */
    private function hasResources($battleUnit, $quantity, $guerrilla)
    {
        return ($guerrilla->hasMoney(($battleUnit['money'] * $quantity)))
            && ($guerrilla->hasPeople(($battleUnit['people'] * $quantity)))
            && ($guerrilla->hasOil(($battleUnit['oil'] * $quantity)));
    }

    /**
    * Reduces the resources according with the battle unit
    * 
    * @param const Array $battleUnit   Variable from GlobalRules.
    * @param int $quantity             Needed quantity of battle units
    * @param Guerrilla $guerrilla      Guerrilla which needs the battle units
    */
    private function reduceResources($battleUnit = [], $quantity, $guerrilla)
    {
        $guerrilla->reducePeople(($battleUnit['people'] * $quantity));
        $guerrilla->reduceOil(($battleUnit['oil'] * $quantity));
        $guerrilla->reduceMoney(($battleUnit['money'] * $quantity));
    }

    private function invalidPurchaseResponse($battleUnit, $quantity)
    {
        return ('Not enough resources to buy ' . $quantity . ' ' . $battleUnit['name'] . ' units');   
    }

    private function validPurchaseResponse($battleUnit, $quantity)
    {
        return ($quantity . ' ' . $battleUnit['name'] . ' units were successfully bought');
    }

    /**
     * Make the attack between two guerrillas.
     * 
     * @param  Request $request Contains the IDs of the attacker and the target.
     * @return json             Reports of the attacker and the target after the battle.
     */
    public function attackGuerrilla(Request $request)
    {
        $target   = Guerrilla::where('id', '=', $request->target_id)->firstOrFail();
        $attacker = Guerrilla::where('id', '=', $request->attacker_id)->firstOrFail();

        $defenseRateTarget   = $this->defenseRate($attacker, $target);
        $offenseRateAttacker = $this->attackRate($attacker, $target);

        $lootCAP = $this->getLootCap($attacker);
        $theftAndLostResources = $this->calculateTheftAndLostResources($lootCAP, $target);

        $lostUnitsAttacker = $this->getAttackerLostUnits($target, $defenseRateTarget);
        $lostUnitsTarget   = $this->getTargetLostUnits($attacker, $offenseRateAttacker);

        $attacker->increaseResources($theftAndLostResources);
        $attacker->updateBattleUnits($lostUnitsAttacker['defense_lost'], $lostUnitsAttacker['offense_lost']);
        $attacker->updatePoints();

        $target->decreaseResources($theftAndLostResources);
        $target->updateBattleUnits($lostUnitsTarget['defense_lost'], $lostUnitsTarget['offense_lost']);
        $target->updatePoints();

        $assaultReportAttacker = $this->generateReportAttackerBattle($attacker, $theftAndLostResources, $lostUnitsAttacker);
        $assaultReportTarget   = $this->generateReportTargetBattle($target, $theftAndLostResources, $lostUnitsTarget);

        $this->saveAssaultReport($target, $attacker, $assaultReportAttacker, $assaultReportTarget);

        return response()->json([
        	'result'   => 'success',
            'attacker' => $assaultReportAttacker,
            'target'   => $assaultReportTarget
        ]);
    }

    /**
     * It calculates the values that were lost by the target and
     * stolen by the attacker.
     * 
     * @param  int      $lootCAP Attacker's loot.
     * @param  Guerilla $target  Guerilla that lost resources.
     * @return array             Values of lost/theft resources.
     */
    public function calculateTheftAndLostResources(int $lootCAP, $target)
    {
        $money = floor(rand(0, $lootCAP));
        $oil   = $lootCAP - $money;

        $money = ($target->money > $money) ? $money : $target->money;
        $oil   = ($target->oil > $oil) ? $oil : $target->oil;

        return array(
            'money' => $money,
            'oil'   => $oil
        );
    }

    /**
     * Generates the record of the battle between two guerrilla
     * and stores it in the database.
     * 
     * @param  Guerrilla $target                Guerrilla that is attacked.
     * @param  Guerrilla $attacker              Guerrilla that attacks.
     * @param  array     $assaultReportAttacker Attacker's report.
     * @param  array     $assaultReportTarget   Target's report.
     */
    public function saveAssaultReport(Guerrilla $target, Guerrilla $attacker, $assaultReportAttacker, $assaultReportTarget)
    {
        $assaultReport = new AssaultReport();

        $assaultReport->target_id   = $target->id;
        $assaultReport->attacker_id = $attacker->id;
        $assaultReport->attacker_result_url = $this->getFileURL($assaultReportAttacker);
        $assaultReport->target_result_url   = $this->getFileURL($assaultReportTarget);

        $assaultReport->save();
    }

    /**
     * Generates the json file of a report of a guerrilla that
     * participated in a battle.
     * 
     * @param  array  $report Report of a guerrilla.
     * @return string         File path.
     */
    public function getFileURL($report)
    {
        $content  = json_encode($report);
        $fileName = 'storage/' . uniqid() . '_report.json';
        $file = fopen($fileName, "w+b") or die("Error when creating the json file");
        
        fputs($file, $content);
        fclose($file);

        return $fileName;
    }

    /**
     * Generates the guerilla's report that was attacked.
     * 
     * @param  Guerrilla $target                Guerrilla that is attacked.
     * @param  array     $theftAndLostResources Values of lost/theft resources.
     * @param  array     $lostUnitsTarget       Lost units.
     * @return array                            Target's report.
     */
    public function generateReportTargetBattle(Guerrilla $target, $theftAndLostResources, $lostUnitsTarget)
    {
        return array(
            'id'        => $target->id,
            'faction'   => $target->guerrilla_type,
            'username'  => $target->username,
            'ranking'   => $target->getRankingPosition(),
            'points'    => $target->ranking_score,
            'timestamp' => $target->updated_at->getTimestamp(),
            'email'     => $target->email,
            'resources_lost' => array(
                'oil'    => $theftAndLostResources['oil'],
                'money'  => $theftAndLostResources['money'],
                'people' => $target->people
            ),
            'offense_lost' => $lostUnitsTarget['offense_lost'],
            'defense_lost' => $lostUnitsTarget['defense_lost']
        );
    }

    /**
     * Generates the guerrilla's report that attacks.
     * 
     * @param  Guerrilla $attacker              Guerrilla that attacks.
     * @param  array     $theftAndLostResources Values of lost/theft resources.
     * @param  array     $lostUnitsAttacker     Lost units.
     * @return array                            Attacker's report.
     */
    public function generateReportAttackerBattle(Guerrilla $attacker, $theftAndLostResources, $lostUnitsAttacker)
    {
        return array(
            'id'        => $attacker->id,
            'faction'   => $attacker->guerrilla_type,
            'username'  => $attacker->username,
            'ranking'   => $attacker->getRankingPosition(),
            'points'    => $attacker->ranking_score,
            'timestamp' => $attacker->updated_at->getTimestamp(),
            'email'     => $attacker->email,
            'resources_lost' => array(
                'oil'    => $theftAndLostResources['oil'],
                'money'  => $theftAndLostResources['money'],
                'people' => $attacker->people
            ),
            'offense_lost' => $lostUnitsAttacker['offense_lost'],
            'defense_lost' => $lostUnitsAttacker['defense_lost']
        );
    }

    public function getTargetLostUnits(Guerrilla $guerrilla, $offenseAttacker)
    {
        return array(
            'defense_lost' => array(
                'assault'   => floor($this->getTargetLostAssaultUnits($guerrilla, $offenseAttacker)),
                'engineers' => floor($this->getTargetLostEngineerUnits($guerrilla, $offenseAttacker)),
                'tanks'     => floor($this->getTargetLostTankUnits($guerrilla, $offenseAttacker)),
            ),
            'offense_lost' => array(
                'bunkers' => floor($this->getTargetLostBunkerUnits($guerrilla, $offenseAttacker))
            )
        );
    }

    public function getTargetLostAssaultUnits(Guerrilla $guerrilla, $offenseAttacker)
    {
        return ($guerrilla->assault * Rules::ASSAULT_DAMAGE_INFLICTED['assault'] +
                $guerrilla->engineer * Rules::ASSAULT_DAMAGE_INFLICTED['engineer'] +
                $guerrilla->tank * Rules::ASSAULT_DAMAGE_INFLICTED['tank'] +
                $guerrilla->bunker * Rules::ASSAULT_DAMAGE_INFLICTED['bunker']) * 
                $offenseAttacker;
    }

    public function getTargetLostEngineerUnits(Guerrilla $guerrilla, $offenseAttacker)
    {
        return ($guerrilla->assault * Rules::ENGINEER_DAMAGE_INFLICTED['assault'] +
                $guerrilla->engineer * Rules::ENGINEER_DAMAGE_INFLICTED['engineer'] +
                $guerrilla->tank * Rules::ENGINEER_DAMAGE_INFLICTED['tank'] +
                $guerrilla->bunker * Rules::ENGINEER_DAMAGE_INFLICTED['bunker']) * 
                $offenseAttacker;
    }

    public function getTargetLostTankUnits(Guerrilla $guerrilla, $offenseAttacker)
    {
        return ($guerrilla->assault * Rules::TANK_DAMAGE_INFLICTED['assault'] +
                $guerrilla->engineer * Rules::TANK_DAMAGE_INFLICTED['engineer'] +
                $guerrilla->tank * Rules::TANK_DAMAGE_INFLICTED['tank'] +
                $guerrilla->bunker * Rules::TANK_DAMAGE_INFLICTED['bunker']) * 
                $offenseAttacker;
    }

    public function getTargetLostBunkerUnits(Guerrilla $guerrilla, $offenseAttacker)
    {
        return ($guerrilla->assault * Rules::BUNKER_DAMAGE_INFLICTED['assault'] +
                $guerrilla->engineer * Rules::BUNKER_DAMAGE_INFLICTED['engineer'] +
                $guerrilla->tank * Rules::BUNKER_DAMAGE_INFLICTED['tank'] +
                $guerrilla->bunker * Rules::BUNKER_DAMAGE_INFLICTED['bunker']) * 
                $offenseAttacker;
    }

    public function getAttackerLostUnits(Guerrilla $guerrilla, $deffenseTarget)
    {
        return array(
            'defense_lost' => array(
                'assault'   => floor($this->getAttackerLostAssaultUnits($guerrilla, $deffenseTarget)),
                'engineers' => floor($this->getAttackerLostEngineerUnits($guerrilla, $deffenseTarget)),
                'tanks'     => floor($this->getAttackerLostTankUnits($guerrilla, $deffenseTarget)),
            ),
            'offense_lost' => array(
                'bunkers' => floor($this->getAttackerLostBunkerUnits($guerrilla, $deffenseTarget))
            )
        );
    }

    public function getAttackerLostAssaultUnits(Guerrilla $guerrilla, $deffenseTarget)
    {
        return ($guerrilla->assault * Rules::ASSAULT_DAMAGE_INFLICTED['assault'] +
                $guerrilla->engineer * Rules::ASSAULT_DAMAGE_INFLICTED['engineer'] +
                $guerrilla->tank * Rules::ASSAULT_DAMAGE_INFLICTED['tank'] +
                $guerrilla->bunker * Rules::ASSAULT_DAMAGE_INFLICTED['bunker']) * 
                $deffenseTarget;
    }

    public function getAttackerLostEngineerUnits(Guerrilla $guerrilla, $deffenseTarget)
    {
        return ($guerrilla->assault * Rules::ENGINEER_DAMAGE_INFLICTED['assault'] +
                $guerrilla->engineer * Rules::ENGINEER_DAMAGE_INFLICTED['engineer'] +
                $guerrilla->tank * Rules::ENGINEER_DAMAGE_INFLICTED['tank'] +
                $guerrilla->bunker * Rules::ENGINEER_DAMAGE_INFLICTED['bunker']) * 
                $deffenseTarget;
    }

    public function getAttackerLostTankUnits(Guerrilla $guerrilla, $deffenseTarget)
    {
        return ($guerrilla->assault * Rules::TANK_DAMAGE_INFLICTED['assault'] +
                $guerrilla->engineer * Rules::TANK_DAMAGE_INFLICTED['engineer'] +
                $guerrilla->tank * Rules::TANK_DAMAGE_INFLICTED['tank'] +
                $guerrilla->bunker * Rules::TANK_DAMAGE_INFLICTED['bunker']) * 
                $deffenseTarget;
    }

    public function getAttackerLostBunkerUnits(Guerrilla $guerrilla, $deffenseTarget)
    {
        return ($guerrilla->assault * Rules::BUNKER_DAMAGE_INFLICTED['assault'] +
                $guerrilla->engineer * Rules::BUNKER_DAMAGE_INFLICTED['engineer'] +
                $guerrilla->tank * Rules::BUNKER_DAMAGE_INFLICTED['tank'] +
                $guerrilla->bunker * Rules::BUNKER_DAMAGE_INFLICTED['bunker']) * 
                $deffenseTarget;
    }

    /**
     * Calculates loot capacity.
     * 
     * @param  Guerrilla $guerrilla Guerrilla to calculate the loot.
     * @return int                  Loot capacity.
     */
    public function getLootCap(Guerrilla $guerrilla)
    {
        $assault   = $guerrilla->assault;
        $engineers = $guerrilla->engineer;
        $tanks     = $guerrilla->tank;
        $bunkers   = $guerrilla->bunker;

        return $assault * Rules::ASSAULT_LOOT +
               $engineers * Rules::ENGINEER_LOOT +
               $tanks * Rules::TANK_LOOT +
               $bunkers * Rules::BUNKER_LOOT;
    }

    /**
     * Calculates the defense rate.
     * 
     * @param  Guerrilla $attacker Guerrilla that attacks.
     * @param  Guerrilla $target   Guerrilla that is attacked.
     * @return float               Defense rate.
     */
    public function defenseRate(Guerrilla $attacker, Guerrilla $target)
    {
        $deffenseTarget  = $this->getDefense($target);
        $offenseAttacker = $this->getOffense($attacker);

        return ($deffenseTarget / ($offenseAttacker + $deffenseTarget)) + 0.1;
    }

    /**
     * Calculates the attack rate.
     * 
     * @param  Guerrilla $attacker Guerrilla that attacks.
     * @param  Guerrilla $target   Guerrilla that is attacked.
     * @return float               Attack rate.
     */
    public function attackRate(Guerrilla $attacker, Guerrilla $target)
    {
        $deffenseTarget  = $this->getDefense($target);
        $offenseAttacker = $this->getOffense($attacker);

        return ($offenseAttacker / ($deffenseTarget + $offenseAttacker)) + 0.1;
    }

    /**
     * Calculates the offense of a guerrilla.
     * 
     * @param  Guerrilla $guerrilla Guerrilla to calculate his offense.
     * @return int                  Guerilla's offense.
     */
    public function getOffense(Guerrilla $guerrilla)
    {
        $assault   = $guerrilla->assault;
        $engineers = $guerrilla->engineer;
        $tanks     = $guerrilla->tank;

        return $assault*80 + $engineers*30 + $tanks*500;
    }

    /**
     * Calculates the defense of a guerrilla.
     * 
     * @param  Guerrilla $guerrilla Guerrilla to calculate his defense.
     * @return int                  Guerilla's defense.
     */
    public function getDefense(Guerrilla $guerrilla)
    {
        $assault   = $guerrilla->assault;
        $engineers = $guerrilla->engineer;
        $tanks     = $guerrilla->tank;
        $bunkers   = $guerrilla->bunker;

        return $assault*20 + $engineers*70 + $tanks*20 + $bunkers*600;
    }

}