@props([
    'color' => 'text-white',
    'bgColor' => 'bg-blue-600 '
])
<button {{ $attributes->merge(['class' => "$color $bgColor rounded-full px-4 py-2"]) }} type="submit">
    {{$slot}}
</button>
