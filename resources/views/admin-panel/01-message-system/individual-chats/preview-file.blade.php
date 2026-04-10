<div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-header d-flex justify-content-between">
            <h5 class="modal-title">File Preview</h5>

            <div class="d-flex justify-content-between align-items-center gap-3">
                <a href="{{ url('download-media-file?dir='.chatDir().'&file_name='.$file_name) }}" class="popup-link"
                    data-download="{{ chatDirUrl($file_name, 'r') }}">
                    <i class="fa fa-download fa-lg white"></i>
                </a>


                @if($chatMessage)
                @if($chatMessage->sent_by == auth()->user()->id)
                <a href="javascript:;"
                    onclick="deleteSelectedAttachmentMessage('{{ $chatMessage->unique_id }}', '{{ $file_name }}')">
                    <i class="fa fa-trash white" aria-hidden="true"></i>

                </a>
                @endif
                @endif


                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
        </div>
        <div class="modal-body">
            @php
            $fileExtension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $previewHtml = '';

            if (in_array($fileExtension, ['pdf'])) {
            // PDF Preview

            $previewHtml = '<iframe src="' . htmlspecialchars($fileUrl) . '" name="' . htmlspecialchars($file_name) . '"
                width="100%" height="500px"></iframe>';
            } elseif (in_array($fileExtension, ['doc', 'docx', 'xlsx','xls'])) {
            // Word/Excel Preview
            $previewHtml = '<iframe src="https://view.officeapps.live.com/op/view.aspx?src=' . urlencode($fileUrl) . '"
                width="100%" height="500px"></iframe>';
            } elseif (in_array($fileExtension, ['txt'])) {
            // Text File Preview
            $content = htmlspecialchars(file_get_contents($fileUrl));
            $previewHtml = '
            <pre>' . $content . '</pre>';
            } elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'])) {
            // Image File Preview
            $previewHtml = '<div class="w-100 mx-auto text-center cdspngview"><img src="' . htmlspecialchars($fileUrl) . '"
                    alt="Preview" style="max-width: 100%; max-height: 500px;" class="bgcolor" /></div>';
            } elseif (in_array($fileExtension, ['mp3', 'wav', 'ogg'])) {
            // Audio File Preview
            $previewHtml = '<div class="w-100 mx-auto text-center">
                <audio controls>
                    <source src="' . htmlspecialchars($fileUrl) . '" type="audio/' . $fileExtension . '">
                    Your browser does not support the audio element.
                </audio>
            </div>
            ';
            } elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg'])) {
            // Video File Preview
            $previewHtml = '<div class="w-100 mx-auto text-center">
                <video controls style="max-width: 100%; max-height: 500px;">
                    <source src="' . htmlspecialchars($fileUrl) . '" type="video/' . $fileExtension . '">
                    Your browser does not support the video element.
                </video>
            </div>
            ';
            } else {
            // Unsupported File Format
            $previewHtml = '<div class="text-center">Preview not available.';
                $previewHtml .= '<a href="'.url('download-media-file?dir='.chatDir().'&file_name='.$file_name).'"
                    class="img-fluid" data-download="'.chatDirUrl($file_name, 'r').'">
                    Click to download
                </a></div>';
            }
            @endphp
            {!! $previewHtml !!}
        </div>
    </div>
</div>