<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Database\Factories\Data\TextsFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->afterMaking(function (Model $comment): void {
            /** @var Post $post */
            $post = Post::query()->findOrFail($comment->getAttribute('post_id'));

            $rating = $post->rating ?? 3.0;

            $text = $rating > 2.5
                ? $this->faker->randomElement(
                    $this->faker->boolean(70)
                        ? TextsFactory::COMMENT_AGREE_POSITIVE
                        : TextsFactory::COMMENT_DISAGREE_POSITIVE
                )
                : $this->faker->randomElement(
                    $this->faker->boolean(70)
                        ? TextsFactory::COMMENT_AGREE_NEGATIVE
                        : TextsFactory::COMMENT_DISAGREE_NEGATIVE
                );

            $comment->fill(['text' => $text]);
        });
    }
}
