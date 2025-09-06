<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ByocTrunk extends Model
{
    use HasFactory;

    protected $fillable = ['twilio_sid', 'friendly_name', 'connection_policy_sid', 'sip_target_uri', 'sip_trunk_id'];
}