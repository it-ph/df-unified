<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index('id');
            $table->index('job_id');
            $table->index('auditor_id');
            $table->index('client_id');
            $table->index('qc_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['id']);
            $table->dropIndex(['job_id']);
            $table->dropIndex(['auditor_id']);
            $table->dropIndex(['client_id']);
            $table->dropIndex(['qc_status']);
            $table->dropIndex(['created_at']);
        });
    }
}
