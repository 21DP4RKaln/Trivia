@props(['size' => 'medium', 'overlay' => false, 'id' => 'loader'])

@php
$sizeClasses = [
    'small' => '--cell-size: 32px;',
    'medium' => '--cell-size: 52px;',
    'large' => '--cell-size: 72px;'
];
$currentSize = $sizeClasses[$size] ?? $sizeClasses['medium'];
@endphp

<div 
    {{ $attributes->merge(['class' => $overlay ? 'loader-overlay' : 'loader-wrapper']) }}
    id="{{ $id }}"
    @if($overlay) style="display: none;" @endif
>
    @if($overlay)
    <div class="loader-backdrop"></div>
    @endif
    
    <div class="loader" style="{{ $currentSize }}">
        <div class="cell d-0"></div>
        <div class="cell d-1"></div>
        <div class="cell d-2"></div>
        <div class="cell d-1"></div>
        <div class="cell d-2"></div>
        <div class="cell d-2"></div>
        <div class="cell d-3"></div>
        <div class="cell d-3"></div>
        <div class="cell d-4"></div>
    </div>
</div>
