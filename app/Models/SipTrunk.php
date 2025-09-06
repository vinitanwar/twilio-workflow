<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SipTrunk extends Model
{
    use HasFactory;

    protected $fillable = ['twilio_sid', 'friendly_name', 'domain_name', 'origination_uri', 'credential_list_sid'];

    public function byocTrunk()
    {
        return $this->hasOne(ByocTrunk::class);
    }
}