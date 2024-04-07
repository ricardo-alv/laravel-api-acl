<?php

use function Pest\Laravel\{
    assertDatabaseCount,
    assertDatabaseHas,
    getJson,
    postJson,
    withoutMiddleware
};
use App\Http\Middleware\{ACLMiddleware};
use App\Models\{Permission, User};


beforeEach(function () {
    withoutMiddleware(ACLMiddleware::class);
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test_e2e')->plainTextToken;
});

/**Sem dados */
test('should return all permissions of user - with empty permissions', function () {
    $user = User::factory()->create();
    getJson(route('users.permissions', $this->user->id), [
        'Authorization' => "Bearer {$this->token}"
    ])
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'description']
            ]
        ]);
});

/**Com dados */
test('should return all permissions of user - with permissions', function () {
    Permission::factory()->count(10)->create();
    $permissions = Permission::factory()->count(10)->create();
    $this->user->permissions()->sync($permissions->pluck('id')->toArray());

    getJson(route('users.permissions', $this->user->id), [
        'Authorization' => "Bearer {$this->token}"
    ])
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'description']
            ]
        ])
        ->assertJsonCount(10, 'data');
});

test('should sync permissions of user', function () {
    assertDatabaseCount('permissions',0);

    $arrayPermissions = Permission::factory()->count(10)->create()->pluck('id')->toArray();

    postJson(route('users.permissions.sync',$this->user->id), [
        'permissions' => $arrayPermissions
    ], [
        'Authorization' => "Bearer {$this->token}"
    ])->assertOk();
    /**Verifica quantas permissions tem na tabela */
    assertDatabaseCount('permissions',10);    
});

test('should validate sync permissions', function () { 
    postJson(route('users.permissions.sync',$this->user->id), [
        'permissions' => ['fake_id_permission']
    ], [
        'Authorization' => "Bearer {$this->token}"
    ])
    ->assertUnprocessable();  
});
