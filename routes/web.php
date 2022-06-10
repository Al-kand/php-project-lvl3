<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

Route::view('/', 'main')->name('main');

Route::post('urls', function (Request $request) {

    $request->validate([
        'url.name' => 'required|max:255|url'
    ]);

    $inputUrl = $request->input('url')['name'];
    $parsedUrl = Arr::only(parse_url($inputUrl), ['scheme', 'host']);
    $name = implode("://", $parsedUrl);

    if (DB::table('urls')->where('name', $name)->exists()) {
        $id = DB::table('urls')->where('name', $name)->value('id');
    } else {
        $id = DB::table('urls')->insertGetId([
            'name' => $name,
            'created_at' => Carbon::now()
        ]);
    }

    return redirect()->route('urls.show', $id); //session status
})->name('urls.store');

Route::get('urls/{id}', function ($id) {

    $url = DB::table('urls')->find($id);
    $url->checks = DB::table('url_checks')->where('url_id', $id)->orderBy('created_at', 'desc')->get();

    return view('show', compact('url'));
})->name('urls.show');

Route::get('urls', function () {
    $latestCheck = DB::table('url_checks')
        ->select('url_id', 'status_code', DB::raw('MAX(created_at) as last_check_created_at'))
        ->groupBy('url_id', 'status_code');

    $urls = DB::table('urls')->orderBy('created_at', 'asc')
        ->leftJoinSub($latestCheck, 'latest_check', function ($join) {
            $join->on('urls.id', '=', 'latest_check.url_id');
        })
        ->get();

    return view('index', compact('urls'));
})->name('urls.index');

Route::post('urls/{id}/checks', function ($id) {

    $url = DB::table('urls')->find($id)->name;

    $response = Http::get($url);

    DB::table('url_checks')->insert([
        'url_id' => $id,
        'status_code' => $response->status(),
        'created_at' => Carbon::now()
    ]);
    return redirect()->route('urls.show', $id); //session status
})->name('urls.checks');
