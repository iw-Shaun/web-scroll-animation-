<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('password');

            $table->timestamp('loggedin_at')->nullable();
            $table->timestamp('password_updated_at')->nullable();
            $table->boolean('active')->default(true);

            // Verification code
            $table->string('verification_code')->nullable();
            $table->timestamp('verification_code_generated_at')->nullable();
            $table->integer('verification_code_failed_times')->default(0);

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
        Schema::dropIfExists('admins');
    }
}
