<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('most_valorated_musics', function (Blueprint $table): void {
            $table->date('period');
            $table->unsignedTinyInteger('rank_position');
            $table->uuid('music_id');
            $table->foreign('music_id')->references('id')->on('musics')->cascadeOnDelete();
            $table->decimal('rating', 4, 2);
            $table->unsignedInteger('count_ratings');
            $table->primary(['period', 'rank_position']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('most_valorated_musics');
    }
};
