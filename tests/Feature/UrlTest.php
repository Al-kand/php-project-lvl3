<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class UrlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $data = array_map(
            fn () => [
                'name' => $this->faker->unique()->url(),
                'created_at' => $this->faker->dateTime()
            ],
            range(0, 3)
        );

        DB::table('urls')->insert($data);
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
        $name = ['name' => $this->faker->unique()->url()];
        $data = ['url' => $name];
        $response = $this->post(route('urls.store'), $data);

        $url = DB::table('urls')->where($name)->first();
        $response->assertRedirect(route('urls.show', $url->id));

        $response->assertSessionHasNoErrors();
    }

    public function testShow()
    {
        $id = DB::table('urls')->insertGetId([
            'name' => $this->faker->unique()->url(),
            'created_at' => $this->faker->dateTime()
        ]);

        $response = $this->get(route('urls.show', $id));

        $response->assertOk();
    }
}
