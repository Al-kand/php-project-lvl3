<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class UrlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * id
     *
     * @var int
     *
     */
    protected $id;


    /**
     * urls
     *
     * @var array
     *
     */
    protected $urls;

    protected function setUp(): void
    {
        parent::setUp();

        $url = [
            'name' => 'http://fake.com',
            'created_at' => '2022-06-17 06:59:24'
        ];

        $this->id = DB::table('urls')->insertGetId($url);
        $this->urls = [
            'empty' => '',
            'long' => 'https://domaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa' .
                'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa' .
                'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaain.com',
            'noValid' => 'https//domain.com',
            'valid' => 'https://domain.com/path?data=value&foo=bar',
        ];
    }

    private function getUrlName(string $key): array
    {
        return [
            'url' => [
                'name' => $this->urls[$key]
            ]
        ];
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
        $response = $this->post(route('urls.store'), $this->getUrlName('empty'));
        $response->assertSessionHasErrors();
        $response->assertStatus(302);

        $response = $this->post(route('urls.store'), $this->getUrlName('long'));
        $response->assertSessionHasErrors();
        $response->assertStatus(302);

        $response = $this->post(route('urls.store'), $this->getUrlName('noValid'));
        $response->assertSessionHasErrors();
        $response->assertStatus(302);

        $this->assertDatabaseMissing('urls', [
            'name' => 'https://domain.com'
        ]);

        $response = $this->post(route('urls.store'), $this->getUrlName('valid'));
        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);

        $this->assertDatabaseHas('urls', [
            'name' => 'https://domain.com'
        ]);
    }

    public function testShow()
    {
        $response = $this->get(route('urls.show', $this->id));
        $response->assertOk();
    }
}
