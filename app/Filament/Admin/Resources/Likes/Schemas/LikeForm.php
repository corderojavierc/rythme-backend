<?php
namespace App\Filament\Admin\Resources\Likes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;


class LikeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'username')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) =>
                        User::where('name', 'like', "%{$search}%")
                            ->orWhere('second_name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($user) => [
                                $user->id => "{$user->name} {$user->second_name} (@{$user->username})"
                            ])
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} {$record->second_name} (@{$record->username})")
                    ->preload()
                    ->disabledOn('edit')
                    ->required(),

                Select::make('likeable_type')
                    ->options([
                        Post::class    => 'Post',
                        Comment::class => 'Comment',
                    ])
                    ->disabledOn('edit')
                    ->required()
                    ->live(),

                    Select::make('likeable_id')
                    ->disabledOn('edit')
                        ->options(function (callable $get) {
                            $type = $get('likeable_type');
                            return match($type) {
                                Post::class => Post::query()
                                    ->with('user')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn ($post) => [
                                        $post->id => "(@{$post->user->username}) — " . str($post->text)->limit(40)
                                    ]),

                                Comment::class => Comment::query()
                                    ->with('user')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn ($comment) => [
                                        $comment->id => "(@{$comment->user->username}) — " . str($comment->text)->limit(40)
                                    ]),

                                default => [],
                            };
                        })
                        ->searchable()
                        ->required()
                        ->live()
                        ->disabled(fn (callable $get) => blank($get('likeable_type'))),
            ]);
    }
}
