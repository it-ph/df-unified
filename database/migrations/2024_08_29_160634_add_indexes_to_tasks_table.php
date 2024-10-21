<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('id');
            $table->index('name');
            $table->index('request_type_id');
            $table->index('request_volume_id');
            $table->index('request_sla_id');
            $table->index('developer_id');
            $table->index('client_id');
            $table->index('status');
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
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['id']);
            $table->dropIndex(['name']);
            $table->dropIndex(['request_type_id']);
            $table->dropIndex(['request_volume_id']);
            $table->dropIndex(['request_sla_id']);
            $table->dropIndex(['developer_id']);
            $table->dropIndex(['client_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
    }
}
