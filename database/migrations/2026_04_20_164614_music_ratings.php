<?php

declare(strict_types=1);

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
        Schema::create('music_ratings', function (Blueprint $table): void {
            $table->foreignUuid('music_id')->primary()->constrained('musics')->cascadeOnDelete();
            $table->decimal('rating', 3, 2)->default(0);
            $table->unsignedInteger('count_ratings')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('music_ratings');
    }
};
