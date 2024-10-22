<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('Not Started');
            $table->string('site_id');
            $table->string('platform'); // duda or wordpress
            $table->integer('client_id'); // auto populate based on user client
            $table->integer('supervisor_id');
            $table->integer('developer_id')->nullable();
            $table->integer('request_type_id');
            $table->integer('request_volume_id'); // num pages
            $table->integer('request_sla_id'); // auto populate based on selected request type and num pages
            $table->boolean('sla_missed')->default(0);
            $table->longText('sla_miss_reason')->nullable();
            $table->string('time_taken')->nullable(); // time elapsed
            $table->integer('qc_rounds')->nullable();
            $table->longText('addon_comments'); // addon comments

            // submit details
            $table->boolean('template_followed')->nullable();
            $table->boolean('template_issue')->nullable();
            $table->integer('pages')->nullable();
            $table->longText('dev_comments')->nullable(); // developer comments
            $table->string('internal_quality')->nullable();
            $table->string('external_quality')->nullable();

            // qc submission
            $table->longText('preview_link')->nullable();
            $table->boolean('self_qc')->nullable();
            $table->string('points')->nullable(); // designer can update

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
        Schema::dropIfExists('tasks');
    }
}
