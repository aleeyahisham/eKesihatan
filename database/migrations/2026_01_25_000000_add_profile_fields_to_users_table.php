<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('patient')->index();
            $table->string('student_id')->nullable()->unique();
            $table->string('staff_id')->nullable()->unique();
            $table->string('phone_number')->nullable();
            $table->string('specialization')->nullable();
        });
    }
 
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role','student_id','staff_id','phone_number','specialization']);
        });
    }
};