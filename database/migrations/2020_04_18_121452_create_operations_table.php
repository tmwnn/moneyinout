<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->comment('Дата');
            $table->integer('summ')->comment('Сумма');
            $table->string('comment')->comment('Комментарий');
            $table->string('search')->comment('Поисковая строка');
            $table->bigInteger('category_id')->unsigned()->comment('Ид категории');
            $table->bigInteger('user_id')->unsigned()->comment('Ид пользователя');
            $table->integer('type')->unsigned()->default(0)->comment('Тип');
            $table->string('tags')->comment('Тэги');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incomes');
    }
}
