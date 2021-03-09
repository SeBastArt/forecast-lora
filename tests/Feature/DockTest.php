<?php

namespace Tests\Feature;

use App\Helpers\UserRole;
use App\Models\Gateway;
use App\Models\Node;
use App\Models\NodeData;
use App\Models\User;
use App\Services\CompanyService;
use App\Services\FacilityService;
use App\Services\NodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\CreatesApplication;
use Tests\TestCase;

define("JSON_INPUT", '{"app_id":"ia","dev_id":"decentlab","hardware_serial":"0004A30B001FDC0B","port":1,"counter":237,"payload_raw":"AgjKAAOBZQvO","payload_fields":{"Version":2,"deviceID":2250,"sensor0":3.022,"sensor1":22.3125,"sensorCount":2},"metadata":{"time":"2018-12-03T20:39:04.748804857Z","frequency":867.1,"modulation":"LORA","data_rate":"SF7BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-c0ee40ffff294870","gtw_trusted":true,"timestamp":1067417243,"time":"","channel":3,"rssi":-44,"snr":8.5,"rf_chain":0,"latitude":51.072018,"longitude":13.768493,"altitude":153,"location_source":"registry"}]},"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/ia/black_mesa?key=ttn-account-v2.HPgeEGfHNCIm3GMzY5shGebD_XNafL18ljnFaGNzZSk"}');
   
class DockTest extends TestCase
{
    use RefreshDatabase;
    use CreatesApplication;

    //Services
    protected $companyService;
    protected $facilityService;
    protected $nodeService;

    //need Adminrights in WebUI
    private User $admin;

      /**
     * This method is called before
     * any test of TestCase class executed
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        //need companyService, FacilityService and NodeService
        $this->companyService = app(CompanyService::class);
        $this->facilityService = app(FacilityService::class);
        $this->nodeService = app(NodeService::class);

        $this->admin = User::factory()->hasCompanies(3)->create([
            'roles' => array(UserRole::ROLE_ADMIN),
        ]);

        $companyCollection = collect( [
            'name' => 'newCompany',
            'city' => 'newCity',
            'country' => 'newCountry'
        ]);
        $newCompany = $this->companyService->createCompany($this->admin, $companyCollection);

        $facilityCollection = collect( [
            'name' => 'newFacility',
            'location' => 'newLocation'
        ]); 
        $newFacility = $this->facilityService->createFacility($newCompany, $facilityCollection);
      
        $nodeCollection = collect( [
            'name' => 'newNode',
            'dev_eui' => '0004A30B001FDC0B',
            'node_type_id' => 1,
        ]); 
        $newNode = $this->nodeService->createNode($newFacility, $nodeCollection);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_web_dock()
    {

        //Account Manager is allowed to edit companies
        $response = $this->actingAs($this->admin)->call('POST', action('Web\DockController@store'), []);
        $response->assertSessionHasErrors(['json']);
       
    
        //too less digits
        $response = $this->actingAs($this->admin)->call(
            'POST',
            action('Web\DockController@store'),
            [
                'json' => 'n',
            ]
        );
        $response->assertSessionHasErrors(['json']);
        $response->assertSessionHasErrors([
            'json' => 'The json must be at least 100 characters.',
        ]);

        //too much digits
        $response = $this->actingAs($this->admin)->call(
            'POST',
            action('Web\DockController@store'),
            [
                'json' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678900',  //1501 digits
            ]
        );
        $response->assertSessionHasErrors(['json']);
        $response->assertSessionHasErrors([
            'json' => 'The json may not be greater than 1500 characters.',
        ]);
            
        //before   
        $this->assertTrue(NodeData::all()->count() === 0);
        $this->assertTrue(Gateway::all()->count() === 0);

        $response = $this->actingAs($this->admin)->call(
            'POST',
            action('Web\DockController@store'),
            [
                'json' => JSON_INPUT,
            ]
        );
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status', 'Nodedata Created'); 

        //after
        $this->assertTrue(NodeData::all()->count() === 1);
        $this->assertTrue(Gateway::all()->count() === 1);
    }

/**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_api_dock()
    {
        //before
        $this->assertTrue(NodeData::all()->count() === 0);
        $this->assertTrue(Gateway::all()->count() === 0);

        $response = $this->json('POST', action('Api\DockApiController@dock'), json_decode(JSON_INPUT, true));
        $response
            ->assertStatus(201)
            ->assertExactJson([
                'message' => 'incoming data processed.',
            ]);

        //after
        $this->assertTrue(NodeData::all()->count() === 1);
        $this->assertTrue(Gateway::all()->count() === 1);
    }
}
