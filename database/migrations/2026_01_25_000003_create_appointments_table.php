<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('health_service_id')->nullable()->constrained('health_services')->nullOnDelete();
            $table->foreignId('appointment_slot_id')->nullable()->constrained('appointment_slots')->nullOnDelete();
            $table->dateTime('scheduled_at');
            $table->string('status')->default('pending')->index();
            $table->text('notes')->nullable();
            $table->unsignedInteger('queue_number')->nullable();
            $table->string('check_in_token')->unique();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('reminder_day_sent_at')->nullable();
            $table->timestamp('reminder_hour_sent_at')->nullable();
            $table->timestamps();
            $table->index(['doctor_id','scheduled_at']);
            $table->index(['patient_id','scheduled_at']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};