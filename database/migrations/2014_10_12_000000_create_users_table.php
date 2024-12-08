<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->unique();
            $table->string('phone_prefix')->default(User::ROMANIA_COUNTRY_CODE);
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('invite_token')->nullable()->unique();
            $table->json('avatar')->nullable();
            $table->integer('sms_code')->nullable();
            $table->timestamp('sms_code_expiration')->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('admin')->default(0);
            $table->string('timezone')->nullable();
            $table->boolean('banned')->default(0);
            $table->smallInteger('banned_count')->default(0);
            $table->timestamp('banned_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
