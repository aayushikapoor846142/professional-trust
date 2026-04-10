@if(!empty($uploadedDocs))
    <h4>Default Documents</h4>
    <ul>
        @foreach($uploadedDocs['default_documents'] as $key => $filename)
            <li>
                {{getRequestDefaultDocument($key)->name}}
                <a href="{{ baseUrl('case-with-professionals/download-documents') }}?case_id={{ $case_id }}&file={{ trim($filename) }}" target="_blank">
                    Download
                </a>
            </li>
        @endforeach
    </ul>

    <h4>Custom Documents</h4>
    <ul>
        @foreach($uploadedDocs['custom_documents'] as $key => $filename)
            <li>
                {{getRequestDocument($key)->name}}
                <a href="{{ baseUrl('case-with-professionals/download-documents') }}?case_id={{ $case_id }}&file={{ trim($filename) }}" >
                    Download
                </a>
            </li>
        @endforeach
    </ul>
@endif
