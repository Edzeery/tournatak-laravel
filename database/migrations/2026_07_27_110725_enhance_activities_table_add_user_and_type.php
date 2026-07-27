<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('type')->nullable()->after('user_id');
            $table->string('event')->nullable()->after('type');
            $table->json('properties')->nullable()->after('event');
            $table->string('ip_address', 45)->nullable()->after('properties');
            $table->string('user_agent')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'type', 'event', 'properties', 'ip_address', 'user_agent']);
        });
    }
};
