<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('call_recordings', function (Blueprint $table) {
            $table->id();
            $table->string('twilio_sid')->unique();
            $table->string('call_sid');
            $table->string('recording_url')->nullable();  // Redacted URL
            $table->boolean('is_redacted')->default(false);
            $table->foreignId('sip_trunk_id')->constrained('sip_trunks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_recordings');
    }
};
