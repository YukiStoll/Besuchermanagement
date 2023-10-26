<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->increments('id');
            $table->text('forename');
            $table->text('surname');
            $table->date('dateOfBirth');
            $table->enum('salutation', ['Herr', 'Frau']);
            $table->text('title');
            $table->text('email');
            $table->text('language');
            $table->text('citizenship');
            $table->text('visitorCategory');
            $table->text('company');
            $table->text('companyStreet');
            $table->text('companyCountry');
            $table->integer('companyZipCode');
            $table->text('companyCity');
            $table->integer('landlineNumber');
            $table->integer('mobileNumber');
            $table->text('confidentialityAgreement');
            $table->date('safetyInstruction');
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
        Schema::dropIfExists('visitors');
    }
}
