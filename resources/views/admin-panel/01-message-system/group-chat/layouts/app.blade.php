@extends('admin-panel.layouts.app')

@section('content')
    @yield('message-content')
@endsection

@section('scripts')
    <!-- Common scripts -->
    <script src="{{ asset('assets/js/message-system/shared/chat-common.js') }}"></script>
    
    <!-- Page specific scripts -->
    @stack('page-scripts')
    
    <!-- Initialize -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof ChatCommon !== 'undefined') {
                ChatCommon.init();
            }
            @stack('init-scripts')
        });
    </script>
@endsection 