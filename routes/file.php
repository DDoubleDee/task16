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

Route::get("/{file}", function (Request $request, $file) {
    $api = ApiKeys::where('akey', $request->bearerToken())->get()->first();
    if(is_null($api)){
        return response(['code' => 401, 'message' => 'Authorization error', 'error' => "AUTH_TOKEN_INCORRECT", 'errors' => []], 401)->header('Content-Type', 'application/json'); 
    }
    $exp = explode("-", $file);
    $ext = explode(".", $exp[count($exp)-1]);
    $id = $ext[count($ext)-2];
    $ext = $ext[count($ext)-1];
    if($ext != "zip"){
        return response(['code' => 404, "message" => "Not found"], 404);
    }
    $project = Project::where(['id' => intval($id), 'creator_id' => $api->creator_id])->get()->first();
    if(!$project){
        return response(['code' => 404, "message" => "Not found"], 404);
    }
    $zip_file = $file;
    $files = glob(storage_path("projects\\".$id."\*"));
    $zip = new ZipArchive();
    $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip = addToZip($zip, $files, "");
    $zip->close();
    return response()->download($zip_file)->deleteFileAfterSend(true);
});
