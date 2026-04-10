@if(!empty($connected_user_list))


@foreach($connected_user_list as $connect)
@php 
if($type == 'followers')
$user = $connect->follower;
else
$user = $connect->following;

@endphp
    <div class="connect-section">
        <div class="chat-item" href="javascript:;" data-chat-unique-id="{{$user->unique_id}}" data-chat-id="{{$user->id}}" data-href="{{ baseUrl('message-centre/chat/'.$user->unique_id) }}">
            <div class="chat-avatar">
                @if($user->profile_image != '')
                    <img src="{{ $user->profile_image ? userDirUrl($user->profile_image, 't') : 'assets/images/default.jpg' }}" alt="Doris">
                @else
                    <div class="group-icon" data-initial="{{ userInitial($user) }}"></div>
                @endif
            </div>
            <div class="chat-info @if($type=='following') cds-following-info @endif">
                <p class="chat-name">{{$user->first_name." ".$user->last_name}}</p>
                @if($type == 'followers')
                <div class="btn-group">
                    <a class="btn btn-sm btn-white cds-followfropdwon" href="javascript:void(0)" id="defaultDropdown"
                        data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                    </a>
                    <ul class="dropdown-menu py-1" aria-labelledby="defaultDropdown">
                        @if(checkIfFollowing($connect->connection_with,$connect->user_id) > 0)
                            <li>
                                <a class="dropdown-item" onclick="unfollow({{$user->id}},'{{$type}}')" href="javascript:;">
                                    Unfollow
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" onclick="removeFromFollowers({{$user->id}},'{{$type}}')" href="javascript:;">
                                    Remove From Followers
                                </a>
                            </li>
                        @else
                        <li>
                            <a class="dropdown-item" href="javascript:;" onclick="followBack({{$user->id}},'{{$type}}')">
                                 Follow Back
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
                    <!-- @if(checkIfFollowing($connect->connection_with,$connect->user_id) > 0)
                        <a href="javascript:;" class="btn btn-outline-danger btn-sm" onclick="unfollow({{$user->id}},'{{$type}}')">Unfollow</a>
                    @else
                        <div class="follow-div">
                            <a href="javascript:;" class="btn btn-outline-primary btn-sm" onclick="followBack({{$user->id}},'{{$type}}')">Follow Back</a>
                        </div>
                    @endif -->
                @else
                    @if(checkIfFollowing($connect->user_id,$connect->connection_with) > 0)
                        <a href="javascript:;" class="cds-btn-unfollow cds-smallbtn" class="text-danger" onclick="unfollow({{$user->id}},'{{$type}}')">Unfollow</a>
                    @else
                        <div class="follow-div">
                            <a href="javascript:;" class="followback" onclick="followBack({{$user->id}},'{{$type}}')">follow back</a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endforeach
@else

    <div>No data</div>

@endif
