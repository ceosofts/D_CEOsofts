@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => null,
    'icon' => null,
])

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'btn btn-' . $variant . ($size ? ' btn-' . $size : '')]) }}
>
    @if($icon)
        <i class="fa fa-{{ $icon }} me-2"></i>
    @endif
    {{ $slot }}
</button>
