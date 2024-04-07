<?php

namespace App\Http\Controllers\Api;


use App\Repositories\PermissionRepository;
use App\Http\Resources\PermissionResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\DTO\Permission\{
    CreatePermissionDTO,
    EditPermissionDTO
};
use App\Http\Requests\Api\{
    StorePermissionRequest,
    UpdatePermissionRequest,
};

class PermissionController extends Controller
{
    public function __construct(private PermissionRepository $permissionRepository)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = $this->permissionRepository->getPaginate(
            totalPerPage: $request->total_per_page ?? 15,
            page: $request->page ?? 1,
            filter: $request->get('filter', '')
        );
        return PermissionResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        $user =  $this->permissionRepository->createNew(
            new CreatePermissionDTO(
                ...$request->validated()
            )
        );

        return new PermissionResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!$user =  $this->permissionRepository->findById($id))
            return response()->json(['message' => 'Permission not found!'], Response::HTTP_NOT_FOUND);

        return new PermissionResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, string $id)
    {
        if (!$user =  $this->permissionRepository->update(
            new EditPermissionDTO(
                ...[$id, ...$request->validated()]
            )
        ))
            return response()->json(['message' => 'Permission not found!'], Response::HTTP_NOT_FOUND);

        return new PermissionResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {      
        if (!$user =  $this->permissionRepository->delete($id))
            return response()->json(['message' => 'Permission not found!'], Response::HTTP_NOT_FOUND);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
