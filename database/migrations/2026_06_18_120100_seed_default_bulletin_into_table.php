<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bulletins')) {
            return;
        }

        $exists = DB::table('bulletins')
            ->where('title', 'Kempen Derma Darah Perdana')
            ->exists();

        if ($exists) {
            return;
        }

        $posterPath = file_exists(public_path('images/kempen-derma-darah-poster.jpeg'))
            ? 'images/kempen-derma-darah-poster.jpeg'
            : 'images/kempen-derma-darah-poster.jpg';

        DB::table('bulletins')->insert([
            'title' => 'Kempen Derma Darah Perdana',
            'summary' => 'Join Unit Kesihatan UiTM Arau for a blood donation campaign and campus health engagement activities.',
            'details' => 'Activities include health exhibitions, health talks, and UiTM product sales. Open to UiTM community members and public participants who meet donation requirements.',
            'event_date' => '2026-06-16',
            'event_time' => '10:00 AM - 5:00 PM',
            'location' => 'Dewan Agung Tuanku Canselor (DATC), UiTM Shah Alam',
            'poster_path' => $posterPath,
            'is_published' => true,
            'created_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('bulletins')) {
            return;
        }

        DB::table('bulletins')
            ->where('title', 'Kempen Derma Darah Perdana')
            ->where('summary', 'Join Unit Kesihatan UiTM Arau for a blood donation campaign and campus health engagement activities.')
            ->where('event_time', '10:00 AM - 5:00 PM')
            ->delete();
    }
};
