<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('post_id'); //FK
            $table->unsignedBigInteger('user_id'); //FK
            $table->unsignedBigInteger('comment_id')->nullable(); //FK
            $table->string('title');
            $table->text('content');
            $table->tinyInteger('status')->default(\App\Contracts\Constant::STATUS_ACTIVATED);
            $table->timestamps();

            $table->index('post_id');
            $table->index('user_id');
            $table->index('comment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
