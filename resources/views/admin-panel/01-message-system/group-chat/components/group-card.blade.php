@props(['group', 'isActive' => false, 'showMembers' => true])

<div class="group-card {{ $isActive ? 'active' : '' }}" 
     data-group-id="{{ $group->id }}"
     data-unique-id="{{ $group->unique_id }}">
    
    @include('admin-panel.01-message-system.group-chat.components.group-avatar', ['group' => $group])
    
    <div class="group-info">
        <h4 class="group-name">{{ $group->name }}</h4>
        
        @if($showMembers)
            <p class="group-members">{{ $group->members->count() }} members</p>
        @endif
        
        @if($group->lastMessage)
            <p class="last-message">{{ Str::limit($group->lastMessage->message, 50) }}</p>
        @endif
    </div>
    
    @include('admin-panel.01-message-system.group-chat.components.group-badge', ['group' => $group])
</div> 