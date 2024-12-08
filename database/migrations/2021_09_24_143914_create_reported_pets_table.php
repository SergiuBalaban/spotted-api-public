<?php

use App\Models\ReportedPet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportedPetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reported_pets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('pet_id')->nullable();
            $table->string('status')->default(ReportedPet::STATUS_REPORTED);
            $table->string('category');
            $table->double('latitude', 11, 8);
            $table->double('longitude', 11, 8);
            $table->string('country');
            $table->string('city');
            $table->string('formatted_address')->nullable();
            $table->string('address')->nullable();
            $table->string('dms_location')->nullable();
            $table->json('avatar')->nullable();
            $table->text('message');
            $table->json('details');
            $table->boolean('found_in_app')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pet_id')->references('id')->on('pets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reported_pets');
    }
}
