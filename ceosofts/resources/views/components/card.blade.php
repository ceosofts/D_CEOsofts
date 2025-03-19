@props(['title' => null, 'footer' => null, 'headerClass' => '', 'bodyClass' => '', 'footerClass' => ''])

<div {{ $attributes->merge(['class' => 'card shadow-sm mb-4']) }}>
    @if($title)
        <div class="card-header {{ $headerClass }}">
            <h5 class="card-title mb-0">{{ $title }}</h5>
        </div>
    @endif
    
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="card-footer {{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div>
