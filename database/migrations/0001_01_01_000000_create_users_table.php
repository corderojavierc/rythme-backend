<?php

declare(strict_types=1);

use App\Enums\UserTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('username')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('type')->default(UserTypeEnum::USER->value);
            $table->string('spotify_id')->nullable()->unique();
            $table->integer('followers')->default(0);
            $table->integer('following')->default(0);
            $table->integer('posts')->default(0);
            $table->integer('musics')->default(0);
            $table->string('password');
            $table->rememberToken();
            $table->string('profile_image')->default('https://api.dicebear.com/9.x/thumbs/svg?seed=username');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
};
