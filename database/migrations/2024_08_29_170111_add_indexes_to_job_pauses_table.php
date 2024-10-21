<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToJobPausesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_pauses', function (Blueprint $table) {
            $table->index('job_id');
            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_pauses', function (Blueprint $table) {
            $table->dropIndex(['job_id']);
            $table->dropIndex(['client_id']);
        });
    }
}
