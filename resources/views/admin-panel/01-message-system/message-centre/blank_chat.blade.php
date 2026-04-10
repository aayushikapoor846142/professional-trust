
<div class="message-content">
    <!-- @include('admin-panel.01-message-system.message-centre.invite-users') -->
    <div class="welcome-chat">
        <div class="chat-avatar">
            @php
            $user = auth()->user();
            $profileImage = $user->profile_image ? userDirUrl($user->profile_image, 't') : null;
            $initial = strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1));
        @endphp
        
        @if($profileImage)
            <img src="{{ $profileImage }}" alt="Profile Picture">
        @else
            <div class="group-icon chat-profile-picture" data-initial="{{ $initial }}"></div>
        @endif
      
        </div>
        <h5>Welcome</h5>
        <h6>{{auth()->user()->first_name.' '.auth()->user()->last_name}}</h6>
    </div>

</div>