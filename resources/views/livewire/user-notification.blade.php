<x-dropdown align="right" width="48" :keep-open-when-click-on-item="true">
    <x-slot name="trigger">
        <button
            class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                </svg>
                <div
                    class="absolute top-0 -right-1 ml-1 @if($unreadNotificationCount) bg-red-600 text-white @endif rounded-full p-3 w-2.5 h-2.5 flex justify-center items-center">
                    @if($unreadNotificationCount)
                        <span>
                        {{$unreadNotificationCount}}
                    </span>
                    @endif
                </div>
            </div>

        </button>
    </x-slot>

    <x-slot name="content">
        @forelse($unreadNotifications as $notification)
            <div class="p-2 border-b-2 border-gray-500 cursor-pointer"
                 wire:click="markNotificationAsRead('{{$notification->id}}')"
                 wire:key="notification-{{ $notification->id }}">
                {!! $notification->data['message'] !!}
            </div>

        @empty
            <span>No notification</span>
        @endforelse
        {{$unreadNotifications->links()}}
    </x-slot>
</x-dropdown>
