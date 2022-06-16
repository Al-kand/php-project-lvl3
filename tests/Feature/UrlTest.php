<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Faker\Factory;
use Illuminate\Support\Facades\Http;

class UrlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $urls = array_map(
            fn () => [
                'name' => $this->getUrlName($this->faker->unique()->url()),
                'created_at' => $this->faker->dateTime()
            ],
            range(0, 3)
        );

        DB::table('urls')->insert($urls);
    }

    private function getUrlName($url)
    {
        return substr($url, 0, strpos($url, '/', 8));
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
        $url = $this->faker->unique()->url();

        $data['url'] = ['name' => $url];
        $response = $this->post(route('urls.store'), $data);

        $name = $this->getUrlName($url);
        $url = DB::table('urls')->where('name', $name)->first();
        $response->assertRedirect(route('urls.show', $url->id));

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
    }

    public function testShow()
    {
        $id = DB::table('urls')->insertGetId([
            'name' => $this->getUrlName($this->faker->unique()->url()),
            'created_at' => $this->faker->dateTime()
        ]);

        $response = $this->get(route('urls.show', $id));

        $response->assertOk();
    }

    public function testUrlsChecks()
    {
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
