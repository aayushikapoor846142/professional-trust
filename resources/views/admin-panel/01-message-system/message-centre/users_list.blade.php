                            @if($professionalsdata)
                            @foreach($professionalsdata as $prof)
                            <div class="chat-item" onclick="getUserList('{{$prof->unique_id}}')">
                                <div class="chat-avatar">
                                    <img src="{{ $prof->profile_image ? userDirUrl($prof->profile_image, 't') : 'assets/images/default.jpg' }}" alt="Doris">
                                    @if($prof->is_login==1)
                                    <span class="status-online"></span>
                                    @else
                                    <span class="status-offline"></span>
                                    @endif
                                </div>
                                <div class="chat-info">
                                    <p class="chat-name">{{$prof->first_name." ".$prof->last_name}}</p>
                                    <p class="chat-preview">
                                      @if($prof->can_accept_decline_request)
                                            <div class="chat-request-action-btn">
                                                <li>
                                                  <a  href="{{ baseUrl('accept-chat-request/'.$prof->unique_id) }}">
                                                      <i class="tio-edit"></i> Accept
                                                  </a>
                                                </li>
                                                <li>
                                                  <a  href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('decline-chat-request/'.$prof->unique_id) }}">
                                                         Decline
                                                  </a>
                                                </li>
                                            </div>
                                    @elseif($prof->can_send_request)
                                        <div class="chat-request-action-btn">
                                        <li>
                                        <a  href="{{ baseUrl('send-chat-request/'.$prof->unique_id.'/')}}">
                                            <i class="tio-edit"></i> Send Request
                                        </a>
                                        </li>
                                        </div>
                                    @else
                                        <span>Request Pending.</span>
                                    @endif

                                           
                                    </p>
                                </div>


                            </div>
                            @endforeach
                            @else
                            <div class="empty-chat-request">
                                <h5>No Request Available</h5>
                            </div>
                            @endif