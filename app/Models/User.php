<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Reputation;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, PivotEventTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    static protected $pivotEvents = ['pivotAttached', 'pivotDetaching'];

    protected static function booted()
    {
        foreach (static::$pivotEvents as $event) {
            static::$event(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) use ($event) {
                if ($relationName === 'favoriteReplies') {
                    static::handlePivotEvents($model, $pivotIds, $event);
                }
            });
        }
    }

    public function avatarPath($withExist = true)
    {
        if (!$this->image_path) {
           return Storage::url("photos/avatar-default.png");
        }

        if ($this->image_path)
        {
            if (Storage::exists($this->image_path)) return Storage::url($this->image_path);

            return null;
        }

    }

    public function generateProfileLink()
    {
        $userProfile = route('users.profile', $this->name_slug);
//        dd(sprintf('<a style="color: #0000FF" href="%s">%s</a>',$userProfile, $this->name));
        return sprintf('<a style="color:#0000FF;" href="%s">%s</a>',$userProfile, $this->name);
//        return "<a style='color: #0000FF' href='$userProfile'>$this->name</a>";
    }

    public function subcribedThreads()
    {
        return $this->belongsToMany(Thread::class, 'thread_subscribed');
    }

    public function subscribe(Thread $thread)
    {
        $this->subcribedThreads()->attach($thread);
    }

    public function unSubscribe(Thread $thread)
    {
        $this->subcribedThreads()->detach($thread);
    }

    public function getRouteKeyName()
    {
        return 'name_slug';
    }

    public function favoriteReplies()
    {
        return $this->morphedByMany(Reply::class, 'favorite', 'favorites')
            ->withTimestamps()->withPivot(['id']);
    }


    public function threads()
    {
        return $this->hasMany(Thread::class);
    }


    public function activities()
    {
        return $this->hasMany(Activities::class)->take(10);
    }

    /**
     * @param $model
     * @param $pivotIds
     * @param string $event
     * @return void
     */
    static function handlePivotEvents($model, $pivotIds, string $event): void
    {
        $favorite = Favorite::query()
            ->where('user_id', $model->id)
            ->where('favorite_type', Reply::class)
            ->where('favorite_id', $replyId = current($pivotIds))
            ->first();
        if ($event === 'pivotAttached') {
            $action = 'create';
            Reputation::award(Reputation::REPLY_FAVORITED, Reply::find($replyId)->user);
        } else {
            $action = 'where';
        }

        $isCreate = $action === 'create';
        $builder = Activities::$action(['type' => "favorite_created", 'user_id' => auth()->id(), 'subject_type' => Favorite::class, 'subject_id' => $favorite->id]);
        if (!$isCreate) {
            Reputation::reduce(Reputation::REPLY_FAVORITED, Reply::find($replyId)->user);
            $builder->delete();
        }
    }

    public function getPointFormat()
    {
        return $this->points . ' ' . Str::plural('xp', $this->points);
    }

    public function isAdmin(): bool
    {
        return in_array($this->email, config('council.admins'), true);
    }
}
