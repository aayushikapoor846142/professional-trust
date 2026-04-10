
@if($userList->isNotEmpty())

    @foreach($userList as $user)
        <div class="col-xl-4 col-md-6 col-lg-6 mb-4">
            <div class="card-box">
                <div class="text-center">
                    @if($user->profile_image != '')
                        <img src="{{ $user->profile_image ? userDirUrl($user->profile_image, 't') : 'assets/images/default.jpg' }}" class="rounded-circle mx-auto img-profile" alt="Profile Image">
                    @else
                        <div class="group-icon img-profile" data-initial="{{ userInitial($user) }}"></div>
                    @endif
                </div>
                <h5 class="mt-3 mb-3 group-title-name">{{$user->first_name}} {{$user->last_name}}</h5>
                <p class="text-muted mb-2 lh-sm connection-email">{{$user->email}}</p>
                <p class="text-muted mb-4 lh-sm">{{$user->role}}</p>
                <div class="d-flex gap-1 mt-auto justify-content-center flex-wrap">
                    @if(empty(checkInvitation(auth()->user()->id,$user->id)))
                        <button class="btn-primary cds-btn-connect" onclick="sendConnection({{$user->id}})">Connect</button>
                    @elseif(!empty(checkInvitation(auth()->user()->id,$user->id)) && checkInvitation(auth()->user()->id,$user->id)->status == 0)
                    <button class="cds-btn-unfollow" onclick="removeConnection('{{checkInvitation(auth()->user()->id,$user->id)->unique_id??0}}')">Unconnect</button>
                    @endif
                    
                    @if(checkFollow($user->id) == 'Follow')
                    <button class="CdsTYButton-btn-primary cds-btn-follow" onclick="followBack({{$user->id}})">Follow</button>
                    @else
                    <button class="CdsTYButton-btn-primary cds-btn-unfollow" onclick="unfollowConnection({{$user->id}})">Unfollow</button>
                    @endif
                </div>
            </div>                        
        </div>
    @endforeach
@endif
