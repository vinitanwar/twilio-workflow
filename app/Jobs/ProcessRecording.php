<?php

namespace App\Jobs;

use App\Models\CallRecording;
use App\Services\TwilioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessRecording implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recordingId;

    public function __construct($recordingId)
    {
        $this->recordingId = $recordingId;
    }

    public function handle(TwilioService $twilio)
    {
        $recording = CallRecording::findOrFail($this->recordingId);

        // Request transcription
        $transcription = $twilio->client->recordings($recording->twilio_sid)->transcriptions->create();

        // Wait for transcription (poll in production; simplified here)
        sleep(5);  // Adjust for real-world timing
        $transcriptionData = $twilio->getTranscription($transcription->sid);

        // Basic regex for SSN (e.g., XXX-XX-XXXX)
        if (preg_match('/\d{3}-\d{2}-\d{4}/', $transcriptionData->transcriptionText)) {
            $twilio->redactRecording($recording->twilio_sid);
            $recording->update(['is_redacted' => true, 'recording_url' => null]);
        }
    }
}