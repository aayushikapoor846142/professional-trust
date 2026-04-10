<div class="row">
     @php 
                $case_documents = array();
                if($record->case_documents != ''){
                    $case_documents = json_decode($record->case_documents,true);
                }
                
            @endphp
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Name:</label>
        <div>{{ $record->name ?? 'N/A' }}</div>
    </div>
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Sort Order:</label>
        <div>{{ $record->sort_order ?? 'N/A' }}</div>
    </div>
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Stage Type:</label>
        <div>{{ $record->stage_type ?? 'N/A' }}</div>
    </div>
    <div class="col-md-6 mb-3" style="{{ $record->stage_type != 'fill-form' ? 'display: none;' : '' }}">
        <label class="fw-bold">Form:</label>
        <div> {{getForm($record->type_id)->name}}</div>
    </div>
       <div class="col-md-6 mb-3" style="{{ $record->stage_type != 'case-document' ? 'display: none;' : '' }}">
        <label class="fw-bold">Document:</label>
        @if(!empty($case_documents['default_documents']))
            @foreach(getDocumentFolders($case_documents['default_documents']) as $value)
                {{$value->name}}
            @endforeach
        @endif
    </div>
    
</div>