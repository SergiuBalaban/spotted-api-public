<?php

namespace App\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Http\Requests\UserEmailRequest;
use App\Http\Requests\UserRequest;
use App\Http\Responses\Response;
use App\Libraries\S3Service;
use App\Libraries\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function update(UserRequest $request)
    {
        /** @var User $user */
        $user = $request->user();
        $user->fill($request->all());
        $user->save();

        if($request->has('file')) {
            $storage = new S3Service($user);
            $user = $storage->createAvatar($request->file, $user);
        }

        return response()->json($user, 200);
    }

    /**
     * Update the user email.
     *
     * @param UserEmailRequest $request
     * @return Response|JsonResponse
     * @throws \Throwable
     */
    public function updateEmail(UserEmailRequest $request)
    {
        /** @var User $user */
        $user = $request->user();

        $userService = new UserService($user);
        $user = $userService->sendVerificationToken($user->email);
        if(!$user) {
            throw new ResourceNotFoundException(User::class);
        }

        return response()->json($user, 200);
    }

    /**
     * Resend user verification code
     *
     * @param UserRequest $request
     * @return Response
     * @throws \Throwable
     */
    public function sendEmailVerification(UserRequest $request)
    {
        /** @var User $user */
        $user = $request->user();

        $userService = new UserService($user);
        if(!$userService->sendVerificationToken($user->email)) {
            return $this->response
                ->withError('', trans('message.user_email_verification_send_failed'));
        }

        return $this->response
            ->withSuccess('', trans('message.user_email_verification_send_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->user()->forceDelete();
        auth()->logout();
        return response()->noContent();
    }

}
