<?php

use App\Models\TrackedReportedPet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackedReportedPetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracked_reported_pets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reported_pet_id');
            $table->unsignedBigInteger('pet_id');
            $table->string('category');
            $table->boolean('is_identically')->default(0);
            $table->string('status')->default(TrackedReportedPet::STATUS_RESEMBLE);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('reported_pet_id')->references('id')->on('reported_pets')->onDelete('cascade');
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
        Schema::dropIfExists('tracked_reported_pets');
    }
}
