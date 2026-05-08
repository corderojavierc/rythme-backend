<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Database\Factories\Data\TextsFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

final class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => User::factory(),
            'text' => '',
            'count_likes' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function ($comment): void {
            $rating = $comment->post_id instanceof Post
                ? $comment->post_id->rating
                : Post::query()->find($comment->post_id)?->rating ?? 3.0;

            if ($rating > 2.5) {
                $comment->text = $this->faker->randomElement(
                    $this->faker->boolean(70)
                        ? TextsFactory::COMMENT_AGREE_POSITIVE
                        : TextsFactory::COMMENT_DISAGREE_POSITIVE
                );
            } else {
                $comment->text = $this->faker->randomElement(
                    $this->faker->boolean(70)
                        ? TextsFactory::COMMENT_AGREE_NEGATIVE
                        : TextsFactory::COMMENT_DISAGREE_NEGATIVE
                );
            }
        });
    }
}
