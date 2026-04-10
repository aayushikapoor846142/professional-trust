<div class="group-join-container">
    <div class="group-banner">
        <div class="back-chats" onclick="backToOtherGroup()">
            <i class="fa-solid fa-angle-left"></i>
        </div>
        @if($group->banner_image)
        <img src="{{ groupChatDirUrl($group->banner_image, 't') }}" alt="Doris">
        @else
        <img src="{{url('assets/images/default-banner.jpg')}}" alt="banner_image">
        @endif
    </div>
    <div class="group-join-content">
        <div class="group-join-heading">
            <div class="group-join-icon">
                @if($group->group_image)
                <img src="{{ groupChatDirUrl($group->group_image, 't') }}" alt="Doris">
                @else
                @php
                $initial = strtoupper(substr($group->name, 0, 1));
                @endphp
                <div class="group-icon" data-initial="{{$initial}}"></div>
                @endif

            </div>
            <div class="group-join-title">
                <h3 class="" id="headerGroupName">{{$group->name}}</h3>
                <p>
                    Created By: {{ $group->groupAdmin->first_name ?? '' }} {{ $group->groupAdmin->last_name ?? '' }}
                </p>
            </div>
        </div>
        <div class="group-join-actionbtn">
            <div>
                <i class="fa-regular fa-user-group"></i>
                {{ count($group->members) > 0 ? count($group->members) : 'No members yet' }}
            </div>
            <div class="join-btn">

                @if($group->groupJoinRequest)
                <div class="" onclick="withdrawRequest({{ $group->id }})">
                    Withdraw Request
                </div>
                @else
                <div class="" onclick="requestToJoinGroup({{ $group->id }})">
                    Join Group
                </div>
                @endif
            </div>

        </div>




    </div>
    <!-- Created At: {{ $group->created_at ? $group->created_at->format('d M Y') : 'N/A' }} -->
    <div class="group-join-desc">
        <!-- <p class="chat-name mb-2" id="headerGroupName">{{$group->description ?? ''}}</p> -->
        <p class="" id="headerGroupName">
            {{$group->description ?? ''}}
        </p>
    </div>
    <div>