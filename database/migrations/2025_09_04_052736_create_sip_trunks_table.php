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
    Schema::create('sip_trunks', function (Blueprint $table) {
        $table->id();
        $table->string('twilio_sid')->unique();
        $table->string('friendly_name');
        $table->string('domain_name');
        $table->string('origination_uri')->nullable();
        $table->string('credential_list_sid')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sip_trunks');
    }
};
