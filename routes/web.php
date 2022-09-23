<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use DiDom\Document;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

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
        flash(__('Page exists'))->info();
        $id = DB::table('urls')
            ->where('name', $name)
            ->value('id');
    } else {
        $id = DB::table('urls')
            ->insertGetId([
                'name' => $name,
                'created_at' => Carbon::now()
            ]);
        flash(__('Page added successfully'))->success();
    }

    return redirect()->route('urls.show', $id);
})->name('urls.store');

Route::get('urls/{id}', function ($id) {

    $url = DB::table('urls')->find($id);

    abort_unless(is_object($url), 404);

    $checks = DB::table('url_checks')
        ->where('url_id', $id)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('show', compact('url', 'checks'));
})->name('urls.show');

Route::get('urls', function () {
    $latestCheck = DB::table('url_checks')
        ->select('url_id', 'status_code', DB::raw('MAX(created_at) as last_check_created_at'))
        ->groupBy('url_id', 'status_code')
        ->get();

    $urls = DB::table('urls')
        ->orderBy('created_at', 'asc')
        ->simplePaginate();

    return view('index', compact('urls', 'latestCheck'));
})->name('urls.index');

Route::post('urls/{id}/checks', function ($id) {

    $url = DB::table('urls')->find($id)->name;

    try {
        $response = Http::get($url);
    } catch (RequestException $error) {
        flash($error->getMessage())->error();
        return redirect()->route('urls.show', $id);
    } catch (ConnectionException $error) {
        flash($error->getMessage())->error();
        return redirect()->route('urls.show', $id);
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

    flash(__('Page verified successfully'))->info();
    return redirect()->route('urls.show', $id);
})->name('urls.checks');
