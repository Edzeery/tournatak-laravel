<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->string('venue')->nullable()->after('match_date');
            $table->string('weather')->nullable()->after('venue');
            $table->integer('attendance')->nullable()->after('weather');
            $table->string('referee')->nullable()->after('attendance');
            $table->string('assistant_referee_1')->nullable()->after('referee');
            $table->string('assistant_referee_2')->nullable()->after('assistant_referee_1');
            $table->string('fourth_official')->nullable()->after('assistant_referee_2');
            $table->integer('added_time_first_half')->default(0)->after('fourth_official');
            $table->integer('added_time_second_half')->default(0)->after('added_time_first_half');
            $table->text('match_notes')->nullable()->after('added_time_second_half');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn([
                'venue', 'weather', 'attendance', 'referee',
                'assistant_referee_1', 'assistant_referee_2', 'fourth_official',
                'added_time_first_half', 'added_time_second_half', 'match_notes',
            ]);
        });
    }
};
