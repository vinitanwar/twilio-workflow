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

    public function testGenerateSensitiveInputTwiml()
    {
        $mockGather = Mockery::mock(\Twilio\TwiML\Voice\Gather::class);
        $mockGather->shouldReceive('say')
            ->with('Please enter your 9 digit number.')
            ->once()
            ->andReturn(new \Twilio\TwiML\Voice\Say('Please enter your 9 digit number.')); // ✅ Correct type

        $mockTwiml = Mockery::mock('overload:Twilio\TwiML\VoiceResponse');
        $mockTwiml->shouldReceive('pause')
            ->with(['length' => 2])
            ->once()
            ->andReturnSelf();

        $mockTwiml->shouldReceive('gather')
            ->with([
                'numDigits' => 9,
                'action' => 'http://example.com/resume',
                'method' => 'POST',
                'pciMode' => 'enable',
            ])
            ->once()
            ->andReturn($mockGather);

        $twilioService = new TwilioService();
        $result = $twilioService->generateSensitiveInputTwiml('http://example.com/resume');

        $this->assertInstanceOf(\Twilio\TwiML\VoiceResponse::class, $result);
    }


}