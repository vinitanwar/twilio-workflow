<?php

namespace Tests\Unit;

use App\Services\TwilioService;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TwilioServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateSipTrunk()
    {
        $mockTrunking = Mockery::mock();
        $mockTrunking->v1 = Mockery::mock();
        $mockTrunking->v1->trunks = Mockery::mock();
        $mockTrunking->v1->trunks->shouldReceive('create')
            ->with(['friendlyName' => 'Test Trunk', 'domainName' => 'test.sip.twilio.com'])
            ->once()
            ->andReturn((object) ['sid' => 'TK123']);

        $mockClient = Mockery::mock(\Twilio\Rest\Client::class, function (MockInterface $mock) use ($mockTrunking) {
            $mock->shouldReceive('getAccountSid')->andReturn('AC123');
            $mock->trunking = $mockTrunking;
        });

        $twilioService = new TwilioService($mockClient);
        $result = $twilioService->createSipTrunk('Test Trunk', 'test.sip.twilio.com');

        $this->assertEquals('TK123', $result->sid);
    }

    public function testMakeTestCall()
    {
        $mockApi = Mockery::mock();
        $mockApi->v2010 = Mockery::mock();
        $mockApi->v2010->account = Mockery::mock();
        $mockApi->v2010->account->calls = Mockery::mock();
        $mockApi->v2010->account->calls->shouldReceive('create')
            ->with('+1234567890', config('services.twilio.phone_number'), ['url' => 'http://example.com/twiml'])
            ->once()
            ->andReturn((object) ['sid' => 'CA123']);

        $mockClient = Mockery::mock(\Twilio\Rest\Client::class, function (MockInterface $mock) use ($mockApi) {
            $mock->shouldReceive('getAccountSid')->andReturn('AC123');
            $mock->api = $mockApi;
        });

        $twilioService = new TwilioService($mockClient);
        $result = $twilioService->makeTestCall('+1234567890', 'http://example.com/twiml');

        $this->assertEquals('CA123', $result->sid);
    }

    public function testGenerateSensitiveInputTwiml()
    {
        $mockTwiml = Mockery::mock('Twilio\Twiml');
        $mockTwiml->shouldReceive('pause')->with(['length' => 2])->once()->andReturnSelf();
        $mockTwiml->shouldReceive('gather')
            ->with([
                'numDigits' => 9,
                'action' => 'http://example.com/resume',
                'method' => 'POST',
            ])
            ->once()
            ->andReturnSelf();
        $mockTwiml->shouldReceive('say')
            ->with('Please enter your 9 digit number.')
            ->once()
            ->andReturnSelf();

        Mockery::mock('alias:Twilio\Twiml')->shouldReceive('__construct')->andReturn($mockTwiml);

        $twilioService = new TwilioService();
        $result = $twilioService->generateSensitiveInputTwiml('http://example.com/resume');

        $this->assertInstanceOf('Twilio\Twiml', $result);
    }
}