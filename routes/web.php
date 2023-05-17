<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Project;
use App\Models\Module;
use App\Models\ApiKeys;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (Request $request) {
    return redirect('/login');
});
Route::get('/logout', function (Request $request) {
    $request->session()->regenerate();
    $request->session()->remove("token"); // clean session
    return redirect('/login');
});
Route::get('/register', function (Request $request) {
    return view('register');
});
Route::post('/register', function (Request $request) {
    if(!is_null(User::where('email', $request->input("email"))->get()->first())) {
        return view('registererror'); // check if email exists
    }
    User::create(["email" => $request->input("email"), "password" => $request->input("password")]);
    return redirect('/login');
});
Route::get('/login', function (Request $request) {
    return view('login');
});
Route::post('/login', function (Request $request) {
    $validated = $request->validate([
        'email' => 'required',
        'password' => 'required'
    ]);
    $user = User::where('email', $request->input("email"))->get()->first();
    if(is_null($user)) {
        return view('loginerror');
    }
    if($user->password != $request->input("password")){
        return view('loginerror');
    }
    $token = Str::random(100);
    $user->accessToken = $token; // sign in new user
    $user->save();
    session(['token' => $token, 'id' => $user->id]);
    return redirect('/project');
});
Route::get('/project', function (Request $request) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    return view('projects', ["projects" => Project::where('creator_id', session('id'))->get()]); // return projects where creator is current user
});
Route::get('/module', function (Request $request) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    return view('modules', ["modules" => Module::where('creator_id', session('id'))->get()]); // return modules where creator is current user
});
Route::get('/module/create', function (Request $request) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    return view('addmodule'); // diplay form for creating modules
});
Route::post('/module/create', function (Request $request) {
    $validated = $request->validate([
        'file' => 'required'
    ]);
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    $zip = new ZipArchive();
    $status = $zip->open($request->file("file")->getRealPath()); // unzip input archive
    if ($status !== true) { // check if opened successfully
        return back();
    }
    else{
        $storageDestinationPath = storage_path("temp").Str::random(10);
        if (!\File::exists( $storageDestinationPath)) {
         \File::makeDirectory($storageDestinationPath, 0755, true);
        }
        $zip->extractTo($storageDestinationPath); // create and extract to random temp folder
        $zip->close();
        $dirs = File::directories($storageDestinationPath);
        foreach ($dirs as $dir) {
            $module = Module::create(); // for each module in zip file, create database row
            $json = File::get($dir."\modinfo.json"); // get info for module
            File::copyDirectory($dir, storage_path("modules\\".strval($module->id))); // copy form temp to pemanent folder
            $module->name = json_decode($json)->about->moduleName; // get name from info
            $module->creator_id = session('id');
            $module->save();
        }
        File::deleteDirectory($storageDestinationPath); // delete temp
    }
    return redirect('/module');
});
Route::get('/module/archive/{id}', function (Request $request, $id) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    $zip_file = 'module.zip';
    $files = glob(storage_path("modules/".strval($id)."/*"));
    $zip = new ZipArchive();
    $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    foreach ($files as $file) {
        $zip->addFile($file, basename($file)); // acrhive module by file
    }
    $zip->close();
    return response()->download($zip_file)->deleteFileAfterSend(true); // send file download
});
Route::get('/module/delete/{id}', function (Request $request, $id) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    $module = Module::where('id', $id)->get()->first();
    File::deleteDirectory(storage_path("modules\\".strval($module->id))); // delete whole module directory
    $module->delete();
    return redirect('/module');
});
Route::get('/project/create', function (Request $request) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    $out = array();
    $dirs = File::directories(base_path()."\iot\src\modules");
    foreach ($dirs as $dir) {
        $dirs = File::directories(base_path()."\iot\src\modules"."\\".basename($dir));
        for ($i=0; $i < count($dirs); $i++) { 
            $out[basename($dir)][$i] = basename($dirs[$i]); // get all base modules
        }
    }
    $modules = Module::where('creator_id', session('id'))->get()->toArray();
    for ($i=0; $i < count($modules); $i++) { 
        $out["custom"][$i] = ["name" => $modules[$i]["name"], "id" => $modules[$i]["id"]]; // get all custom modules
    }
    return view('addproject', ["basemodules" => $out]);
});
Route::post('/project/create', function (Request $request) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    $project = Project::create(["name" => $request->input("name"), "creator_id" => session('id')]);
    File::copyDirectory(base_path()."\iot", storage_path("projects\\".strval($project->id)));
    foreach (File::directories(storage_path("projects\\".strval($project->id)."\src\modules")) as $module) {
        foreach (File::directories(storage_path("projects\\".strval($project->id)."\src\modules\\".basename($module))) as $dir) {
            $arr = $request->input(basename($module));
            if($arr){
                if(!in_array(basename($dir), $arr)){ // delete a dir if module is not selected
                    File::deleteDirectory(storage_path("projects\\".strval($project->id)."\src\modules\\".basename($module)."\\".basename($dir)));
                }
            }
        }
    }
    $custom = $request->input("custom");
    if($custom){
        foreach ($custom as $id) {
            $module = Module::where('id', intval($id))->get()->first(); // add custom modules to folder
            File::copyDirectory(storage_path("modules\\".$id), storage_path("projects\\".strval($project->id)."\src\modules\custom\\".$module->name));
        }
    }
    return redirect("project");
});
Route::get('/project/archive/{id}', function (Request $request, $id) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    $zip_file = 'project.zip';
    $files = glob(storage_path("projects\\".strval($id)."\*"));
    $zip = new ZipArchive();
    $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip = addToZip($zip, $files, ""); // recursively add everything to zip
    $zip->close();
    return response()->download($zip_file)->deleteFileAfterSend(true);
});
Route::get('/apikey', function (Request $request) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    return view('apikeys', ["apikeys" => ApiKeys::where('creator_id', session('id'))->get()]);
});
Route::get('/apikey/create', function (Request $request) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    return view('apikeyscreate');
});
Route::post('/apikey/create', function (Request $request) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    $akey = Str::random(100); // generate random string for token
    ApiKeys::create(["name" => $request->input("name"), "akey" => $akey, "creator_id" => session('id')]);
    return redirect('/apikey');
});
Route::get('/apikey/delete/{id}', function (Request $request, $id) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    ApiKeys::where('id', $id)->get()->first()->delete();
    return view('apikeys', ["apikeys" => ApiKeys::where('creator_id', session('id'))->get()]);
});
function addToZip($zip, $files, $route) {
    foreach ($files as $file) {
        if(is_dir($file)){ // if it's a directory
            $files = glob($file."\*"); // get list of all files
            $zip = addToZip($zip, $files, $route.basename($file)."\\"); // recursive, add routes together
        } else {
            $zip->addFile($file, $route.basename($file)); // if it's a file, add it to archive with correct route
        }
    }
    return $zip;
}
