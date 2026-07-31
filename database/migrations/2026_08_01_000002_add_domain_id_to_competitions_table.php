<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->foreignId('domain_id')
                ->nullable()
                ->after('sport_id')
                ->constrained('competition_domains')
                ->nullOnDelete();
        });

        $sportsDomainId = DB::table('competition_domains')->where('slug', 'sports')->value('id');

        if ($sportsDomainId !== null) {
            DB::table('competitions')->whereNull('domain_id')->update(['domain_id' => $sportsDomainId]);
        }
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('domain_id');
        });
    }
};
