<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User;
use App\DTO\Users\{
    CreateUserDTO,
    EditUserDTO
};

class UserRepository
{
    public function __construct(protected User $user)
    {
    }

    public function getPaginate(int $totalPerPage = 15, int $page = 1, string $filter = ''): LengthAwarePaginator
    {
        return $this->user->where(function ($query) use ($filter) {
            if ($filter !== '')
                $query->where('name', 'LIKE', "%{$filter}%");
        })
            ->with(['permissions'])
            ->paginate($totalPerPage, ['*'], 'page', $page);
    }

    public function createNew(CreateUserDTO $dto): User
    {
        $data = (array) $dto;
        $data['password'] = bcrypt($data['password']);
        return $this->user->create($data);
    }

    public function findById(string $id, array $relations = []): ?User
    {
        return $this->user->with($relations)->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->user->where('email', $email)->first();
    }

    public function update(EditUserDTO $dto): ?User
    {
        if (!$user = $this->findById($dto->id))  return $user;

        $data = (array) $dto;
        unset($data['password']);
        if ($dto->password !== null)
            $data['password'] = bcrypt($dto->password);

        $user->update($data);
        return $user;
    }

    public function delete(string $id): bool
    {
        if (!$user = $this->findById($id))  return false;
        return  $user->delete();
    }

    public function syncPermissions(string $id,  array $permissions): ?bool
    {
        if (!$user = $this->findById($id))  return false;
        $user->permissions()->sync($permissions);
        return true;
    }

    public function getPermissionsOfUserId(string $id)
    {
        return $this->findById($id)->permissions()->get();
    }

    public function hasPermissions(User $user, string $permissionName): bool
    {
        if($user->isSuperAdmin()){
            return true;
        }
        
        return $user->permissions()->where('name', $permissionName)->exists();
    }
}
