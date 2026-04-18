<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('incident_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condition_data_id')->constrained('condition_data')->onDelete('cascade');
            $table->enum('status', ['open', 'dalam_penanganan', 'closed'])->default('open');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down() { Schema::dropIfExists('incident_tickets'); }
};