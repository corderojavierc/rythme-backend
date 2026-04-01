<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Likes\Schemas;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

final class LikeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'username')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => User::query()->where('name', 'like', sprintf('%%%s%%', $search))
                        ->orWhere('second_name', 'like', sprintf('%%%s%%', $search))
                        ->orWhere('username', 'like', sprintf('%%%s%%', $search))
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn ($user): array => [
                            $user->id => sprintf('%s %s (@%s)', $user->name, $user->second_name, $user->username),
                        ])
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record): string => sprintf('%s %s (@%s)', $record->name, $record->second_name, $record->username))
                    ->preload()
                    ->disabledOn('edit')
                    ->required(),

                Select::make('likeable_type')
                    ->options([
                        Post::class => 'Post',
                        Comment::class => 'Comment',
                    ])
                    ->disabledOn('edit')
                    ->required()
                    ->live(),

                Select::make('likeable_id')
                    ->disabledOn('edit')
                    ->options(function (callable $get) {
                        $type = $get('likeable_type');

                        return match ($type) {
                            Post::class => Post::query()
                                ->with('user')
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn ($post): array => [
                                    $post->id => sprintf('(@%s) — ', $post->user->username).str($post->text)->limit(40),
                                ]),

                            Comment::class => Comment::query()
                                ->with('user')
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn ($comment): array => [
                                    $comment->id => sprintf('(@%s) — ', $comment->user->username).str($comment->text)->limit(40),
                                ]),

                            default => [],
                        };
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    ->disabled(fn (callable $get): bool => blank($get('likeable_type'))),
            ]);
    }
}
