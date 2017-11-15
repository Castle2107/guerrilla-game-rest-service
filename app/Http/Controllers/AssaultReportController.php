<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\ExceptionTrait;
use App\Models\AssaultReport;
use App\Models\Guerrilla;

class AssaultReportController extends Controller
{
    use ExceptionTrait;
    
    public function index($id)
    {
    	$guerrilla = Guerrilla::where('id', '=', $id)->firstOrFail();

    	return response()->json([
    		'status' => 200,
    		'attacker_report' => $guerrilla->attackerReport,
    		'target_report' => $guerrilla->targetReport
    	]);
    }

    public function show($guerrillaId, $assaultReportId)
    {
    	$assaultReport = AssaultReport::where('id', '=', $assaultReportId)->firstOrFail();

    	return response()->json([
    		'status' => 200,
    		'guerrilla_id' => $guerrillaId,
    		'assault_report' => $assaultReport
    	]);
    }
}
