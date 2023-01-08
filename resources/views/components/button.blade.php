@props([
    'color' => 'bg-blue-600 text-white',
])
<button {{ $attributes->merge(['class' => "$color rounded-full px-4 py-2"]) }} type="submit">
    {{$slot}}
</button>
