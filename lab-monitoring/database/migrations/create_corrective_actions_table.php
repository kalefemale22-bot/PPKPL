<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('corrective_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_ticket_id')->constrained('incident_tickets')->onDelete('cascade');
            $table->text('description');
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent(); // Hanya created_at sesuai prompt
        });
    }

    public function down() { Schema::dropIfExists('corrective_actions'); }
};