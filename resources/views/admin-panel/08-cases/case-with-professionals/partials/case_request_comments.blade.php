{{-- Debug: {{ count($request_notes) }} comments found --}}
@if(count($request_notes) > 0)
    @foreach($request_notes as $note)
    <!-- Comment 2 with attachment -->
   <div class="CdsCaseRequest-comment">
        {{-- Header --}}
        <div class="CdsCaseRequest-comment-header">
            {{-- Avatar --}}
            <div class="CdsCaseRequest-comment-avatar" style="background: linear-gradient(135deg, #5b4be7 0%, #06b6d4 100%);">
                {{ strtoupper(substr($note->User->first_name, 0, 1)) }}{{ strtoupper(substr($note->User->last_name, 0, 1)) }}
            </div>
            {{-- Meta Info --}}
            <div class="CdsCaseRequest-comment-meta">
                <h4>
                    @if($note->user_id == auth()->user()->id)
                        You
                    @else
                        {{ $note->User->first_name }} {{ $note->User->last_name }}
                    @endif
                </h4>
                <p>
                    @if($note->user_id == auth()->user()->id)
                        {{ ucwords(str_replace("-", " ", $note->User->role ?? '')) }}
                    @else
                        @if($note->User->role != 'staff')
                            {{ ucwords(str_replace("-", " ", $note->User->role)) }}
                        @else
                            {{ ucwords(str_replace("-", " ", $note->User->role ?? '')) }}
                        @endif
                    @endif
                </p>
            </div>
        </div>

        {{-- Comment Body --}}
        <div class="CdsCaseRequest-comment-body">
            <p>{{ $note->notes }}</p>
            {{-- Attachments --}}
            @if(!empty($note->attachment))
                @php
                    $attachments = explode(",", $note->attachment);
                @endphp
                @foreach($attachments as $file)
                    <div class="CdsCaseRequest-comment-attachment">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                        <div class="CdsCaseRequest-attachment-info">
                            <a href="{{ baseUrl('case-with-professionals/download-note-attachment?file='.$file) }}" download class="CdsCaseRequest-attachment-name text-primary d-block">
                                {{ $file }}
                            </a>
                            {{-- Optionally calculate file size if needed --}}
                            {{-- <span class="CdsCaseRequest-attachment-size">2.4 MB</span> --}}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    @endforeach
@else
    <div class="CdsCaseRequest-comment">
        <div class="CdsCaseRequest-comment-body">
            <p class="text-muted">No comments yet. Be the first to add a comment!</p>
        </div>
    </div>
@endif