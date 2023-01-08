@props([
    'color' => 'text-blue-700',
    'href' => null
])
<a href="{{$href}}" {{ $attributes->merge(['class' => "$color rounded-full px-4 py-2 underline inline-block my-2"]) }}>
        {{$slot}}
</a>
