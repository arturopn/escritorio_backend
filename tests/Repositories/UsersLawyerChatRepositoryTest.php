<?php namespace Tests\Repositories;

use App\Models\UsersLawyerChat;
use App\Repositories\UsersLawyerChatRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class UsersLawyerChatRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var UsersLawyerChatRepository
     */
    protected $usersLawyerChatRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->usersLawyerChatRepo = \App::make(UsersLawyerChatRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_users_lawyer_chat()
    {
        $usersLawyerChat = factory(UsersLawyerChat::class)->make()->toArray();

        $createdUsersLawyerChat = $this->usersLawyerChatRepo->create($usersLawyerChat);

        $createdUsersLawyerChat = $createdUsersLawyerChat->toArray();
        $this->assertArrayHasKey('id', $createdUsersLawyerChat);
        $this->assertNotNull($createdUsersLawyerChat['id'], 'Created UsersLawyerChat must have id specified');
        $this->assertNotNull(UsersLawyerChat::find($createdUsersLawyerChat['id']), 'UsersLawyerChat with given id must be in DB');
        $this->assertModelData($usersLawyerChat, $createdUsersLawyerChat);
    }

    /**
     * @test read
     */
    public function test_read_users_lawyer_chat()
    {
        $usersLawyerChat = factory(UsersLawyerChat::class)->create();

        $dbUsersLawyerChat = $this->usersLawyerChatRepo->find($usersLawyerChat->id);

        $dbUsersLawyerChat = $dbUsersLawyerChat->toArray();
        $this->assertModelData($usersLawyerChat->toArray(), $dbUsersLawyerChat);
    }

    /**
     * @test update
     */
    public function test_update_users_lawyer_chat()
    {
        $usersLawyerChat = factory(UsersLawyerChat::class)->create();
        $fakeUsersLawyerChat = factory(UsersLawyerChat::class)->make()->toArray();

        $updatedUsersLawyerChat = $this->usersLawyerChatRepo->update($fakeUsersLawyerChat, $usersLawyerChat->id);

        $this->assertModelData($fakeUsersLawyerChat, $updatedUsersLawyerChat->toArray());
        $dbUsersLawyerChat = $this->usersLawyerChatRepo->find($usersLawyerChat->id);
        $this->assertModelData($fakeUsersLawyerChat, $dbUsersLawyerChat->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_users_lawyer_chat()
    {
        $usersLawyerChat = factory(UsersLawyerChat::class)->create();

        $resp = $this->usersLawyerChatRepo->delete($usersLawyerChat->id);

        $this->assertTrue($resp);
        $this->assertNull(UsersLawyerChat::find($usersLawyerChat->id), 'UsersLawyerChat should not exist in DB');
    }
}
