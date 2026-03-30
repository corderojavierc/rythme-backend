<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read boolean $artist
 * @property-read integer $followers
 * @property-read integer | null $listeners
 * @property-read string | null $youtube
 * @property-read string | null $tiktok
 * @property-read string | null $instagram
 * @property-read string | null $spotify
 * @property-read string | null $twitch
 * @property-read string $description
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\ArtistApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistApplication query()
 */
	class ArtistApplication extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read string $id
 * @property-read string $post_id
 * @property-read string $user_id
 * @property-read string $text
 * @property-read integer $likes
 * @property-read integer $repost
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read int|null $likes_count
 * @property-read \App\Models\Post|null $post
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\CommentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment query()
 */
	class Comment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $title
 * @property-read string $description
 * @property-read string $location
 * @property-read string $date
 * @property-read string | null $image
 * @property-read string $capacity
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $hasParticipants
 * @property-read int|null $has_participants_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read string $id
 * @property-read string $event_id
 * @property-read string $user_id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant query()
 */
	class EventParticipant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read string $id
 * @property-read string $follower_id
 * @property-read string $followed_id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow query()
 */
	class Follow extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $likeable_type
 * @property-read string $likeable_id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $target
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\LikeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like query()
 */
	class Like extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read string $id
 * @property-read string $title
 * @property-read string $cover_url
 * @property-read string $description
 * @property-read string $release_date
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $createdBy
 * @property-read int|null $created_by_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $post
 * @property-read int|null $post_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recommendation> $recommendated
 * @property-read int|null $recommendated_count
 * @method static \Database\Factories\MusicFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Music newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Music newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Music query()
 */
	class Music extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $music_id
 * @property-read string $text
 * @property-read decimal $rating
 * @property-read integer $likes
 * @property-read integer $repost
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read int|null $likes_count
 * @property-read \App\Models\Music|null $music
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\PostFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post query()
 */
	class Post extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $music_id
 * @property-read string $message
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @method static \Database\Factories\RecommendationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recommendation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recommendation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recommendation query()
 */
	class Recommendation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read string $id
 * @property-read string $username
 * @property-read string $name
 * @property-read string $second_name
 * @property-read string $email
 * @property-read CarbonInterface|null $email_verified_at
 * @property-read integer $is_verified_as
 * @property-read string $password
 * @property-read string|null $remember_token
 * @property-read string $profile_name
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArtistApplication> $applicate
 * @property-read int|null $applicate_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comment
 * @property-read int|null $comment_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $createEvent
 * @property-read int|null $create_event_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Music> $createdMusic
 * @property-read int|null $created_music_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $followers
 * @property-read int|null $followers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $following
 * @property-read int|null $following_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Like> $gustar
 * @property-read int|null $gustar_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $participating
 * @property-read int|null $participating_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Music> $recommendate
 * @property-read int|null $recommendate_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 */
	final class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

