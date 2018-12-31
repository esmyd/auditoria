<?php

use Illuminate\Database\Migrations\Migration;

class AddSpecialRoleColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function ($table) {
            $table->enum('special', ['all-access', 'no-access','null'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function ($table) {
            $table->dropColumn('special');
        });
    }
}
