<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumntsToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('email')->unique()->nullable();
            $table->string('citizenship')->nullable();
            $table->double('height',5,2)->nullable();
            $table->double('weight',5,2)->nullable();
            $table->enum('bloodType',['A','B','O','AB'])->nullable();
            $table->text('img')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('citizenship');
            $table->dropColumn('height');
            $table->dropColumn('weight');
            $table->dropColumn('bloodType');
            $table->dropColumn('img');
        });
    }
}
