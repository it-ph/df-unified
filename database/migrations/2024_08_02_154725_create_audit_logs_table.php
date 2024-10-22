<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id');

            // send for qc
            $table->longText('preview_link')->nullable();
            $table->boolean('self_qc')->nullable();
            $table->longText('dev_comments');

            // submit feedback
            $table->string('time_taken')->nullable();
            $table->integer('qc_round')->nullable();
            $table->integer('auditor_id')->nullable();
            $table->string('qc_status')->nullable(); // Pass, NFE, Fail
            $table->integer('for_rework')->nullable(); // if NFE or Fail then for_rework is set to 1
            $table->longText('qc_comments')->nullable();
            $table->string('points')->nullable(); // per QC round

            // dates
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();
            $table->integer('created_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
}
