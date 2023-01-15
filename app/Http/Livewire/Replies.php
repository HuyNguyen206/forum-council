<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Action\RepliesBuilder;
use Livewire\Component;
use Livewire\WithPagination;

class Replies extends Component
{
    use WithPagination;
    protected $listeners = ['refreshReplies'];
    public $thread;

    public function render()
    {
//        $user = auth()->user();
//        $replies = $this->thread->replies()->with('user')->withCount('favoriteUsers as favoriteUsersCount');
//        if ($user) {
//            $replies->addSelect([
//                'isFavorite' => Favorite::query()->select('id')
//                    ->where('favorite_type', Reply::class)
//                    ->whereColumn('favorite_id', 'replies.id')
//                    ->where('user_id', $user->id)
//            ]);
//        }
//        $replies = $replies->latest()->paginate(5, pageName:'page-reply')->withQueryString();
        $repliesBuilder = RepliesBuilder::build($this->thread);
        $replies = $repliesBuilder->paginate(config('council.pagination.perPage'), pageName:'page-reply')->withQueryString();

        return view('livewire.replies', compact('replies'));
    }

    public function refreshReplies($message = null)
    {
        if ($message) {
            $this->dispatchBrowserEvent('notify', ['message' => $message]);
        }
    }
}
