
@if($record->reply != '')
        @foreach (json_decode($record->reply, true) as $key => $files)
        
        <div class="cdsViewfiles d-flex align-items-center gap-2 col-sm mt-2">
            <div class="col-auto">
                <img class="size28 img-fluid rounded-0" src="http://127.0.0.1:8000/assets/svg/google-slides.svg" alt="Files" />
            </div>
            <div class="col">
                <h6 class="fileTitle mb-1">
                    Document: <span>{{ getRequestDocument($key)->name }}{{ getRequestDocument($key)->name }}
                </h6>

                <ul class="list-inline list-separator small">
                    @foreach (explode(',', $files) as $file)
                        <li class="list-inline-item">
                            <a href="{{ asset('uploads/' . trim($file)) }}" target="_blank">{{ $file }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-sm-5 mt-2 mt-sm-0 col-sm-auto text-end">
                <div class="btn-group">
                    <a class="btn btn-outline-primary dropdown-toggle" href="javascript:void(0)" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                        <li>
                            <a href="{{ baseUrl('case-with-professionals/download-documents') }}?case_id={{ $case_id }}&file={{ trim($file) }}"> Download </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{--<label><strong>Document: {{ getRequestDocument($key)->name }}</strong></label>
        <ul>
            @foreach (explode(',', $files) as $file)
                <li>
                    <a href="{{ asset('uploads/' . trim($file)) }}" target="_blank">{{ $file }}</a>
                    <a href="{{ baseUrl('case-with-professionals/download-documents') }}?case_id={{ $case_id }}&file={{ trim($file) }}"> Download </a>
                </li>
            @endforeach
        </ul>--}}
    @endforeach
   
@else
    <p>No uploaded files available.</p>
@endif

