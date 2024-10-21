<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToRequestSlasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_slas', function (Blueprint $table) {
            $table->index('id');
            $table->index('request_type_id');
            $table->index('request_volume_id');
            $table->index('agreed_sla');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_slas', function (Blueprint $table) {
            $table->dropIndex(['id']);
            $table->dropIndex(['request_type_id']);
            $table->dropIndex(['request_volume_id']);
            $table->dropIndex(['agreed_sla']);
        });
    }
}
