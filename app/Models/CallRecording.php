<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallRecording extends Model
{
    use HasFactory;

    protected $fillable = ['twilio_sid', 'call_sid', 'recording_url', 'is_redacted', 'sip_trunk_id'];
}