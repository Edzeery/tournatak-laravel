<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('competition_types', 'participant_type')) {
            Schema::table('competition_types', function (Blueprint $table) {
                $table->string('participant_type', 20)->default('team');
            });
        }

        if (DB::getDriverName() !== 'sqlite') {
            $this->safeDropForeign('registrations', 'competition_id');
            $this->safeDropForeign('registrations', 'team_id');
        }

        try {
            Schema::table('registrations', function (Blueprint $table) {
                $table->dropUnique('registrations_competition_id_team_id_unique');
            });
        } catch (Exception $e) {
            // index may not exist
        }

        if (! Schema::hasColumn('registrations', 'participant_type')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->string('participant_type', 20)->default('team')->after('competition_id');
            });
        }

        if (! Schema::hasColumn('registrations', 'user_id')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete()->after('team_id');
            });
        }

        if (! Schema::hasColumn('registrations', 'player_id')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->foreignId('player_id')->nullable()->constrained()->cascadeOnDelete()->after('user_id');
            });
        }

        if (DB::getDriverName() !== 'sqlite') {
            try {
                Schema::table('registrations', function (Blueprint $table) {
                    $table->unsignedBigInteger('team_id')->nullable()->change();
                });
            } catch (Exception $e) {
                // change() may not be supported
            }
        }

        if (DB::getDriverName() !== 'sqlite') {
            $this->safeAddForeign('registrations', 'competition_id', 'competitions');
            $this->safeAddForeign('registrations', 'team_id', 'teams');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            $this->safeDropForeign('registrations', 'team_id');
            $this->safeDropForeign('registrations', 'competition_id');
        }

        try {
            Schema::table('registrations', function (Blueprint $table) {
                $table->unique(['competition_id', 'team_id']);
            });
        } catch (Exception $e) {
            // may already exist
        }

        if (DB::getDriverName() !== 'sqlite') {
            try {
                Schema::table('registrations', function (Blueprint $table) {
                    $table->unsignedBigInteger('team_id')->nullable(false)->change();
                });
            } catch (Exception $e) {
                // change() may not be supported
            }
        }

        if (DB::getDriverName() !== 'sqlite') {
            $this->safeDropForeign('registrations', 'user_id');
            $this->safeDropForeign('registrations', 'player_id');
        }

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['participant_type', 'user_id', 'player_id']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            $this->safeAddForeign('registrations', 'competition_id', 'competitions');
            $this->safeAddForeign('registrations', 'team_id', 'teams');
        }

        if (Schema::hasColumn('competition_types', 'participant_type')) {
            Schema::table('competition_types', function (Blueprint $table) {
                $table->dropColumn('participant_type');
            });
        }
    }

    private function safeDropForeign(string $table, string $column): void
    {
        try {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$table}_{$column}_foreign");
        } catch (Exception $e) {
            // FK may not exist
        }
    }

    private function safeAddForeign(string $table, string $column, string $references): void
    {
        try {
            Schema::table($table, function (Blueprint $t) use ($column, $references) {
                $t->foreign($column)->references('id')->on($references)->cascadeOnDelete();
            });
        } catch (Exception $e) {
            // FK may already exist
        }
    }
};
