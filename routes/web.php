<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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

Route::view('/', 'main')->name('main');

Route::post('urls', function (Request $request) {

    $request->validate([
        'url.name' => 'required|unique:urls,name|max:255'
    ]);

    $name = $request->input('url')['name'];

    $id = DB::table('urls')->insertGetId([
        'name' => $name,
        'created_at' => Carbon::now()
    ]);
    return redirect()->route('urls.show', $id);
})->name('urls.store');

Route::get('urls/{id}', function ($id) {
    $url = DB::table('urls')->find($id);
    return view('show', compact('url'));
})->name('urls.show');

Route::get('urls', function () {
    $urls = DB::table('urls')->orderBy('id')->get()->all();
    return view('index', compact('urls'));
})->name('urls.index');
