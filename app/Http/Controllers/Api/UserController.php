<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreUserRequest;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\DTO\Users\{
    CreateUserDTO,
    EditUserDTO
};
use App\Http\Requests\Api\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->getPaginate(
            totalPerPage: $request->totalPerPage ?? 15,
            page: $request->page ?? 1,
            filter: $request->get('filter', '')
        );
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user =  $this->userRepository->createNew(
            new CreateUserDTO(
                ...$request->validated()
            )
        );

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!$user =  $this->userRepository->findById($id))
            return response()->json(['message' => 'User not found!'], Response::HTTP_NOT_FOUND);

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        if (!$user =  $this->userRepository->update(
            new EditUserDTO(
                ...[$id, ...$request->validated()]
            )
        ))
            return response()->json(['message' => 'User not found!'], Response::HTTP_NOT_FOUND);

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {      
        if (!$user =  $this->userRepository->delete($id))
            return response()->json(['message' => 'User not found!'], Response::HTTP_NOT_FOUND);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
