<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('musics', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('artist');
            $table->json('spotify_artist_ids')->nullable();
            $table->string('cover_url');
            $table->string('release_date');
            $table->unique(['title', 'artist']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('musics');
    }
};
