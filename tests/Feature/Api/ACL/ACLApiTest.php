<?php

use App\Models\Permission;
use App\Models\User;

use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test_e2e')->plainTextToken;
});

test('should return 403', function () {
    getJson(route('users.index'),[
        'Authorization' => "Bearer {$this->token}"
    ])->assertForbidden();
});

//->throws(CustomExeption::class);
test('should get resources', function () {
    $permission = Permission::factory()->create(['name' => 'users.index']);
    $this->user->permissions()->attach($permission);

    getJson(route('users.index'),[
        'Authorization' => "Bearer {$this->token}"
    ])->assertOk();
});

test('should get permissions.index', function () {
    $permission = Permission::factory()->create(['name' => 'permissions.index']);
    $this->user->permissions()->attach($permission);

    getJson(route('permissions.index'),[
        'Authorization' => "Bearer {$this->token}"
    ])->assertOk();
});
