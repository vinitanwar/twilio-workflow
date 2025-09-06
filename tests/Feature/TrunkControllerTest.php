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
        $mockTrunk = Mockery::mock();
        $mockTrunk->shouldReceive('update')
            ->with([
                'originationUri' => 'sip:pbx.test.com',
                'recording' => ['mode' => 'always', 'trim' => 'trim-silence'],
            ])
            ->once()
            ->andReturn((object)['sid' => 'TK123']);

        $mockTrunking = Mockery::mock();
        $mockTrunking->v1 = Mockery::mock();
        $mockTrunking->v1->trunks = Mockery::mock();
        $mockTrunking->v1->trunks->shouldReceive('create')
            ->with(['friendlyName' => 'Test Trunk', 'domainName' => 'test.sip.twilio.com'])
            ->once()
            ->andReturn((object)['sid' => 'TK123']);
        $mockTrunking->v1->trunks->shouldReceive('__invoke')
            ->with('TK123')
            ->andReturn($mockTrunk);

        $mockClient = Mockery::mock(\Twilio\Rest\Client::class, function (MockInterface $mock) use ($mockTrunking) {
            $mock->shouldReceive('getAccountSid')->andReturn('AC123');
            $mock->trunking = $mockTrunking;
        });

        $mockService = Mockery::mock(TwilioService::class, function (MockInterface $mock) use ($mockClient, $mockTrunk) {
            $mock->shouldReceive('__construct')->with($mockClient)->once();
            $mock->shouldReceive('createSipTrunk')
                ->with('Test Trunk', 'test.sip.twilio.com')
                ->once()
                ->andReturn((object)['sid' => 'TK123']);
            $mock->shouldReceive('updateSipTrunk')
                ->with('TK123', 'sip:pbx.test.com', 'always')
                ->once()
                ->andReturn((object)['sid' => 'TK123']);
        })->makePartial();

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