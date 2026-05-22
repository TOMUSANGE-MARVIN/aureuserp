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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->boolean('is_tenant')->default(false)->after('is_active');
            $table->timestamp('trial_ends_at')->nullable()->after('is_tenant');
            $table->timestamp('suspended_at')->nullable()->after('trial_ends_at');
            $table->string('suspension_reason')->nullable()->after('suspended_at');
            $table->string('subdomain')->nullable()->unique()->after('suspension_reason');
            $table->json('settings')->nullable()->after('subdomain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['slug', 'is_tenant', 'trial_ends_at', 'suspended_at', 'suspension_reason', 'subdomain', 'settings']);
        });
    }
};
