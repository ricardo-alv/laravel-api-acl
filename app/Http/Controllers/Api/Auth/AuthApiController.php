<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Api\Auth\AuthApiRequest;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\{Hash, Auth};
use Illuminate\Http\{Request, Response};


class AuthApiController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function auth(AuthApiRequest $request)
    {
        $user = $this->userRepository->findByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken($request->device_name)->plainTextToken;
        return response()->json(['token' => $token]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return new UserResource($user);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
