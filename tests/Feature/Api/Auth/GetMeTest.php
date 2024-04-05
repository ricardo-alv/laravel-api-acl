<?php

use App\Models\Permission;
use App\Models\User;

use function Pest\Laravel\getJson;

it('unauthenticated user cannot get our data', function () {
    getJson(route('auth.me'), [])
        ->assertJson([
            'message' => 'Unauthenticated.'
        ])
        ->assertUnauthorized();
});

it('should return user with our data', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test_e2e')->plainTextToken;

    getJson(route('auth.me'), [
        'Authorization' => "Bearer {$token}"
    ])
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'permissions' => []
            ]
        ])
        ->assertOk();
});


it('should return user with our data and our permissions', function () {

    Permission::factory(10)->create();
    $permissionsIds = Permission::factory(10)->create()->pluck('id')->toArray();
    $user = User::factory()->create();
    $token = $user->createToken('test_e2e')->plainTextToken;
    $user->permissions()->attach($permissionsIds);

    getJson(route('auth.me'), [
        'Authorization' => "Bearer {$token}"
    ])
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'permissions' => [
                    '*' => [
                        'id',
                        'name',
                        'description'
                    ]
                ]
            ]
        ])   
        ->assertJsonCount(10,'data.permissions')
        ->assertOk();
});
