<div class="chat-profile-card rounded">
    <div class="chat-profile-title">
        <h2>My Profile</h2>
    </div>
    <!-- Profile Header -->
    <div class="chat-profile-header text-center p-4">
        @php
        $user = auth()->user();
        $profileImage = $user->profile_image ? userDirUrl($user->profile_image, 't') : null;
        $initial = strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1));
        @endphp
        
        @if($profileImage)
            <img class="chat-profile-picture" src="{{ $profileImage }}" alt="Profile Picture">
        @else
            <div class="group-icon chat-profile-picture" data-initial="{{ $initial }}"></div>
        @endif
        <h2 class="chat-profile-name mt-3">{{auth()->user()->first_name." ".auth()->user()->last_name}}</h2>
        @if(auth()->user()->is_login==1)
        <p class="chat-profile-status text-success">Active</p>
        @else
        <p class="chat-profile-status text-danger">Inactive</p>
        @endif
    </div>

    <!-- Accordion -->
    <div class="accordion chat-profile-accordion" id="chatProfileAccordion">
        <!-- About Section -->
        <div class="accordion-item">
        </div>
        <!-- Attached Files Section -->
        <div class="accordion-item" style="display: none;">
            <h2 class="accordion-header" id="filesHeader">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filesContent" aria-expanded="false" aria-controls="filesContent">
                    Attached Files
                </button>
            </h2>
            <div id="filesContent" class="accordion-collapse collapse" aria-labelledby="filesHeader"
                data-bs-parent="#chatProfileAccordion">
                <div class="accordion-body">
                    This is the Attached Files section content.
                </div>
            </div>
        </div>
    </div>
</div> 