<?php

namespace App\Models;

use App\Traits\RecordActivity;
use Chelout\RelationshipEvents\Concerns\HasMorphedByManyEvents;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Favorite extends MorphPivot
{
    use HasFactory;

    protected $table = 'favorites';

    public $incrementing = true;

    public function activities()
    {
        return $this->morphMany(Activities::class, 'subject');
    }

    public function getReply()
    {
        return Reply::find($this->favorite_id);
    }
}
