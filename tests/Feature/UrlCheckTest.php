<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UrlCheckTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test UrlsChecks.
     *
     * @return void
     */
    public function testUrlsChecks()
    {
        $url = [
            'name' => 'http://fake.com',
            'created_at' => '2022-06-17 06:59:24'
        ];
        $id = DB::table('urls')->insertGetId($url);

        Http::fake();
        $response = $this->post(route('urls.checks', $id));
        $response->assertRedirect(route('urls.show', $id));
        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $this->assertDatabaseHas('url_checks', [
            'url_id' => $id,
        ]);
    }
}
