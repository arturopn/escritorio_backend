<?php namespace Tests\Repositories;

use App\Models\Services;
use App\Repositories\ServicesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ServicesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ServicesRepository
     */
    protected $servicesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->servicesRepo = \App::make(ServicesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_services()
    {
        $services = factory(Services::class)->make()->toArray();

        $createdServices = $this->servicesRepo->create($services);

        $createdServices = $createdServices->toArray();
        $this->assertArrayHasKey('id', $createdServices);
        $this->assertNotNull($createdServices['id'], 'Created Services must have id specified');
        $this->assertNotNull(Services::find($createdServices['id']), 'Services with given id must be in DB');
        $this->assertModelData($services, $createdServices);
    }

    /**
     * @test read
     */
    public function test_read_services()
    {
        $services = factory(Services::class)->create();

        $dbServices = $this->servicesRepo->find($services->id);

        $dbServices = $dbServices->toArray();
        $this->assertModelData($services->toArray(), $dbServices);
    }

    /**
     * @test update
     */
    public function test_update_services()
    {
        $services = factory(Services::class)->create();
        $fakeServices = factory(Services::class)->make()->toArray();

        $updatedServices = $this->servicesRepo->update($fakeServices, $services->id);

        $this->assertModelData($fakeServices, $updatedServices->toArray());
        $dbServices = $this->servicesRepo->find($services->id);
        $this->assertModelData($fakeServices, $dbServices->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_services()
    {
        $services = factory(Services::class)->create();

        $resp = $this->servicesRepo->delete($services->id);

        $this->assertTrue($resp);
        $this->assertNull(Services::find($services->id), 'Services should not exist in DB');
    }
}
