<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UrlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $url = [
            'name' => 'http://fake.com',
            'created_at' => '2022-06-17 06:59:24'
        ];

        DB::table('urls')->insert($url);
    }

    public function testMain()
    {
        $response = $this->get(route('main'));
        $response->assertOk();
    }

    public function testIndex()
    {
        $response = $this->get(route('urls.index'));
        $response->assertOk();
    }

    public function testStore()
    {
        $data['url'] = ['name' => 'https://domain.com/example'];
        $response = $this->post(route('urls.store'), $data);
        $this->assertDatabaseHas('urls', [
            'name' => 'https://domain.com'
        ]);
        $response->assertRedirect(route('urls.show', 2));
        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
    }

    public function testShow()
    {
        $response = $this->get(route('urls.show', 1));
        $response->assertOk();
    }

    public function testUrlsChecks()
    {
        $this->testStore();
        Http::fake();
        $id = 1;
        $response = $this->post(route('urls.checks', $id));
        $response->assertRedirect(route('urls.show', $id));
        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $this->assertDatabaseHas('url_checks', [
            'url_id' => $id,
        ]);
    }
}
