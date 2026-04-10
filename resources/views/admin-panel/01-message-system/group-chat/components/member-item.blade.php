@props(['member', 'isSelected' => false])

<div class="member-item {{ $isSelected ? 'selected' : '' }}" 
     data-member-id="{{ $member->id }}"
     data-name="{{ strtolower($member->first_name.' '.$member->last_name) }}">
    
    <input type="checkbox" 
           id="member-{{ $member->id }}" 
           class="member-checkbox" 
           value="{{ $member->id }}" 
           name="member_id[]"
           {{ $isSelected ? 'checked' : '' }}>
    
    <div class="member-avatar">
        @if($member->profile_image)
            <img src="{{ userDirUrl($member->profile_image, 'm') }}" 
                 alt="{{ $member->first_name }} {{ $member->last_name }}">
        @else
            @php
                $initial = strtoupper(substr($member->first_name, 0, 1)) . 
                          strtoupper(substr($member->last_name, 0, 1));
            @endphp
            <div class="user-icon">{{ $initial }}</div>
        @endif
        
        @if($member->is_login)
            <span class="status-online"></span>
        @else
            <span class="status-offline"></span>
        @endif
    </div>
    
    <div class="member-info">
        <p class="member-name">{{ $member->first_name }} {{ $member->last_name }}</p>
        @if($member->email)
            <p class="member-email">{{ $member->email }}</p>
        @endif
    </div>
</div> 