<?php

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
        Schema::create('appusers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->json('app_name')->nullable();
            $table->string('store_url')->nullable();
            $table->string('store_name')->nullable();
            $table->string('plan_status')->nullable();
            $table->string('plan_name')->nullable();
            $table->decimal('plan_price', 10, 2)->nullable();
            $table->timestamp('plan_activation_time')->nullable();
            $table->boolean('account_owner')->default(false);
            $table->boolean('collaborator')->default(false);
            $table->boolean('email_verified')->default(false);
            $table->string('locale')->nullable();
            $table->string('last_session')->nullable();
            $table->timestamp('last_login_time')->nullable();
            $table->string('myshopify_store_url')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appusers');
    }
};
