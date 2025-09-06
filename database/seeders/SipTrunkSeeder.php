<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SipTrunk;

class SipTrunkSeeder extends Seeder
{
    public function run()
    {
        SipTrunk::create([
            'twilio_sid' => 'TKtest123',
            'friendly_name' => 'Test Trunk',
            'domain_name' => 'test.sip.twilio.com',
            'origination_uri' => 'sip:pbx.test.com',
        ]);
    }
}