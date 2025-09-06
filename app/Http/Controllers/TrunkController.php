<?php

namespace App\Http\Controllers;

use App\Services\TwilioService;
use App\Models\SipTrunk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrunkController extends Controller
{
    protected $twilio;

    public function __construct(TwilioService $twilio)
    {
        $this->twilio = $twilio;
    }

    public function index()
    {
        Log::info('TrunkController::index accessed');
        $trunks = SipTrunk::with('byocTrunk')->get();
        return view('trunks.index', compact('trunks'));
    }

    public function create()
    {
        Log::info('TrunkController::create accessed');
        return view('trunks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'friendly_name' => 'required',
            'domain_name' => 'required',
            'origination_uri' => 'required',
            'username' => 'nullable',
            'password' => 'nullable',
        ]);

        try {
            $trunk = $this->twilio->createSipTrunk($request->friendly_name, $request->domain_name);
            $this->twilio->updateSipTrunk($trunk->sid, $request->origination_uri, 'always');

            if ($request->username && $request->password) {
                $credList = $this->twilio->createCredentialList($request->friendly_name . ' Credentials');
                $this->twilio->addCredentialsToList($credList->sid, $request->username, $request->password);
                $this->twilio->assignCredentialListToTrunk($trunk->sid, $credList->sid);
            }

            SipTrunk::create([
                'twilio_sid' => $trunk->sid,
                'friendly_name' => $request->friendly_name,
                'domain_name' => $request->domain_name,
                'origination_uri' => $request->origination_uri,
                'credential_list_sid' => $credList->sid ?? null,
            ]);

            return redirect()->route('trunks.index')->with('success', 'Trunk created successfully.');
        } catch (\Exception $e) {
            Log::error('TrunkController::store error: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function recordings($sipTrunkId)
    {
        Log::info('TrunkController::recordings accessed', ['sipTrunkId' => $sipTrunkId]);
        $sipTrunk = SipTrunk::findOrFail($sipTrunkId);
        $recordings = \App\Models\CallRecording::where('sip_trunk_id', $sipTrunkId)->get();
        return view('trunks.recordings', compact('sipTrunk', 'recordings'));
    }
}