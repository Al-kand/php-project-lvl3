<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
// use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use DiDom\Document;

Route::view('/', 'main')->name('main');

Route::post('urls', function (Request $request) {

    $request->validate([
        'url.name' => 'required|max:255|url'
    ]);

    $inputUrl = $request->input('url')['name'];
    $scheme = parse_url($inputUrl, PHP_URL_SCHEME);
    $host = parse_url($inputUrl, PHP_URL_HOST);
    $name = "{$scheme}://{$host}";


    if (DB::table('urls')->where('name', $name)->exists()) {
        $status = ['css' => 'info', 'message' => 'Страница существует'];
        $id = DB::table('urls')
            ->where('name', $name)
            ->value('id');
    } else {
        $id = DB::table('urls')
            ->insertGetId([
                'name' => $name,
                'created_at' => Carbon::now()
            ]);
        $status = ['css' => 'success', 'message' => 'Страница успешно добавлена'];
    }

    return redirect()->route('urls.show', $id)->with('status', $status);
})->name('urls.store');

Route::get('urls/{id}', function ($id) {

    $url = DB::table('urls')->find($id);

    if (!is_object($url)) {
        abort(404);
    }

    $url = (array)$url;
    $url['checks'] = DB::table('url_checks')
        ->where('url_id', $id)
        ->orderBy('created_at', 'desc')
        ->get();
    $url = (object)$url;

    return view('show', compact('url'));
})->name('urls.show');

Route::get('urls', function () {
    $latestCheck = DB::table('url_checks')
        ->select('url_id', 'status_code', DB::raw('MAX(created_at) as last_check_created_at'))
        ->groupBy('url_id', 'status_code');

    $urls = DB::table('urls')
        ->orderBy('created_at', 'asc')
        ->leftJoinSub($latestCheck, 'latest_check', function ($join) {
            $join->on('urls.id', '=', 'latest_check.url_id');
        })
        ->get();

    return view('index', compact('urls'));
})->name('urls.index');

Route::post('urls/{id}/checks', function ($id) {

    $url = DB::table('urls')->find($id)->name;

    try {
        $response = Http::get($url);
    } catch (\Throwable $th) {
        $status = ['css' => 'danger', 'message' => $th->getMessage()];
        return back()->with('status', $status);
    }

    $data = [
        'url_id' => $id,
        'status_code' =>  $response->status(),
        'created_at' => Carbon::now()
    ];

    $html = $response->body();

    if (!empty($html)) {
        $document = new Document($html);
        $data['title'] = optional($document->first('head>title'))->text();
        $data['h1'] = optional($document->first('body h1'))->text();
        $data['description'] = optional(
            $document->first('head>meta[name="description"]::attr(content)'),
            fn ($value) => $value
        );
    }

    DB::table('url_checks')
        ->insert($data);

    $status = ['css' => 'info', 'message' => 'Страница успешно проверена'];

    return redirect()->route('urls.show', $id)->with('status', $status);
})->name('urls.checks');
