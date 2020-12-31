<?php

namespace Nelisys\Rbac\Tests\Feature;

use Mockery;
use Orchestra\Testbench\TestCase;

use Nelisys\Rbac\Models\User;
use Nelisys\Rbac\RbacServiceProvider;
use Nelisys\Rbac\Http\Controllers\LoginController;

class ApiLoginTest extends TestCase
{
    protected $username;

    public function tearDown(): void
    {
        Mockery::close();
    }

    protected function getPackageProviders($app)
    {
        return [
            RbacServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('nelisys.rbac.username', 'username');

        $this->username = config('nelisys.rbac.username');
    }

    /** @test */
    public function api_login_requires_fields()
    {
        $data = [
            $this->username => '',
            'password' => '',
        ];

        $this->json('POST', '/api/login', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    $this->username => [
                        "The {$this->username} field is required."
                    ],
                    'password' => [
                        'The password field is required.'
                    ],
                ],
            ]);
    }

    /** @test */
    public function api_login_requires_valid_username_and_password()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        $data = [
            $this->username => 'invalid',
            'password' => 'invalid',
        ];

        $this->json('POST', '/api/login', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    $this->username => [
                        'These credentials do not match our records.',
                    ],
                ],
            ]);
    }

    /** @test */
    public function api_too_many_invalid_login_cause_429_response()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        $data = [
            $this->username => 'invalid',
            'password' => 'invalid',
        ];

        // default : 6 attempts within 60 seconds
        $this->json('POST', '/api/login', $data)->assertStatus(422);
        $this->json('POST', '/api/login', $data)->assertStatus(422);
        $this->json('POST', '/api/login', $data)->assertStatus(422);
        $this->json('POST', '/api/login', $data)->assertStatus(422);
        $this->json('POST', '/api/login', $data)->assertStatus(422);

        $this->json('POST', '/api/login', $data)
            ->assertStatus(429)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    $this->username => [
                        'Too many login attempts. Please try again in 60 seconds.',
                    ],
                ],
            ]);

        // try again after 60 seconds
        $this->travel(61)->seconds();

        $this->json('POST', 'login', $data)
            ->assertStatus(422);
    }

    /** @test */
    public function api_inactive_user_cannot_login_even_with_valid_username_and_password()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $user = User::forceCreate([
            'username' => 'alice',
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => bcrypt('secret'),
            'is_active' => false,
        ]);

        $data = [
            $this->username => $user->{$this->username},
            'password' => 'secret',
        ];

        $this->json('POST', 'login', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    $this->username => [
                        'These credentials do not match our records.',
                    ],
                ],
            ]);
    }

    /** @test */
    public function api_user_can_login_with_valid_username_and_password()
    {
        $this->withoutExceptionHandling();

        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $user = User::forceCreate([
            'username' => 'alice',
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => bcrypt('secret'),
        ]);

        $data = [
            $this->username => $user->{$this->username},
            'password' => 'secret',
        ];

        // mock
        $mock = Mockery::mock(LoginController::class)
            ->makePartial();

        $mock->shouldReceive('createToken')
            ->andReturn('mock-token');

        $this->instance(LoginController::class, $mock);

        $this->json('POST', '/api/login', $data)
            ->assertStatus(200)
            ->assertJson([
                'token' => 'mock-token',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                ],
                'scopes' => ['*'],
            ]);
    }

    /** @test */
    public function api_logout_should_destroy_user_session()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        $user = User::forceCreate([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->actingAs($user);

        $this->json('POST', '/api/logout')
            ->assertStatus(204);

        $this->assertNull(auth()->user());
    }
}
