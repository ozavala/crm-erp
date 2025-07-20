{{-- filepath: resources/views/components/polymorphic-link.blade.php --}}
@if($href)
    <a href="{{ $href }}">{{ $text }}</a>
@else
    {{ $text }}
@endif