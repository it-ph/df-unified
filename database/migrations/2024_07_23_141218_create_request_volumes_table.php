<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestVolumesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_volumes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // number of pages E.g 1, 2, 3, etc
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->string('status')->default('active');;
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
        Schema::dropIfExists('request_volumes');
    }
}
