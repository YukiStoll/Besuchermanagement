<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvanceRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_registrations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId');
            $table->integer('canteenId');
            $table->integer('accessAuthorizationId');
            $table->integer('parkingManagementId');
            $table->date('startDate');
            $table->date('endDate');
            $table->boolean('journeySketch');
            $table->enum('contactPossibility', ['E-Mail', 'SMS', 'Telefon']);
            $table->date('delete');
            $table->text('vehicleRegistrationNumber');
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
        Schema::dropIfExists('advance_registrations');
    }
}
