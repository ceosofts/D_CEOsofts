@props(['striped' => false, 'hover' => true, 'responsive' => true, 'bordered' => false])

<div @if($responsive) class="table-responsive" @endif>
    <table {{ $attributes->merge([
        'class' => 'table' . 
                  ($striped ? ' table-striped' : '') . 
                  ($hover ? ' table-hover' : '') . 
                  ($bordered ? ' table-bordered' : '') . 
                  ' align-middle'
    ]) }}>
        @isset($header)
            <thead>
                {{ $header }}
            </thead>
        @endisset
        
        <tbody>
            {{ $slot }}
        </tbody>
        
        @isset($footer)
            <tfoot>
                {{ $footer }}
            </tfoot>
        @endisset
    </table>
</div>
