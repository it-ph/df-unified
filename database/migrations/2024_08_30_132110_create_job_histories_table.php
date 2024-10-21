<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by');
            $table->integer('job_id');
            $table->integer('client_id');
            $table->string('activity');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->index('created_by');
            $table->index('job_id');
            $table->index('client_id');
            $table->index('deleted_at');

            $table->index(['created_by', 'job_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_histories');
    }
}
