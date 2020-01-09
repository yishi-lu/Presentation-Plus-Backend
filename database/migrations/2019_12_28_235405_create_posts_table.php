<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id'); //FK
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_url');
            $table->text('content');
            $table->string('type');
            $table->tinyInteger('status')->default(\App\Contracts\Constant::STATUS_ACTIVATED);
            $table->tinyInteger('visibility');
            $table->integer('viewed');
            $table->integer('liked');
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
