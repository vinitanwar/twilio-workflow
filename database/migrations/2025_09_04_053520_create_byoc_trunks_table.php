<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('byoc_trunks', function (Blueprint $table) {
        $table->id();
        $table->string('twilio_sid')->unique();
        $table->string('friendly_name');
        $table->string('connection_policy_sid')->nullable();
        $table->string('sip_target_uri');  // Carrier SIP
        $table->foreignId('sip_trunk_id')->constrained('sip_trunks')->onDelete('cascade');  // Link to existing SIP trunk
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('byoc_trunks');
    }
};
