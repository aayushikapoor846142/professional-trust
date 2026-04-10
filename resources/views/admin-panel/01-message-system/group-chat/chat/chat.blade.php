@extends('admin-panel.01-message-system.group-chat.layouts.app')

@section('message-content')
    @include('admin-panel.01-message-system.group-chat.chat.chat_message')
@endsection

@push('page-scripts')
    <script src="{{ asset('assets/js/message-system/groups/chat/group-chat.js') }}"></script>
@endpush

@push('init-scripts')
    <script>
        window.groupChatConfig = {
            baseUrl: "{{ baseUrl('/') }}",
            groupId: "{{ $group_id ?? '' }}",
            userId: "{{ auth()->user()->id }}",
            openfor: "{{ $openfor ?? '' }}",
            csrfToken: "{{ csrf_token() }}"
        };
        
        GroupChat.init();
    </script>
@endpush