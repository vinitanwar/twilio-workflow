<?php

namespace App\Services;

use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse as Response;



class TwilioService
{
    protected $client;

    public function __construct(Client $client = null)
    {
        $this->client = $client ?: new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
    }


    public function sendTestSms($to, $message)
    {
        return $this->client->messages->create($to, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            'body' => $message,
        ]);
    }


    public function makeTestCall($to, $url)
    {
        return $this->client->calls->create(
            $to,
            config('services.twilio.phone_number'),
            ['url' => $url]
        );
    }


    public function generateTestTwiml()
    {
        $response = new Response();
        $response->say('Hello from Laravel Twilio integration!');
        $response->play('https://demo.twilio.com/docs/classic.mp3');  // Optional audio
        return $response;
    }

    public function listTrunks()
    {
        return $this->client->trunking->v1->trunks->read();
    }

    public function createCredentialList($friendlyName)
    {
        return $this->client->sip->credentialLists->create($friendlyName);
    }


    public function addCredentialsToList($credentialListSid, $username, $password)
    {
        return $this->client->sip->credentialLists($credentialListSid)->credentials->create($username, $password);
    }

    public function assignCredentialListToTrunk($trunkSid, $credentialListSid)
    {
        return $this->client->trunking->v1->trunks($trunkSid)->credentialLists->create($credentialListSid);
    }



    public function createByocTrunk($friendlyName)
    {
        return $this->client->voice->v1->byocTrunks->create([
            'friendlyName' => $friendlyName,
            // Optional: 'voiceUrl' => '...', but we'll handle via SIP trunk
        ]);
    }

    // Create Connection Policy for the BYOC Trunk
    public function createConnectionPolicy($friendlyName)
    {
        return $this->client->voice->v1->connectionPolicies->create([
            'friendlyName' => $friendlyName,
        ]);
    }

    // Assign Connection Policy to BYOC Trunk
    public function assignConnectionPolicyToByoc($byocSid, $policySid)
    {
        $this->client->voice->v1->byocTrunks($byocSid)->update([
            'connectionPolicySid' => $policySid,
        ]);
    }

    // Add Target to Connection Policy (points to carrier SIP for outgoing)
    public function addTargetToPolicy($policySid, $friendlyName, $sipUri, $priority = 1, $weight = 1)
    {
        return $this->client->voice->v1->connectionPolicies($policySid)->targets->create([
            'friendlyName' => $friendlyName,
            'target' => $sipUri,  // e.g., 'sip:carrier.example.com:5060'
            'priority' => $priority,
            'weight' => $weight,
        ]);
    }

    // Generate TwiML for outgoing call routing via BYOC (called by webhook)
    public function generateByocDialTwiml($number, $byocSid)
    {
        $response = new Response();
        $dial = $response->dial(['trunk' => $byocSid]);  // Routes to BYOC Trunk
        $dial->number($number);  // The destination PSTN number
        return $response;
    }

    // Update SIP Trunk to use voice URL for Programmable Voice routing
    public function updateSipTrunkForByoc($trunkSid, $voiceUrl)
    {
        $this->client->trunking->v1->trunks($trunkSid)->update([
            'voiceUrl' => $voiceUrl,  // e.g., 'https://your-app.com/twilio/byoc-dial'
            'voiceMethod' => 'POST',
            // Ensure recording is still enabled if needed
        ]);
    }



    public function updateSipTrunk($trunkSid, $originationUri)
{
    try {
        // 1. Update friendly name only (trunks don't accept sipUrl directly)
        $this->client->trunking->v1->trunks($trunkSid)
            ->update([
                'friendlyName' => 'Updated SIP Trunk',
            ]);

        // 2. Add origination URI properly
        $this->client->trunking->v1->trunks($trunkSid)
            ->originationUrls
            ->update([
                'friendlyName' => 'Primary Origination',
                'sipUrl'       => $originationUri,
                'priority'     => 0,
                'weight'       => 10,
                'enabled'      => true,
            ]);

        return $this->client->trunking->v1->trunks($trunkSid)->fetch();
    } catch (TwilioException $e) {
        \Illuminate\Support\Facades\Log::error('Twilio API error: ' . $e->getMessage());
        throw new \Exception('Failed to update SIP trunk: ' . $e->getMessage());
    }
}


    // Generate TwiML to pause/resume recording during sensitive input
    public function generateSensitiveInputTwiml($resumeUrl)
    {
        $response = new Response();
        $response->pause(['length' => 2]);
        $gather = $response->gather([
            'numDigits' => 9,
            'action' => $resumeUrl,
            'method' => 'POST',
            'pciMode' => 'enable',  // Redacts sensitive inputs for PCI compliance
        ]);
        $gather->say('Please enter your 9 digit number.');
        return $response;
    }

    // Generate TwiML to resume recording after input
    public function generateResumeTwiml()
    {
        $response = new Response();
        $response->resumeRecording();
        $response->say('Thank you, recording resumed.');
        return $response;
    }


    public function getTranscription($transcriptionSid)
    {
        return $this->client->transcriptions($transcriptionSid)->fetch();
    }


    public function redactRecording($recordingSid)
    {
        $this->client->recordings($recordingSid)->update([
            'status' => 'deleted',
        ]);
    }



    public function createSipTrunk($friendlyName, $domainName)
    {
        try {
            return $this->client->trunking->v1->trunks->create([
                'friendlyName' => $friendlyName,
                'domainName' => $domainName,
            ]);
        } catch (TwilioException $e) {
            \Illuminate\Support\Facades\Log::error('Twilio API error: ' . $e->getMessage());
            throw new \Exception('Failed to create SIP trunk: ' . $e->getMessage());
        }
    }

}