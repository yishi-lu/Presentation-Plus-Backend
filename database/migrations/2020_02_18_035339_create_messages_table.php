<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->string('content');
            $table->string('contentType')->default(\App\Contracts\Constant::MESG_CONTENT_TEXT);;
            $table->tinyInteger('type')->default(\App\Contracts\Constant::MESG_TYPE_USER);
            $table->tinyInteger('status')->default(\App\Contracts\Constant::MESG_STATUS_UNREAD);
            $table->timestamps();

            $table->index('sender_id');
            $table->index('receiver_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
