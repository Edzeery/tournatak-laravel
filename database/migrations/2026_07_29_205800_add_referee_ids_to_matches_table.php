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
        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('referee_id')->nullable()->constrained('referees')->nullOnDelete()->after('referee');
            $table->foreignId('assistant_referee_1_id')->nullable()->constrained('referees')->nullOnDelete()->after('assistant_referee_1');
            $table->foreignId('assistant_referee_2_id')->nullable()->constrained('referees')->nullOnDelete()->after('assistant_referee_2');
            $table->foreignId('fourth_official_id')->nullable()->constrained('referees')->nullOnDelete()->after('fourth_official');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['referee_id']);
            $table->dropForeign(['assistant_referee_1_id']);
            $table->dropForeign(['assistant_referee_2_id']);
            $table->dropForeign(['fourth_official_id']);
            $table->dropColumn(['referee_id', 'assistant_referee_1_id', 'assistant_referee_2_id', 'fourth_official_id']);
        });
    }
};
