<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class PermissionUserController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function syncPermissionOfUser(string $id, Request $request)
    {
        if (!$this->userRepository->syncPermissions($id, $request->permissions))
            return response()->json(['message' => 'User not found!'], Response::HTTP_NOT_FOUND);

        return response()->json(['message' => 'ok'], Response::HTTP_OK);
    }

    public function getPermissionOfUser(string $id)
    {
        if (!$this->userRepository->findById($id))
            return response()->json(['message' => 'User not found!'], Response::HTTP_NOT_FOUND);

        $permissions = $this->userRepository->getPermissionsOfUserId($id);
        return PermissionResource::collection($permissions);
    }
}
