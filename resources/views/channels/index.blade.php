<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-6 py-3">
                Name
            </th>
            <th scope="col" class="px-6 py-3">
                Action
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($channels as $channel)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$channel->name}}
                </th>
                <td class="px-6 py-4">
                    <form action="{{route('channels.edit', $channel->slug)}}">
                        @csrf
                        <x-button>Edit</x-button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$channels->links()}}

    <x-notify/>
</x-admin-layout>
