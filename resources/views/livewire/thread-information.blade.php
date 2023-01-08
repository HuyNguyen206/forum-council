<div class="bg-white w-1/3 p-2">
    {{--                @php--}}
    {{--                    $repliesCount = $thread->replies()->count();--}}
    {{--                @endphp--}}
    <p>This thread was published {{$thread->created_at->diffForHumans()}} by {{$thread->user->name}} and
        current
        has {{$threadRepliesCount}} {{\Illuminate\Support\Str::plural('replies',$threadRepliesCount)}}</p>
    @auth
        <x-button wire:click.prevent="toggleSubscribe" :color="$color">{{$action}}</x-button>
        @can('canLockThread')
            <x-button wire:click.prevent="toggleLockThread" color="{{$thread->is_lock ? 'bg-red-500 text-white' : 'bg-green-500 text-white'}}">
                {{$thread->is_lock ? 'Unlock' : 'Lock'}}
            </x-button>
        @endcan
        <x-notify/>
    @endauth

</div>

