<?php

namespace App\Http\Controllers;

use App\Exceptions\SpamReplyException;
use App\Models\Channel;
use App\Models\Thread;
use App\Rules\CheckSpam;
use App\Services\ThreadsTrending;
use App\Services\ThreadsVisits;
use App\SpamRules\Spam;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ThreadController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except(['index', 'show']);
    }

    public function index(Channel $channel = null)
    {
        return view('threads.index', compact('channel'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', new CheckSpam(Thread::class, 'title')],
            'body' => ['required', new CheckSpam(Thread::class, 'body')],
            'channel_id' => ['required', Rule::exists('channels', 'id')->where('is_archive', false)]
        ]);

        $thread = $request->user()->threads()->create($validated);

        return redirect($thread->showThreadPath())->with('message', 'The thread was created successfully!');
    }

    public function show(Thread $thread, ThreadsTrending $trending, ThreadsVisits $threadsVisits)
    {
        $trending->push($thread->id);
        $threadsVisits->recordVisits($thread->id);
        $thread->loadCount('replies as repliesCount');

        if (auth()->check()) {
            $thread->cacheKey();
        }

        return view('threads.show', compact('thread'));
    }

    public function destroy(Thread $thread)
    {
        $this->authorize('delete', $thread);
//        abort_if($thread->user_id !== auth()->id(), 403, 'You can delete only your threads');
        $thread->delete();

        if (\request()->wantsJson()) {
            return response([], 204);
        }

        return back()->with('message', 'The thread was deleted successfully!');;
    }
}
