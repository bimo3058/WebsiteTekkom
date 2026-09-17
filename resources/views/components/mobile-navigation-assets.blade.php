@once
<link rel="stylesheet" href="{{ asset('css/mobile-navigation.css') }}?v={{ filemtime(public_path('css/mobile-navigation.css')) }}">
<script defer src="{{ asset('js/mobile-navigation.js') }}?v={{ filemtime(public_path('js/mobile-navigation.js')) }}"></script>
@endonce
