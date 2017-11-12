<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guerrilla;
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

}
