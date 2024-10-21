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
            $table->string('qc_status')->nullable();
            $table->integer('for_rework')->nullable();
            $table->integer('num_times')->nullable();
            $table->integer('alignment_aesthetics')->nullable();
            $table->longText('c_alignment_aesthetics')->nullable();
            $table->integer('availability_formats')->nullable();
            $table->longText('c_availability_formats')->nullable();
            $table->integer('accuracy')->nullable();
            $table->longText('c_accuracy')->nullable();
            $table->integer('functionality')->nullable();
            $table->longText('c_functionality')->nullable();
            $table->longText('qc_comments')->nullable();

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
