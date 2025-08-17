{{-- Global App Loader Overlay --}}
<x-loader 
    overlay="true" 
    id="app-loader-overlay" 
    size="medium"
    class="loader-hidden"
/>

{{-- Include loader CSS and JS --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/loader.js') }}"></script>
@endpush
