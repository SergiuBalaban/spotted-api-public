<?php

use App\Models\AuthSms;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auth_sms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('phone')->unique();
            $table->string('phone_prefix')->default(AuthSms::ROMANIA_COUNTRY_CODE);
            $table->integer('sms_code')->nullable();
            $table->integer('sms_attempts')->default(1);
            $table->timestamp('sms_created_at')->nullable();
            $table->timestamp('sms_expired_at')->nullable();
            $table->timestamp('sms_blocked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_sms');
    }
};
