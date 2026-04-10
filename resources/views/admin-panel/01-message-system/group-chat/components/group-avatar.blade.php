@props(['group'])

<div class="group-avatar">
    @if($group->group_image)
        <img src="{{ groupChatDirUrl($group->group_image, 't') }}" 
             alt="{{ $group->name }}"
             class="group-image">
    @else
        @php
            $initial = strtoupper(substr($group->name, 0, 1));
        @endphp
        <div class="group-icon" data-initial="{{ $initial }}">
            {{ $initial }}
        </div>
    @endif
    
    @if($group->type == 'Private')
        <div class="privacy-badge">
            <i class="fa-solid fa-lock"></i>
        </div>
    @endif
</div> 