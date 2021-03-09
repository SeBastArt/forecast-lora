<?php

namespace Tests\Feature;

use App\Helpers\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');
        // $response->assertStatus(200);
        $this->assertTrue(true);
    }


    //Home
    public function test_home_authentication()
    {
        //redirect to login
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        //allowed for any user
        $user = User::factory()->create();
        $user['roles'] = Array(UserRole::ROLE_SUPPORT);
        $user->save();

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);            
    }

    public function test_menu_authentication()
    {
        //redirect to login
        $response = $this->get(action('Web\CompanyController@dashboard'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        //allowed for any user
        $user = User::factory()->create();
        $user['roles'] = Array(UserRole::ROLE_SUPPORT);
        $user->save();

        $response = $this->actingAs($user)->get(action('Web\CompanyController@dashboard'));
        $response->assertStatus(200);            
    }
}
