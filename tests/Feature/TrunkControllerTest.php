<?php

namespace Tests\Feature;

use App\Models\SipTrunk;
use App\Models\User;
use App\Services\TwilioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TrunkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testTrunksIndexPageRenders()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('trunks.index'));
        $response->assertStatus(200);
        $response->assertViewIs('trunks.index');
    }

    public function testTrunksCreatePageRenders()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('trunks.create'));
        $response->assertStatus(200);
        $response->assertViewIs('trunks.create');
    }

    public function testStoreSipTrunk()
    {
        $mockService = Mockery::mock(TwilioService::class);
        $mockService->shouldReceive('createSipTrunk')
            ->with('Test Trunk', 'test.sip.twilio.com')
            ->once()
            ->andReturn((object) ['sid' => 'TK123']);
        $mockService->shouldReceive('updateSipTrunk')
            ->with('TK123', 'sip:pbx.test.com', 'always')
            ->once()
            ->andReturn((object) ['sid' => 'TK123']);
        // If testing credentials, add POST data and mock createCredentialList, etc.

        $this->app->instance(TwilioService::class, $mockService);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('trunks.store'), [
            'friendly_name' => 'Test Trunk',
            'domain_name' => 'test.sip.twilio.com',
            'origination_uri' => 'sip:pbx.test.com',
        ]);

        $response->assertRedirect(route('trunks.index'));
        $this->assertDatabaseHas('sip_trunks', [
            'twilio_sid' => 'TK123',
            'friendly_name' => 'Test Trunk',
        ]);
    }
}