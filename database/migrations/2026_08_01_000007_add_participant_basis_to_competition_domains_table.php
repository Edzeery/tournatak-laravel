<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('competition_domains', 'participant_basis')) {
            Schema::table('competition_domains', function (Blueprint $table) {
                $table->string('participant_basis', 20)->default('both')->after('evaluation_basis');
            });
        }

        $bases = [
            'sports' => 'both',
            'esports' => 'both',
            'academic' => 'individual',
            'hackathon' => 'both',
            'creative' => 'both',
        ];

        foreach ($bases as $slug => $basis) {
            DB::table('competition_domains')->where('slug', $slug)->update(['participant_basis' => $basis]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('competition_domains', 'participant_basis')) {
            Schema::table('competition_domains', function (Blueprint $table) {
                $table->dropColumn('participant_basis');
            });
        }
    }
};
