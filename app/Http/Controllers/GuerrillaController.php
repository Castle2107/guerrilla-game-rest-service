<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guerrilla;
use App\Models\AssaultReport;
use App\Helpers\GlobalRules as Rules;
use App\Http\Resources\Guerrilla as GuerrillaResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function store(Request $request)
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

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $guerrilla = Guerrilla::findOrFail($id);
        if ($guerrilla->delete()) {
            return response()->json('Guerrilla deleted', 200);
        } else {
            return response()->json('Error at deleting model', 500);
        }
    }



    /**
    * TODO: the response should contain a message with the transaction result
    * For example: if the player bought 1 battle unit, we should notify it was
    * successfully added. If the player didn't have enough resources, it
    * should return a message indicating it
    */
    public function buyGuerrilla(Request $request)
    {
        $guerrilla = Guerrilla::where('username', $request->username)->firstOrFail();
        $battleUnits = array_merge($request->defense, $request->offense);
        foreach ($battleUnits as $key => $value) {
            if ($value) {
                switch ($key) {
                    case 'bunkers':
                        $this->buyBattle(Rules::BUNKER_UNIT, $value, $guerrilla);
                        break;
                    case 'assault':
                        $this->buyBattle(Rules::ASSAULT_UNIT, $value, $guerrilla);
                        break;
                    case 'engineers':
                        $this->buyBattle(Rules::ENGINEER_UNIT, $value, $guerrilla);
                        break;
                    case 'tanks':
                        $this->buyBattle(Rules::TANK_UNIT, $value, $guerrilla);
                        break;
                    default:
                        return response()->json('No valid battle unit indicated', 403);
                        break;
                }
            }
        }
        return response()->json(['status' => 'ok'], 200);
    }


    /**
    * @param const $btuVal      Variable from GlobalRules. 
    * @param $quantity          Needed quantity of battle units
    * @param $guerrilla         Guerrilla which needs the battle units
    */
    public function buyBattle($btuVal = [], $quantity, $guerrilla) 
    {
        // return $this->customResponse([$this->hasResources($btuVal, $quantity, $guerrilla)]);
        if ($this->hasResources($btuVal, $quantity, $guerrilla)) {
            $this->reduceResources($btuVal, $quantity, $guerrilla);
            $this->addPoints($btuVal, $quantity, $guerrilla);
            $this->addBattleUnit($btuVal, $quantity, $guerrilla);
            $guerrilla->save();            
        }
    }

    /**
    * Verifies if the guerrillas has the enough resources to buy 
    * the specified battle unit
    * 
    * @param const $btuVal      Variable from GlobalRules (battle unit)
    * @param $quantity          Needed quantity of battle units
    * @param $guerrilla         Guerrilla which needs the battle units
    */
    private function hasResources($btuVal, $quantity, $guerrilla)
    {
        return ($guerrilla->money >= ($btuVal['money']  * $quantity))
            && ($guerrilla->people >= ($btuVal['people']  * $quantity))
            && ($guerrilla->oil >= ($btuVal['oil'] * $quantity));
    }

    /**
    * Reduces the resources according with the battle unit
    * 
    * @param const $btuVal      Variable from GlobalRules. (battle unit)
    * @param $quantity          Needed quantity of battle units
    * @param $guerrilla         Guerrilla which needs the battle units
    */
    private function reduceResources($btuVal = [], $quantity, $guerrilla)
    {
        $guerrilla->money -= ($btuVal['money'] * $quantity);
        $guerrilla->people -= ($btuVal['people'] * $quantity);
        $guerrilla->oil -= ($btuVal['oil'] * $quantity);
    }

    /**
    * Add points according to the battle unit
    *
    * @param const $btuVal      Variable from GlobalRules. (battle unit) 
    * @param $quantity          Needed quantity of battle units
    * @param $guerrilla         Guerrilla which needs the battle units
    */
    private function addPoints($btuVal, $quantity, $guerrilla)
    {
        $guerrilla->ranking_score += ($btuVal['points'] * $quantity);
    }

    private function addBattleUnit($btuVal, $quantity, $guerrilla)
    {
        $guerrilla[$btuVal['name']] += $quantity;
    }

    // TODO: remove after testing functions
    private function customResponse($data)
    {
        return response()->json($data, 200);
    }

    /**
     * Make the attack between two guerrillas.
     * 
     * @param  Request $request contains the IDs of the attacker and the target.
     * @return json             Reports of the attacker and the target after the battle.
     */
    public function attackGuerrilla(Request $request)
    {
        $target   = Guerrilla::where('id', '=', $request->target_id)->firstOrFail();
        $attacker = Guerrilla::where('id', '=', $request->attacker_id)->firstOrFail();

        $defenseRateTarget   = $this->defenseRate($attacker, $target);
        $offenseRateAttacker = $this->attackRate($attacker, $target);

        $lootCAP = $this->getLootCap($attacker);

        $lostUnitsAttacker = $this->getAttackerLostUnits($target, $defenseRateTarget);
        $lostUnitsTarget   = $this->getTargetLostUnits($attacker, $offenseRateAttacker);

        $assaultReportAttacker = $this->generateReportAttackerBattle($attacker, $lootCAP, $lostUnitsAttacker);
        $assaultReportTarget   = $this->generateReportTargetBattle($target, $lootCAP, $lostUnitsTarget);

        $this->saveAssaultReport($target, $attacker, $assaultReportAttacker, $assaultReportTarget);

        //$attacker->updateResources($assaultReportAttacker['resources_lost']);
        $attacker->updateBattleUnits($assaultReportAttacker['defense_lost'], $assaultReportAttacker['offense_lost']);
        $attacker->updatePoints();

        $target->updateResources($assaultReportTarget['resources_lost']);
        $target->updateBattleUnits($assaultReportTarget['defense_lost'], $assaultReportTarget['offense_lost']);
        $target->updatePoints();

        return response()->json([
        	'result'   => 'success',
            'attacker' => $assaultReportAttacker,
            'target'   => $assaultReportTarget
        ]);
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
     * Generate the json file of a report of a guerrilla that
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
     * Generates the report the guerrilla that was attacked.
     * 
     * @param  Guerrilla $target          Guerrilla that is attacked.
     * @param  int       $lootCAP         Loot capacity.
     * @param  array     $lostUnitsTarget Lost units.
     * @return array                      Target's report.
     */
    public function generateReportTargetBattle(Guerrilla $target, int $lootCAP, $lostUnitsTarget)
    {
        $earnedMoney = floor(rand(0, $lootCAP));
        $earnedOil   = $lootCAP - $earnedMoney;

        return array(
            'id'        => $target->id,
            'faction'   => $target->guerrilla_type,
            'username'  => $target->username,
            'ranking'   => $target->getRankingPosition(),
            'points'    => $target->ranking_score,
            'timestamp' => $target->updated_at->getTimestamp(),
            'email'     => $target->email,
            'resources_lost' => array(
                'oil'    => $earnedOil,
                'money'  => $earnedMoney,
                'people' => $target->people
            ),
            'offense_lost' => $lostUnitsTarget['offense_lost'],
            'defense_lost' => $lostUnitsTarget['defense_lost']
        );
    }

    /**
     * Generates the report the guerrilla that attacks.
     * 
     * @param  Guerrilla $attacker          Guerrilla that attacks.
     * @param  int       $lootCAP           Loot capacity.
     * @param  array     $lostUnitsAttacker Lost units.
     * @return array                        Attacker's report.
     */
    public function generateReportAttackerBattle(Guerrilla $attacker, int $lootCAP, $lostUnitsAttacker)
    {
        $earnedMoney = floor(rand(0, $lootCAP));
        $earnedOil   = $lootCAP - $earnedMoney;

        return array(
            'id'        => $attacker->id,
            'faction'   => $attacker->guerrilla_type,
            'username'  => $attacker->username,
            'ranking'   => $attacker->getRankingPosition(),
            'points'    => $attacker->ranking_score,
            'timestamp' => $attacker->updated_at->getTimestamp(),
            'email'     => $attacker->email,
            'resources_lost' => array(
                'oil'    => $earnedOil,
                'money'  => $earnedMoney,
                'people' => $attacker->people
            ),
            'offense_lost' => $lostUnitsAttacker['offense_lost'],
            'defense_lost' => $lostUnitsAttacker['defense_lost']
        );
    }

    public function getTargetLostUnits(Guerrilla $guerrilla, $deffenseTarget)
    {
        return array(
            'defense_lost' => array(
                'assault'   => floor($this->getTargetLostAssaultUnits($guerrilla, $deffenseTarget)),
                'engineers' => floor($this->getTargetLostEngineerUnits($guerrilla, $deffenseTarget)),
                'tanks'     => floor($this->getTargetLostTankUnits($guerrilla, $deffenseTarget)),
            ),
            'offense_lost' => array(
                'bunkers' => floor($this->getTargetLostBunkerUnits($guerrilla, $deffenseTarget))
            )
        );
    }

    public function getTargetLostAssaultUnits(Guerrilla $guerrilla, $deffenseTarget)
    {
        return ($guerrilla->assault * Rules::ASSAULT_DAMAGE_INFLICTED['assault'] +
                $guerrilla->engineer * Rules::ASSAULT_DAMAGE_INFLICTED['engineer'] +
                $guerrilla->tank * Rules::ASSAULT_DAMAGE_INFLICTED['tank'] +
                $guerrilla->bunker * Rules::ASSAULT_DAMAGE_INFLICTED['bunker']) * 
                $deffenseTarget;
    }

    public function getTargetLostEngineerUnits(Guerrilla $guerrilla, $deffenseTarget)
    {
        return ($guerrilla->assault * Rules::ENGINEER_DAMAGE_INFLICTED['assault'] +
                $guerrilla->engineer * Rules::ENGINEER_DAMAGE_INFLICTED['engineer'] +
                $guerrilla->tank * Rules::ENGINEER_DAMAGE_INFLICTED['tank'] +
                $guerrilla->bunker * Rules::ENGINEER_DAMAGE_INFLICTED['bunker']) * 
                $deffenseTarget;
    }

    public function getTargetLostTankUnits(Guerrilla $guerrilla, $deffenseTarget)
    {
        return ($guerrilla->assault * Rules::TANK_DAMAGE_INFLICTED['assault'] +
                $guerrilla->engineer * Rules::TANK_DAMAGE_INFLICTED['engineer'] +
                $guerrilla->tank * Rules::TANK_DAMAGE_INFLICTED['tank'] +
                $guerrilla->bunker * Rules::TANK_DAMAGE_INFLICTED['bunker']) * 
                $deffenseTarget;
    }

    public function getTargetLostBunkerUnits(Guerrilla $guerrilla, $deffenseTarget)
    {
        return ($guerrilla->assault * Rules::BUNKER_DAMAGE_INFLICTED['assault'] +
                $guerrilla->engineer * Rules::BUNKER_DAMAGE_INFLICTED['engineer'] +
                $guerrilla->tank * Rules::BUNKER_DAMAGE_INFLICTED['tank'] +
                $guerrilla->bunker * Rules::BUNKER_DAMAGE_INFLICTED['bunker']) * 
                $deffenseTarget;
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
     * Calculate the defense rate.
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
     * Calculate the attack rate.
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