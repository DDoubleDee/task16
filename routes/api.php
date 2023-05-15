<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Project;
use App\Models\ApiKeys;
use App\Models\User;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get("/projects", function (Request $request) {
    $api = ApiKeys::where('akey', $request->bearerToken())->get()->first();
    if(is_null($api)){
        return response(['code' => 401, 'message' => 'Authorization error', 'error' => "AUTH_TOKEN_INCORRECT", 'errors' => []], 401)->header('Content-Type', 'application/json'); 
    }
    return response(["projects" => Project::where('creator_id', $api->creator_id)->get()], 200)->header('Content-Type', 'application/json');
});
