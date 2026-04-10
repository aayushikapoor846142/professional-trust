<!-- <div class="chat-profile-sidebar" > -->
            <div class="chat-profile-card rounded">
                <div class="chat-profile-title">
                    <h2> Profile</h2>
                </div>
                <!-- Profile Header -->
                <div class="chat-profile-header text-center p-4">
                    @if(auth()->user()->id!=$get_chat_professional->id)
                    @if($get_chat_professional->profile_image != '')
                    <img class="chat-profile-picture"
                        src="{{ $get_chat_professional->profile_image ? userDirUrl($get_chat_professional->profile_image, 't') : 'assets/images/default.jpg' }}"
                        alt="Doris">
                    @else
                    <div class="group-icon chat-profile-picture"
                        data-initial="{{ userInitial($get_chat_professional) }}"></div>
                    @endif
                    @else
                    @if($get_chat_user->profile_image != '')
                    <img class="chat-profile-picture"
                        src="{{ $get_chat_user->profile_image ? userDirUrl($get_chat_user->profile_image, 't') : 'assets/images/default.jpg' }}"
                        alt="Doris">
                    @else
                    <div class="group-icon chat-profile-picture" data-initial="{{ userInitial($get_chat_user) }}"></div>
                    @endif
                    @endif

                    <h2 class="chat-profile-name mt-3">
                        @if(auth()->user()->id!=$get_chat_professional->id)
                        {{$get_chat_professional->first_name." ".$get_chat_professional->last_name}}
                        @else
                        {{$get_chat_user->first_name." ".$get_chat_user->last_name}}
                        @endif
                    </h2>
                    @if(auth()->user()->id!=$get_chat_professional->id)
                    @if(loginStatus($get_chat_professional) ==1)
                    <p class="chat-profile-status text-success sidebarOnlineStatus{{$chat_id}}">Active</p>
                    @else
                    <p class="chat-profile-status text-danger sidebarOnlineStatus{{$chat_id}}">Inactive</p>
                    @endif
                    @else
                    @if(loginStatus($get_chat_user) == 1)
                    <p class="chat-profile-status text-success sidebarOnlineStatus{{$chat_id}}">Active</p>
                    @else
                    <p class="chat-profile-status text-danger sidebarOnlineStatus{{$chat_id}}">Inactive</p>

                    @endif
                    @endif
                    <p class="chat-profile-description mt-3">
                    </p>
                </div>
    
                <!-- Accordion -->
                <div class="accordion chat-profile-accordion" id="chatProfileAccordion">

                    <div class="accordion-item" style="display: none">
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
        <!-- </div> -->