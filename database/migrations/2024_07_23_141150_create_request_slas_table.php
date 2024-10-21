<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestSlasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_slas', function (Blueprint $table) {
            $table->id();
            $table->integer('request_type_id');
            $table->integer('request_volume_id');
            $table->string('agreed_sla'); // E.g 8
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->string('status')->default('active');
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
        Schema::dropIfExists('request_slas');
    }
}
