<?php

namespace Tests\Feature;

use App\Helpers\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesApplication;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;
    use CreatesApplication;

    protected $support;
    protected $acc_mngr;

    /**
     * This method is called before
     * any test of TestCase class executed
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->support = User::factory()->hasCompanies(3)->create([
            'roles' => array(UserRole::ROLE_SUPPORT),
        ]);

        Company::factory()
            ->count(3)
            ->hasAttached($this->support)
            ->create();

        $this->acc_mngr = User::factory()->create([
            'roles' => array(UserRole::ROLE_ACCOUNT_MANAGER),
        ]);

        Company::factory()
            ->count(3)
            ->hasAttached($this->acc_mngr)
            ->create();
    }

    /**
     * A basic feature test example.
     * @return void
     */
    public function test_company_dashboard()
    {
        //redirect to login if not authenticated
        $response = $this->get(action('Web\CompanyController@dashboard'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        //Support and up is allowed 
        $response = $this->actingAs($this->support)->get(action('Web\CompanyController@dashboard'));
        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     * @return void
     */
    public function test_company_index_unauthenticate()
    {
        $response = $this->get(action('Web\CompanyController@index'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * A basic feature test example.
     * @return void
     */
    public function test_company_index_support()
    {
        //support is allowed to see index
        $support_response = $this->actingAs($this->support)->get(action('Web\CompanyController@index'));
        $support_response->assertStatus(200);
        return $support_response;
    }


    /**
     * A basic feature test example.
     * @return void
     */
    public function test_company_index_acc_mngr()
    {
        //Account Manager is allowed to see index
        $acc_mngr_response = $this->actingAs($this->acc_mngr)->get(action('Web\CompanyController@index'));
        $acc_mngr_response->assertStatus(200);
        return $acc_mngr_response;
    }

    /**
     * A basic feature test example.
     * @depends test_company_index_support
     * @return void
     */
    public function test_company_create_support($support_response)
    {
        $support_response->assertDontSee('Create a new Company');
    }


    /**
     * A basic feature test example.
     * @depends test_company_index_acc_mngr
     * @return void
     */
    public function test_company_create_acc_mngr($acc_mngr_response)
    {
        $companiesCount = Company::all()->count();
        $acc_mngr_response->assertSee('Create a new Company');

         //Account Manager is allowed to edit companies
         $response = $this->actingAs($this->acc_mngr)->call('POST', action('Web\CompanyController@store'), []);
         $response->assertSessionHasErrors(['name', 'city', 'country']);
        
     
         //too less digits
         $response = $this->actingAs($this->acc_mngr)->call(
             'POST',
             action('Web\CompanyController@store'),
             [
                 'name' => 'n',
                 'city' => 'c',
                 'country' => 'c'
             ]
         );
         $response->assertSessionHasErrors(['city', 'country', 'name']);
         $response->assertSessionHasErrors([
             'name' => 'The name must be at least 3 characters.',
             'city' => 'The city must be at least 3 characters.',
             'country' => 'The country must be at least 3 characters.',
         ]);
 
         //too much digits
         $response = $this->actingAs($this->acc_mngr)->call(
             'POST',
             action('Web\CompanyController@store'),
             [
                 'name' => 'abcdefghijklmnopqrstu',  //21 digits
                 'city' => 'abcdefghijklmnopqrstu',  //21 digits
                 'country' => 'abcdefghijklmnopqrstu',  //21 digits
             ]
         );
         $response->assertSessionHasErrors(['city', 'country', 'name']);
         $response->assertSessionHasErrors([
             'name' => 'The name may not be greater than 20 characters.',
             'city' => 'The city may not be greater than 20 characters.',
             'country' => 'The country may not be greater than 20 characters.',
         ]);
 
         $response = $this->actingAs($this->acc_mngr)->call(
             'POST',
             action('Web\CompanyController@store'),
             [
                 'name' => 'newName',
                 'city' => 'newCity',
                 'country' => 'newCountry'
             ]
         );
         $response->assertSessionHasNoErrors();
         $response->assertSessionHas('message', 'Node "newName" created');
         $this->assertTrue($companiesCount + 1 == Company::all()->count());

    }

    /**
     * A basic feature test example.
     * @depends test_company_index_support
     * @return void
     */
    public function test_company_edit_support($support_response)
    {
        $companies = $this->support->companies;
        foreach ($companies as $key => $company) {
            //no Edit-Button 
            $support_response->assertDontSee(action('Web\CompanyController@edit', ['company' => $company->id]));

            //redirect if not allowed to see edit route
            $edit_response = $this->actingAs($this->support)->get(action('Web\CompanyController@edit', ['company' => $company->id]));
            $edit_response->assertStatus(302);
            $edit_response->assertRedirect(action('Web\CompanyController@index'));
        }
    }

    /**
     * A basic feature test example.
     * @depends test_company_index_acc_mngr
     * @return void
     */
    public function test_company_edit_acc_mngr($acc_mngr_response)
    {
        $companies = $this->acc_mngr->companies;
        //Account Manager is allowed to edit companies
        foreach ($companies as $key => $company) {
            //Edit-Button is visible
            $acc_mngr_response->assertSee(action('Web\CompanyController@edit', ['company' => $company->id]));

            //is allowed to see route for editing companies
            $edit_response = $this->actingAs($this->acc_mngr)->get(action('Web\CompanyController@edit', ['company' => $company->id]));
            $edit_response->assertStatus(200);
        }
    }

    /**
     * A basic feature test example.
     * @depends test_company_index_support
     * @return void
     */
    public function test_company_delete_support($support_response)
    {
        //Delete is not allowed to be possible for Suppoprt
        $support_response->assertDontSee('confirmDelete');
        $companies = $this->support->companies;
        foreach ($companies as $key => $company) {
            //redirect if not allowed to see delete route
            $edit_response = $this->actingAs($this->support)->get(action('Web\CompanyController@destroy', ['company' => $company->id]));
            $edit_response->assertStatus(405);
        }
    }


    /**
     * A basic feature test example.
     * @depends test_company_index_acc_mngr
     * @return void
     */
    public function test_company_delete_acc_mngr($acc_mngr_response)
    {
        $countCompanies = Company::all()->count();
        //is delete possible in View
        $acc_mngr_response->assertSee('confirmDelete');
        //Account Manager is allowed to edit companies
        $companies = $this->acc_mngr->companies;
        foreach ($companies as $key => $company) {
            //delete-Button is visible
            $acc_mngr_response->assertSee(action('Web\CompanyController@destroy', ['company' => $company->id]));

            //redirect if not allowed to see delete route
            $destroy_response = $this->actingAs($this->acc_mngr)->delete(action('Web\CompanyController@destroy', ['company' => $company->id]));
            $destroy_response->assertNoContent($status = 204);

            //test if its really deleted
            $this->assertTrue(Company::find($company->id) == null);
            $countRemainingCompanies = Company::all()->count();
            $this->assertTrue($countCompanies == $countRemainingCompanies + 1);
            $countCompanies = $countRemainingCompanies;
        }
    }

    /**
     * A basic feature test example.
     * @depends test_company_index_acc_mngr
     * @depends test_company_index_support
     * @return void
     */
    public function test_company_update_support()
    {
        //Support is not allowed
        $company = $this->support->companies->first();
        $response = $this->actingAs($this->support)->call('PATCH', action('Web\CompanyController@update', ['company' => $company->id]), []);

        //redirect with errormessage in default bag without key
        $response->assertStatus(302);
        $response->assertRedirect(action('Web\CompanyController@index'));
        $this->assertTrue(session('errors')->first() == 'You are not allowed to edit companies.');
    }

    /**
     * A basic feature test example.
     * @depends test_company_index_acc_mngr
     * @depends test_company_index_support
     * @return void
     */
    public function test_company_update_acc_mngr()
    {
        //Account Manager is allowed to edit companies
        $company = $this->acc_mngr->companies->first();
        $response = $this->actingAs($this->acc_mngr)->call('PATCH', action('Web\CompanyController@update', ['company' => $company->id]), []);
        $response->assertSessionHasErrors(['name', 'city', 'country']);

        //too less digits
        $response = $this->actingAs($this->acc_mngr)->call(
            'PATCH',
            action('Web\CompanyController@update', ['company' => $company->id]),
            [
                'name' => 'n',
                'city' => 'c',
                'country' => 'c'
            ]
        );
        $response->assertSessionHasErrors(['city', 'country', 'name']);
        $response->assertSessionHasErrors([
            'name' => 'The name must be at least 3 characters.',
            'city' => 'The city must be at least 3 characters.',
            'country' => 'The country must be at least 3 characters.',
        ]);

        //too much digits
        $response = $this->actingAs($this->acc_mngr)->call(
            'PATCH',
            action('Web\CompanyController@update', ['company' => $company->id]),
            [
                'name' => 'abcdefghijklmnopqrstu',  //21 digits
                'city' => 'abcdefghijklmnopqrstu',  //21 digits
                'country' => 'abcdefghijklmnopqrstu',  //21 digits
            ]
        );
        $response->assertSessionHasErrors(['city', 'country', 'name']);
        $response->assertSessionHasErrors([
            'name' => 'The name may not be greater than 20 characters.',
            'city' => 'The city may not be greater than 20 characters.',
            'country' => 'The country may not be greater than 20 characters.',
        ]);

        $response = $this->actingAs($this->acc_mngr)->call(
            'PATCH',
            action('Web\CompanyController@update', ['company' => $company->id]),
            [
                'name' => 'newName',
                'city' => 'newCity',
                'country' => 'newCountry'
            ]
        );
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('message', 'Company Updated');

        //test if it fits with database data
        $reloadCompany = Company::findorfail($company->id);
        $this->assertTrue($reloadCompany->name == 'newName');
        $this->assertTrue($reloadCompany->city == 'newCity');
        $this->assertTrue($reloadCompany->country == 'newCountry');
    }
}
