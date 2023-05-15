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
    return view('welcome');
});
Route::get('/logout', function (Request $request) {
    $request->session()->regenerate();
    $request->session()->remove("token");
    return redirect('/login');
});
Route::get('/register', function (Request $request) {
    return view('register');
});
Route::post('/register', function (Request $request) {
    if(!is_null(User::where('email', $request->input("email"))->get()->first())) {
        return response($request->all(), 200);
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
    $user->accessToken = $token;
    $user->save();
    session(['token' => $token, 'id' => $user->id]);
    return redirect('/project');
});
Route::get('/project', function (Request $request) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    return view('projects', ["projects" => Project::where('creator_id', session('id'))->get()]);
});
Route::get('/module', function (Request $request) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    return view('modules', ["modules" => Module::where('creator_id', session('id'))->get()]);
});
Route::get('/module/create', function (Request $request) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    return view('addmodule');
});
Route::post('/module/create', function (Request $request) {
    $validated = $request->validate([
        'file' => 'required'
    ]);
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    $zip = new ZipArchive();
    $status = $zip->open($request->file("file")->getRealPath());
    if ($status !== true) {
     throw new \Exception($status);
    }
    else{
        $storageDestinationPath = storage_path("temp").Str::random(10);
        if (!\File::exists( $storageDestinationPath)) {
         \File::makeDirectory($storageDestinationPath, 0755, true);
        }
        $zip->extractTo($storageDestinationPath);
        $zip->close();
        $dirs = File::directories($storageDestinationPath);
        foreach ($dirs as $dir) {
            $module = Module::create();
            $json = File::get($dir."\modinfo.json");
            File::copyDirectory($dir, storage_path("modules\\".strval($module->id)));
            $module->name = json_decode($json)->about->moduleName;
            $module->creator_id = session('id');
            $module->save();
        }
        File::deleteDirectory($storageDestinationPath);
    }
    return redirect('/module');
});
Route::get('/module/archive/{id}', function (Request $request, $id) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    $zip_file = 'archive.zip';
    $files = glob(storage_path("modules/".strval($id)."/*"));
    $zip = new ZipArchive();
    $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    foreach ($files as $file) {
        $zip->addFile($file, basename($file));
    }
    $zip->close();
    return response()->download($zip_file)->deleteFileAfterSend(true);
});
Route::get('/module/delete/{id}', function (Request $request, $id) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    $module = Module::where('id', $id)->get()->first();
    File::deleteDirectory(storage_path("modules\\".strval($module->id)));
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
            $out[basename($dir)][$i] = basename($dirs[$i]);
        }
    }
    $modules = Module::where('creator_id', session('id'))->get()->toArray();
    for ($i=0; $i < count($modules); $i++) { 
        $out["custom"][$i] = $modules[$i]["name"];
    }
    return view('addproject', ["basemodules" => $out]);
});
Route::post('/project/create', function (Request $request) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    return response($request->all(), 200);
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
    $akey = Str::random(100);
    ApiKeys::create(["name" => $request->input("name"), "akey" => $akey, "creator_id" => session('id')]);
    return redirect('/apikey');
});
Route::get('/apikey/delete/{id}', function (Request $request, $id) {
    if(is_null($request->session()->get('token', null))){return redirect('/login');}
    ApiKeys::where('id', $id)->get()->first()->delete();
    return view('apikeys', ["apikeys" => ApiKeys::where('creator_id', session('id'))->get()]);
});
