<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('application_id');
            $table->string('video_id')->unique();
            $table->integer('upload_id')->nullable();
            
            $table->mediumText('title')->nullable();

            $table->string('original_video_filename');
            $table->string('original_video_mimetype')->nullable();
            
            $table->string('path');

            $table->timestamp('queued_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamp('failed_at')->nullable();
            $table->mediumText('fail_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
