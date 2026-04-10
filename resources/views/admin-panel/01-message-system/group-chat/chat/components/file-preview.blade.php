@props(['attachments'])

@php
    $attachmentArray = explode(',', $attachments);
@endphp

@foreach($attachmentArray as $attachment)
    @php
        $fileExtension = pathinfo($attachment, PATHINFO_EXTENSION);
        $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        $isVideo = in_array(strtolower($fileExtension), ['mp4', 'avi', 'mov', 'wmv']);
        $isAudio = in_array(strtolower($fileExtension), ['mp3', 'wav', 'ogg']);
        $isDocument = in_array(strtolower($fileExtension), ['pdf', 'doc', 'docx', 'txt']);
    @endphp
    
    <div class="file-attachment">
        @if($isImage)
            <div class="image-preview">
                <img src="{{ groupChatDirUrl($attachment, 'r') }}" 
                     alt="Image attachment" 
                     onclick="previewFile('{{ $attachment }}', 'image')">
            </div>
        @elseif($isVideo)
            <div class="video-preview">
                <video controls>
                    <source src="{{ groupChatDirUrl($attachment, 'r') }}" type="video/{{ $fileExtension }}">
                    Your browser does not support the video tag.
                </video>
            </div>
        @elseif($isAudio)
            <div class="audio-preview">
                <audio controls>
                    <source src="{{ groupChatDirUrl($attachment, 'r') }}" type="audio/{{ $fileExtension }}">
                    Your browser does not support the audio tag.
                </audio>
            </div>
        @elseif($isDocument)
            <div class="document-preview">
                <div class="document-icon">
                    <i class="fa-solid fa-file-{{ $fileExtension === 'pdf' ? 'pdf' : 'word' }}"></i>
                </div>
                <div class="document-info">
                    <span class="document-name">{{ basename($attachment) }}</span>
                    <span class="document-size">{{ $fileExtension }}</span>
                </div>
                <a href="{{ groupChatDirUrl($attachment, 'r') }}" 
                   target="_blank" 
                   class="download-btn">
                    <i class="fa-solid fa-download"></i>
                </a>
            </div>
        @else
            <div class="file-preview">
                <div class="file-icon">
                    <i class="fa-solid fa-file"></i>
                </div>
                <div class="file-info">
                    <span class="file-name">{{ basename($attachment) }}</span>
                    <span class="file-size">{{ $fileExtension }}</span>
                </div>
                <a href="{{ groupChatDirUrl($attachment, 'r') }}" 
                   target="_blank" 
                   class="download-btn">
                    <i class="fa-solid fa-download"></i>
                </a>
            </div>
        @endif
    </div>
@endforeach

<script>
function previewFile(fileName, type) {
    if (type === 'image') {
        // Show image in modal
        const modal = document.createElement('div');
        modal.className = 'file-preview-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <span class="close">&times;</span>
                <img src="${groupChatDirUrl(fileName, 'r')}" alt="Preview">
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Close modal
        modal.querySelector('.close').onclick = () => modal.remove();
        modal.onclick = (e) => {
            if (e.target === modal) modal.remove();
        };
    }
}

function groupChatDirUrl(fileName, type) {
    // This function should be defined globally or passed from PHP
    return `${baseUrl}group-chat/${fileName}`;
}
</script> 