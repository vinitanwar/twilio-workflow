<?php

use App\Http\Controllers\ByocController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrunkController;
use App\Models\CallRecording;
use App\Models\SipTrunk;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});





Route::post('/twilio/voice', function () {
    $twilio = app(\App\Services\TwilioService::class);
    return response($twilio->generateTestTwiml(), 200)->header('Content-Type', 'text/xml');
});

Route::get('/trunks/{trunk}/recordings', [TrunkController::class, 'recordings'])
    ->name('trunks.recordings');



Route::get('/test-call', function () {
    $twilio = app(\App\Services\TwilioService::class);
    try {
        $call = $twilio->makeTestCall('+1234567890', 'https://your-ngrok-url/twilio/voice');
        return 'Call initiated: ' . $call->sid;
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});


Route::get('/test-create-trunk', function () {
    $twilio = app(\App\Services\TwilioService::class);
    try {
        $trunk = $twilio->createSipTrunk('Test SIP Trunk', 'testcompany.sip.twilio.com');
        return 'Trunk created: ' . $trunk->sid;
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
})->middleware('auth');


Route::get('/list-trunks', function () {
    $twilio = app(\App\Services\TwilioService::class);
    $trunks = $twilio->listTrunks();
    return response()->json($trunks);
});



Route::middleware('auth')->group(function () {
    Route::get('/trunks', [TrunkController::class, 'index'])->name('trunks.index');
    Route::get('/trunks/create', [TrunkController::class, 'create'])->name('trunks.create');
    Route::post('/trunks', [TrunkController::class, 'store'])->name('trunks.store');
    Route::get('/trunks/{sipTrunkId}/byoc/create', [ByocController::class, 'create'])->name('byoc.create');
    Route::post('/trunks/{sipTrunkId}/byoc', [ByocController::class, 'store'])->name('byoc.store');
});

Route::post('/twilio/byoc-dial/{byocSid}', function (Request $request, $byocSid) {
    $twilio = app(\App\Services\TwilioService::class);
    $number = $request->input('To');
    return response($twilio->generateByocDialTwiml($number, $byocSid), 200)->header('Content-Type', 'text/xml');
});

Route::post('/twilio/sensitive-input/{callSid}', function (Request $request, $callSid) {
    $twilio = app(\App\Services\TwilioService::class);
    $actionUrl = url('/twilio/resume-recording/' . $callSid);
    return response($twilio->generateSensitiveInputTwiml($actionUrl), 200)->header('Content-Type', 'text/xml');
});

Route::post('/twilio/resume-recording/{callSid}', function () {
    $twilio = app(\App\Services\TwilioService::class);
    return response($twilio->generateResumeTwiml(), 200)->header('Content-Type', 'text/xml');
});





Route::post('/twilio/recording-webhook', function (Request $request) {
    $twilio = app(\App\Services\TwilioService::class);
    $recordingSid = $request->input('RecordingSid');
    $callSid = $request->input('CallSid');
    $recordingUrl = $request->input('RecordingUrl');

    // Store recording metadata
    $recording = CallRecording::create([
        'twilio_sid' => $recordingSid,
        'call_sid' => $callSid,
        'recording_url' => $recordingUrl,
        'sip_trunk_id' => SipTrunk ::first()->id,  // Adjust based on call context
    ]);

    // Queue job for transcription and redaction
\App\Jobs\ProcessRecording::dispatch($recording->id);

    return response('', 204);  // Twilio expects empty 204 response
})->middleware('twilio.webhook');  // Add validator middleware later


require __DIR__ . '/auth.php';
