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
        //TODO: create GuerrillaRequest
        $email = $request->email;
        $username = $request->username;
        $guerrilla_type = $request->faction;

        $guerrilla = new Guerrilla();
        $guerrilla->email = $email;
        $guerrilla->username = $username;
        $guerrilla->guerrilla_type = $guerrilla_type;
        $guerrilla->save();

        return response()->json([
            'username' => $guerrilla->username, 
            'id' => $guerrilla->id
        ], 200);
    }

    public function show($id)
    {
        try {
            $guerrilla = Guerrilla::where('id', '=', $id)
                ->orWhere('username', '=', $id)
                ->firstOrFail();
            return response()->json(
                $guerrilla->guerrillaJSONFormat()
            );
        } catch (ModelNotFoundException $e) {
            return $this->ModelResponse($e);
        }
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        try {
            $guerrilla = Guerrilla::findOrFail($id);
            if ($guerrilla->delete()) {
                return response()->json('Guerrilla deleted', 200);
            } else {
                return response()->json('Error at deleting model', 500);
            }
        } catch (ModelNotFoundException $e) {
            return $this->ModelResponse($e);
        }
    }


    /**
    * TODO: change it all, it sucks xD
    */
    public function buyBattleUnit(Request $request)
    {
        try {
            $guerrilla = Guerrilla::findOrFail($request->guerrilla);
            $battleUnit = $request->battle_unit;
            switch ($battleUnit) {
                case 'ASSAULT_UNIT':
                    return $this->buyOrFail(Rules::ASSAULT_UNIT, $guerrilla);
                    break;
                case 'ENGINEER_UNIT':
                    return $this->buyOrFail(Rules::ENGINEER_UNIT, $guerrilla);
                    break;
                case 'TANK_UNIT':
                    return $this->buyOrFail(Rules::TANK_UNIT, $guerrilla);
                    break;
                case 'BUNKER_UNIT':
                    return $this->buyOrFail(Rules::BUNKER_UNIT, $guerrilla);
                    break;
                default:
                    return response()->json('No valid battle unit indicated', 403);
                    break;
            }

        } catch (ModelNotFoundException $e) {
            return $this->ModelResponse($e);   
        }        
    }


    /**
    * $btuVal = battle unit global value
    */
    private function buyOrFail($btuVal = [], $guerrilla) {

        if ($this->hasResources($btuVal, $guerrilla)) {

            $this->reduceResources($btuVal, $guerrilla);
            $this->addPoints($btuVal, $guerrilla);
            $this->addBattleUnit($btuVal, $guerrilla);
            
            $guerrilla->save();

            return response()->json([
                'status' => 'ok'
            ], 200);

        } else {
            
            return response()->json([
                'status' => 'failed',
                'error' => 'You dont have enough resources to buy this unit'
            ], 403);

        }
    }

    private function hasResources($btuVal, $guerrilla) {
        return ($guerrilla->money >= $btuVal['money'])
            && ($guerrilla->people >= $btuVal['people'])
            && ($guerrilla->oil >= $btuVal['oil']);
    }

    private function reduceResources($btuVal = [], $guerrilla) {
        $guerrilla->money -= $btuVal['money'];
        $guerrilla->people -= $btuVal['people'];
        $guerrilla->oil -= $btuVal['oil'];
    }

    private function addPoints($btuVal, $guerrilla) {
        $guerrilla->ranking_score += $btuVal['points'];
    }

    private function addBattleUnit($btuVal, $guerrilla) {
        // TODO: it should add the battle unit
    }

    // TODO: remove after testing functions
    private function customResponse($data) {
        return response()->json($data, 200);
    }

}
