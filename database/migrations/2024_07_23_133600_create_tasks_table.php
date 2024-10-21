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
            $table->integer('developer_id');
            $table->integer('request_type_id');
            $table->integer('request_volume_id'); // num pages
            $table->integer('request_sla_id'); // auto populate based on selected request type and num pages
            $table->boolean('sla_missed')->default(0);
            $table->longText('sla_miss_reason')->nullable();
            $table->string('time_taken')->nullable(); // time elapsed
            $table->integer('qc_rounds')->nullable();
            $table->longText('salesforce_link');
            $table->boolean('special_request');
            $table->longText('comments_special_request'); // comments for special request
            $table->longText('addon_comments'); // addon comments

            // submit details
            $table->boolean('template_followed')->nullable();
            $table->boolean('template_issue')->nullable();
            $table->longText('comments_template_issue')->nullable(); // comments for issue in template
            $table->boolean('auto_recommend')->nullable();
            $table->longText('comments_auto_recommend')->nullable(); // comments for automation recommendation
            $table->boolean('img_localstock')->nullable(); // from localstock
            $table->boolean('img_customer')->nullable(); // provided by customer
            $table->string('img_num')->default(0); // images used
            $table->string('shared_folder_location')->nullable();
            $table->longText('dev_comments')->nullable(); // developer comments
            $table->string('internal_quality')->nullable();
            $table->string('external_quality')->nullable();

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
