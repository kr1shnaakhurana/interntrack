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
        Schema::table('job_sources', function (Blueprint $table) {
            $table->string('adapter_key')->nullable()->after('type');
            $table->string('auth_type')->default('none')->after('api_endpoint'); // none, api_key, bearer, oauth2
            $table->jsonb('config')->nullable()->after('auth_type');
            $table->jsonb('field_mapping')->nullable()->after('config');
            $table->timestamp('last_fetched_at')->nullable()->after('is_active');
            $table->string('last_fetch_status')->nullable()->after('last_fetched_at'); // success, failed, running
            $table->text('last_fetch_error')->nullable()->after('last_fetch_status');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->jsonb('metadata')->nullable()->after('source_job_id');
            $table->unique(['source_id', 'source_job_id'], 'jobs_source_id_source_job_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropUnique('jobs_source_id_source_job_id_unique');
            $table->dropColumn('metadata');
        });

        Schema::table('job_sources', function (Blueprint $table) {
            $table->dropColumn([
                'adapter_key',
                'auth_type',
                'config',
                'field_mapping',
                'last_fetched_at',
                'last_fetch_status',
                'last_fetch_error',
            ]);
        });
    }
};
