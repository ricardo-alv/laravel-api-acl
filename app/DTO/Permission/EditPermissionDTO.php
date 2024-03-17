<?php

namespace App\DTO\Permission;

class EditPermissionDTO
{
    public function __construct(
       readonly public string $id,
       readonly public string $name,
       readonly public string $description,
    ) {
        //
    }
}
