<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkcenterHasEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workcenter_has_employees', function (Blueprint $table) {
            $table->unsignedInteger('workcenter_id');
            $table->unsignedInteger('employee_id');

            $table->foreign('workcenter_id')
                ->references('id')
                ->on('workcenters')
                ->onDelete('cascade');

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workcenter_has_employees');
    }
}
