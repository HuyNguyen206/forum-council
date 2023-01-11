<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Channel extends Model
{
    use HasFactory;

    protected $casts = [
        'is_archive' => 'bool'
    ];

    protected static function booted()
    {
       static::creating(function (Channel $channel){
           $channel->slug = Str::slug($channel->name);
           Cache::forget('channels');
       });

       static::updated(function ($channel){
           Cache::forget('channels');
       });


       static::deleted(function (){
           Cache::forget('channels');
       });

       static::addGlobalScope('exclude-archive', function (Builder $builder){
           $builder->where('is_archive', 0);
       });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }
}
