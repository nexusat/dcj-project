<?php

use App\Imports\AmnestyImport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmnestiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amnesties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('limited')->nullable();
            $table->string('unconditional')->nullable();
        });

        Excel::import(new AmnestyImport, 'public/dcj.xlsx');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amnesties');
    }
}
