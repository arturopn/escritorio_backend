<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUsersLawyerChatAPIRequest;
use App\Http\Requests\UpdateUsersLawyerChatAPIRequest;
use App\Models\UsersLawyerChat;
use App\Repositories\UsersLawyerChatRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class UsersLawyerChatController
 * @package App\Http\Controllers
 */

class UsersLawyerChatAPIController extends AppBaseController
{
    /** @var  UsersLawyerChatRepository */
    private $usersLawyerChatRepository;

    public function __construct(UsersLawyerChatRepository $usersLawyerChatRepo)
    {
        $this->usersLawyerChatRepository = $usersLawyerChatRepo;
    }

    /**
     * Display a listing of the UsersLawyerChat.
     * GET|HEAD /usersLawyerChats
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function index(Request $request)
    {
        $usersLawyerChats = $this->usersLawyerChatRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );


        return $this->sendResponse($usersLawyerChats->toArray(), 'Users Lawyer Chats retrieved successfully');
    }

    /**
     * Store a newly created UsersLawyerChat in storage.
     * POST /usersLawyerChats
     *
     * @param CreateUsersLawyerChatAPIRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function store(CreateUsersLawyerChatAPIRequest $request)
    {
        $input = $request->all();

        $users = DB::table('users')
            ->join('user_roles', 'user_roles.userId', '=', 'users.userId')
            ->where('user_roles.rolId', 2)
            ->get()
            ->random(1);

        $input['lawyer_id'] = $users[0]->userId;
        $input['firebase_lawyerId'] = $users[0]->googleToken;

        $usersLawyerChat = $this->usersLawyerChatRepository->create($input);

        $usersLawyerChat = UsersLawyerChat::with(['user', 'lawyer'])->find($usersLawyerChat->id);

        return $this->sendResponse($usersLawyerChat->toArray(), 'Users Lawyer Chat saved successfully');
    }

    /**
     * Display the specified UsersLawyerChat.
     * GET|HEAD /usersLawyerChats/{id}
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function show($id)
    {
        /** @var UsersLawyerChat $usersLawyerChat */
        //$usersLawyerChat = $this->usersLawyerChatRepository->find($id);
        $lawyer = DB::table('user_roles')->where([
            ['userId', '=', $id],
            ['rolId', '=', '2'],
          ])->first();
        
        if($lawyer){
            $usersLawyerChat = UsersLawyerChat::with(['user','lawyer'])->where('lawyer_id', $id)->get();
        }else{
            $usersLawyerChat = UsersLawyerChat::with(['user','lawyer'])->where('user_id', $id)->get();
        }

        if (empty($usersLawyerChat)) {
            return $this->sendError('Users Lawyer Chat not found');
        }

        return $this->sendResponse($usersLawyerChat->toArray(), 'Users Lawyer Chat retrieved successfully');
    }

    /**
     * Update the specified UsersLawyerChat in storage.
     * PUT/PATCH /usersLawyerChats/{id}
     *
     * @param int $id
     * @param UpdateUsersLawyerChatAPIRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function update($id, UpdateUsersLawyerChatAPIRequest $request)
    {
        $input = $request->all();

        /** @var UsersLawyerChat $usersLawyerChat */
        $usersLawyerChat = $this->usersLawyerChatRepository->find($id);

        if (empty($usersLawyerChat)) {
            return $this->sendError('Users Lawyer Chat not found');
        }

        $usersLawyerChat = $this->usersLawyerChatRepository->update($input, $id);

        return $this->sendResponse($usersLawyerChat->toArray(), 'UsersLawyerChat updated successfully');
    }

    /**
     * Remove the specified UsersLawyerChat from storage.
     * DELETE /usersLawyerChats/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function destroy($id)
    {
        /** @var UsersLawyerChat $usersLawyerChat */
        $usersLawyerChat = $this->usersLawyerChatRepository->find($id);

        if (empty($usersLawyerChat)) {
            return $this->sendError('Users Lawyer Chat not found');
        }

        $usersLawyerChat->delete();

        return $this->sendSuccess('Users Lawyer Chat deleted successfully');
    }
}
