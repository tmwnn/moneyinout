<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('operation_id')->unsigned()->comment('Id операции');
            $table->bigInteger('tag_id')->unsigned()->comment('Id тэга');
            $table->timestamps();

            $table->foreign('operation_id')->references('id')->on('operations');
            $table->foreign('tag_id')->references('id')->on('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operations_tags');
    }
}
