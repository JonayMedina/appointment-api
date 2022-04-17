<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('second_phone')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('address')->nullable();
            $table->string('dni')->nullable();
            $table->string('rif_type')->nullable();
            $table->string('rif_num')->nullable();
            $table->date('birthday')->nullable();
            $table->text('avatar')->nullable();
            $table->string('security_question')->nullable();
            $table->string('security_response')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
