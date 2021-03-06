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
use App\Models\User;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
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

    public function sendNotification ($googletoken, Request $request)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('Tienes un mensaje');
        $notificationBuilder->setBody($request->description)
                            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = User::where('googleToken', $googletoken)->first(['firebase_registration_token']);
        $token = $token['firebase_registration_token'];
        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        // return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        // return Array (key : oldToken, value : new token - you must change the token in your database)
        $downstreamResponse->tokensToModify();

        // return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:error) - in production you should remove from your database the tokens
        $downstreamResponse->tokensWithError();
    }
}
