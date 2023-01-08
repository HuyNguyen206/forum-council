<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{$user->email}}
        </h2>
        @can('upload', $user)
            <livewire:avatar-upload :profileUser="$user"/>
        @endcan
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg w-2/3 pr-2">
                @foreach($activitiesGroupByDate as $date => $activitiesChunk)
                    <span class="text-2xl font-bold">{{$date}}</span>
                    @foreach($activitiesChunk as $activity)
                        @if(view()->exists("components.activities.{$activity->type}"))
                            @include("components.activities.{$activity->type}")
                            <hr>
                        @endif
                    @endforeach
                    <hr>
                @endforeach
            </div>
            <div>
                {{$activities->links()}}
            </div>
        </div>
    </div>
</x-app-layout>
