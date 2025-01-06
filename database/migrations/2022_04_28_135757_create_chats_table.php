<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('reporter_id');
            $table->unsignedBigInteger('report_found_id');
            $table->unsignedBigInteger('report_missing_id');
            $table->boolean('active')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reporter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('report_found_id')->references('id')->on('reports')->onDelete('cascade');
            $table->foreign('report_missing_id')->references('id')->on('reports')->onDelete('cascade');
        });
    }
}
