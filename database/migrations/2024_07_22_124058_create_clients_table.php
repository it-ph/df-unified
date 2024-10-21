<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sla_threshold')->nullable();
            $table->string('sla_threshold_to')->nullable();
            $table->string('sla_threshold_cc')->nullable();
            $table->string('sla_missed_to')->nullable();
            $table->string('sla_missed_cc')->nullable();
            $table->string('new_job_cc')->nullable();
            $table->string('qc_send_cc')->nullable();
            $table->string('daily_report_to')->nullable();
            $table->string('daily_report_cc')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
