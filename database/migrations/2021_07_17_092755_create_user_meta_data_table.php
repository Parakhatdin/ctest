<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMetaDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_meta_data', function (Blueprint $table) {
            $table->id();
            $table->string("fio");
            $table->string("birthday");
            $table->enum("gender", ["male", "female"]);
            $table->string("address");
            $table->string("phone_number");
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
        Schema::dropIfExists('user_meta_data');
    }
}
