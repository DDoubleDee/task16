<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Project;
use App\Models\ApiKeys;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get("/projects", function (Request $request) {
    $api = ApiKeys::where('akey', $request->bearerToken())->get()->first();
    if(is_null($api)){
        return response(['code' => 401, 'message' => 'Authorization error', 'error' => "AUTH_TOKEN_INCORRECT", 'errors' => []], 401)->header('Content-Type', 'application/json'); 
    }
    $projects = Project::where('creator_id', $api->creator_id)->get();
    $out = array();
    for ($i=0; $i < count($projects); $i++) { 
        $out[$i] = ["id" => $projects[$i]->id, "name" => $projects[$i]->name, "file" => URL::to('/')."/"."files/".$projects[$i]->name."-".strval($projects[$i]->id).".zip"];
    }
    return response($out, 200)->header('Content-Type', 'application/json');
});
