@foreach(explode(',',$get_attachments) as $attachment)
    @php
        $attachmentLower=strtolower($attachment);
    @endphp
    @if(\Illuminate\Support\Str::endsWith($attachmentLower, ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
        <div class="attachment" data-file-name="{{ $attachment }}">
            <a href="javascript:;" onclick="showPopup('<?php echo baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$chat_msg_Unique_id) ?>')" class="popup-link" title="{{$attachment}}">
                <img src="{{ groupChatDirUrl($attachment, 's') }}" class="img-fluid msg-image-show" alt="images" />
            </a>
        </div>
        @elseif(\Illuminate\Support\Str::endsWith($attachmentLower, ['.pdf']))
        <div class="attachment" data-file-name="{{ $attachment }}">
            <a href="javascript:;" onclick="showPopup('<?php echo baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$chat_msg_Unique_id) ?>')" class="popup-link cds-pdfPreview" title="{{$attachment}}">
                @php
                $pdfid = "pdf-".mt_rand();
                $fileurl = groupChatDirUrl($attachment, 'r');
                $thumbimg = groupChatDirUrl($attachment, 'r',true);
                @endphp
                <div class="pdfView">
                    <img src="{{$thumbimg}}" alt="Thumbnail Preview" class="img-fluid pdf-thumbnail">
                </div>
                <div class="fileTitle">
                    <p class="file-name">{{$attachment}}</p>
                </div>
            </a>
        </div>
        @elseif(\Illuminate\Support\Str::endsWith($attachmentLower, ['.mp3']))
        <div class="attachment" data-file-name="{{ $attachment }}">
            <div class="attachment-docs">
                <a href="javascript:;" onclick="showPopup('<?php echo baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$chat_msg_Unique_id) ?>')" class="popup-link" title="{{$attachment}}">
                    <div class="cds-smalIcons song-title">
                        <img src="{{url('assets/images/chat-icons/headphone.svg')}}" alt="File Icon" class="file-mp3-img img-fluid" />
                        <p class="file-name">{{$attachment}}</p>
                    </div>
                </a>
            </div>
        </div>
        @elseif(\Illuminate\Support\Str::endsWith($attachmentLower, ['.mp4']))
        <div class="attachment" data-file-name="{{ $attachment }}">
            <div class="attachment-docs">
                <a href="javascript:;" onclick="showPopup('<?php echo baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$chat_msg_Unique_id) ?>')" class="popup-link" title="{{$attachment}}">
                    <div class="cds-smalIcons video-title">
                        <img src="{{url('assets/images/chat-icons/video.svg')}}" alt="File Icon" class="file-video-img img-fluid" />
                        <p class="file-name">{{$attachment}}</p>
                    </div>
                </a>
            </div>
        </div>
        @elseif(\Illuminate\Support\Str::endsWith($attachmentLower, ['.xls', '.xlsx']))
        <div class="attachment" data-file-name="{{ $attachment }}">
            <a href="javascript:;" onclick="showPopup('<?php echo baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$chat_msg_Unique_id) ?>')" class="popup-link" title="{{$attachment}}">
                <div class="cds-smalIcons">
                    <img src="{{url('assets/images/chat-icons/xls.svg')}}" alt="File Icon"  class="img-fluid file-image" />
                    <p class="file-name">{{$attachment}}</p>
                </div>
            </a>
        </div>
        @elseif(\Illuminate\Support\Str::endsWith($attachmentLower, ['.doc']))
        <div class="attachment" data-file-name="{{ $attachment }}">
            <div class="attachment-docs">
                <a href="javascript:;" onclick="showPopup('<?php echo baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$chat_msg_Unique_id) ?>')" class="popup-link" title="{{$attachment}}">
                    <div class="cds-smalIcons">
                        <img src="{{url('assets/images/chat-icons/doc-icon.png')}}" alt="File Icon" class="img-fluid file-image" />
                        <p class="file-name">{{$attachment}}</p>
                    </div>
                </a>
            </div>
        </div>
        @else
        <div class="attachment" data-file-name="{{ $attachment }}">
            <div class="attachment-docs">
                <a href="javascript:;" onclick="showPopup('<?php echo baseUrl('group-message/preview-file?file_name='.$attachment.'&chat_id='.$chat_msg_Unique_id) ?>')" class="popup-link" title="{{$attachment}}">
                    <div class="cds-smalIcons">
                        <img src="{{url('assets/images/chat-icons/file.svg')}}" alt="File Icon" class="img-fluid file-image" />
                        <p class="file-name">{{$attachment}}</p>
                    </div>
                </a>
            </div>
        </div>
    @endif
@endforeach