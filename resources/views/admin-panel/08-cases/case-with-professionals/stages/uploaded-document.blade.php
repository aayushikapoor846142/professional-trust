<b class="mt-5">Uploaded Documents</b>

@foreach($other_document_folders as $folder)
<div class="cdsTYDashboard-profile-documents-container-box mt-2">
    <h6>{{$folder->name}}</h6>
    @if(!empty($case_documents))
        @foreach(collect($case_documents)->where('folder_id',$folder->id) as $value)
            @php $fileNames = explode(',', $value->file_name); @endphp 
            @foreach($fileNames as $fileName)
                <div class="cdsTYDashboard-profile-documents-container-box-segment">
                    <div class="cdsTYDashboard-profile-documents-details-wrap">
                        <div class="cdsTYDashboard-profile-documents-image">
                            
                        </div>
                        <div class="cdsTYDashboard-profile-documents-details">
                            <h3> {{ $fileName }}</h3>
                            <span>{{dateFormat($value->created_at)}}</span>
                        </div> 
                    </div>
                    <div class="cdsTYDashboard-profile-documents-buttons">
                        <a href="{{ baseUrl('case-with-professionals/download-documents') }}?case_id={{ $case_id }}&file={{ $fileName }}" class="cdsTYDashboard-button-light cdsTYDashboard-button-small download-link">
                            <i class="fa-regular fa-cloud-arrow-down" aria-hidden="true"></i>   Download
                        </a>
                        <a href="http://127.0.0.1:8081/panel/professional/remove-file/81" class="cdsTYDashboard-button-light-outline cdsTYDashboard-button-small">
                            Remove
                        </a>
                    </div>
                </div>
            @endforeach
        @endforeach
    @else

        <div class="cdsTYDashboard-profile-documents-container-box-segment">
            <div class="cdsTYDashboard-profile-documents-details-wrap">
                <span class="text-danger">Document not uploaded yet</span>
            </div>
        </div>
    @endif
</div>
@endforeach
