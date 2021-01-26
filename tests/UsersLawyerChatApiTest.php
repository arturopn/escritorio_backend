<?php namespace Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\UsersLawyerChat;

class UsersLawyerChatApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_users_lawyer_chat()
    {
        $usersLawyerChat = factory(UsersLawyerChat::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/users_lawyer_chats', $usersLawyerChat
        );

        $this->assertApiResponse($usersLawyerChat);
    }

    /**
     * @test
     */
    public function test_read_users_lawyer_chat()
    {
        $usersLawyerChat = factory(UsersLawyerChat::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/users_lawyer_chats/'.$usersLawyerChat->id
        );

        $this->assertApiResponse($usersLawyerChat->toArray());
    }

    /**
     * @test
     */
    public function test_update_users_lawyer_chat()
    {
        $usersLawyerChat = factory(UsersLawyerChat::class)->create();
        $editedUsersLawyerChat = factory(UsersLawyerChat::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/users_lawyer_chats/'.$usersLawyerChat->id,
            $editedUsersLawyerChat
        );

        $this->assertApiResponse($editedUsersLawyerChat);
    }

    /**
     * @test
     */
    public function test_delete_users_lawyer_chat()
    {
        $usersLawyerChat = factory(UsersLawyerChat::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/users_lawyer_chats/'.$usersLawyerChat->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/users_lawyer_chats/'.$usersLawyerChat->id
        );

        $this->response->assertStatus(404);
    }
}
