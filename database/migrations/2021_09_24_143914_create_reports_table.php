<?php

use App\Models\Report;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('pet_id')->nullable();
            $table->string('status')->default(Report::STATUS_REPORTED);
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
            $table->boolean('found_in_app')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pet_id')->references('id')->on('pets')->onDelete('cascade');
        });
    }
}
