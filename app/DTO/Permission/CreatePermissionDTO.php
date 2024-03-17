<?php

namespace App\DTO\Permission;

class CreatePermissionDTO
{
    public function __construct(
       readonly public string $name,
       readonly public string $description = '',    
    ) {
        //
    }
}
