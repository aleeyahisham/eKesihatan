<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->unique()->constrained('appointments')->cascadeOnDelete();
            $table->date('issued_on');
            $table->unsignedInteger('number');
            $table->timestamps();
            $table->unique(['issued_on','number']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('queue_tickets');
    }
};