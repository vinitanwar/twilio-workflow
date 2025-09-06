<?php

namespace App\Http\Controllers;

use App\Services\TwilioService;
use App\Models\ByocTrunk;
use App\Models\SipTrunk;
use Illuminate\Http\Request;

class ByocController extends Controller
{
    protected $twilio;

    public function __construct(TwilioService $twilio)
    {
        $this->twilio = $twilio;
    }

    public function create($sipTrunkId)
    {
        $sipTrunk = SipTrunk::findOrFail($sipTrunkId);
        return view('byoc.create', compact('sipTrunk'));
    }

    public function store(Request $request, $sipTrunkId)
    {
        $request->validate([
            'friendly_name' => 'required',
            'sip_target_uri' => 'required',
        ]);

        $sipTrunk = SipTrunk::findOrFail($sipTrunkId);

        try {
            // Create BYOC Trunk
            $byoc = $this->twilio->createByocTrunk($request->friendly_name);

            // Create Connection Policy
            $policy = $this->twilio->createConnectionPolicy($request->friendly_name . ' Policy');

            // Assign policy to BYOC
            $this->twilio->assignConnectionPolicyToByoc($byoc->sid, $policy->sid);

            // Add target (carrier SIP)
            $this->twilio->addTargetToPolicy($policy->sid, $request->friendly_name . ' Target', $request->sip_target_uri);

            // Update SIP Trunk with voice URL (use ngrok or production URL)
            $voiceUrl = url('/twilio/byoc-dial/' . $byoc->sid);  // Dynamic per BYOC
            $this->twilio->updateSipTrunkForByoc($sipTrunk->twilio_sid, $voiceUrl);

            // Store in DB
            ByocTrunk::create([
                'twilio_sid' => $byoc->sid,
                'friendly_name' => $request->friendly_name,
                'connection_policy_sid' => $policy->sid,
                'sip_target_uri' => $request->sip_target_uri,
                'sip_trunk_id' => $sipTrunk->id,
            ]);

            return redirect()->route('trunks.index')->with('success', 'BYOC connected successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}