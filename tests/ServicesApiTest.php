<?php namespace Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Services;

class ServicesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_services()
    {
        $services = factory(Services::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/services', $services
        );

        $this->assertApiResponse($services);
    }

    /**
     * @test
     */
    public function test_read_services()
    {
        $services = factory(Services::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/services/'.$services->id
        );

        $this->assertApiResponse($services->toArray());
    }

    /**
     * @test
     */
    public function test_update_services()
    {
        $services = factory(Services::class)->create();
        $editedServices = factory(Services::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/services/'.$services->id,
            $editedServices
        );

        $this->assertApiResponse($editedServices);
    }

    /**
     * @test
     */
    public function test_delete_services()
    {
        $services = factory(Services::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/services/'.$services->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/services/'.$services->id
        );

        $this->response->assertStatus(404);
    }
}
