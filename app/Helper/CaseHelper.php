<?php
use App\Models\CaseWithProfessionals;
use Illuminate\Support\Str;
use App\Models\CaseFolders;
use App\Models\DocumentsFolder;
use App\Models\CaseDocumentCommentRead;
use Carbon\Carbon;
use App\Models\Cases;

function caseInfo($case_id)
{
    return CaseWithProfessionals::where("unique_id", $case_id)
    ->with(['services', 'subServices','subServicesTypes','userAdded','retainAgreements'])->first();
}

function getLetterName($first_name,$last_name)
{
    $name = $first_name.' '.$last_name;
    return $initials = Str::upper(Str::substr($name, 0, 1) . Str::substr(Str::after($name, ' '), 0, 1));
}

function getRequestDocument($document_id)
{
    return CaseFolders::where("id",$document_id)->first();
}

function getRequestDefaultDocument($document_id)
{
    return DocumentsFolder::where("id",$document_id)->first();
}
// Add these helper functions to your helpers file or controller

/**
 * Get file icon based on file extension
 */
function getFileIcon($fileName) {
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $icons = [
        // Images
        'jpg' => '🖼️',
        'jpeg' => '🖼️',
        'png' => '🖼️',
        'gif' => '🖼️',
        'svg' => '🖼️',
        'webp' => '🖼️',
        
        // Documents
        'pdf' => '📄',
        'doc' => '📝',
        'docx' => '📝',
        'txt' => '📃',
        'rtf' => '📝',
        
        // Spreadsheets
        'xls' => '📊',
        'xlsx' => '📊',
        'csv' => '📊',
        
        // Presentations
        'ppt' => '📊',
        'pptx' => '📊',
        
        // Archives
        'zip' => '🗜️',
        'rar' => '🗜️',
        '7z' => '🗜️',
        'tar' => '🗜️',
        'gz' => '🗜️',
        
        // Code
        'html' => '💻',
        'css' => '💻',
        'js' => '💻',
        'php' => '💻',
        'py' => '💻',
        'json' => '💻',
        'xml' => '💻',
        
        // Video
        'mp4' => '🎥',
        'avi' => '🎥',
        'mov' => '🎥',
        'wmv' => '🎥',
        'flv' => '🎥',
        
        // Audio
        'mp3' => '🎵',
        'wav' => '🎵',
        'flac' => '🎵',
        'aac' => '🎵',
        'ogg' => '🎵',
    ];
    
    return $icons[$extension] ?? '📎'; // Default file icon
}

/**
 * Get file icon class based on file extension
 */
function getFileIconClass($fileName) {
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $classes = [
        // Images
        'jpg' => 'image',
        'jpeg' => 'image',
        'png' => 'image',
        'gif' => 'image',
        'svg' => 'image',
        'webp' => 'image',
        
        // Documents
        'pdf' => 'pdf',
        'doc' => 'doc',
        'docx' => 'doc',
        'txt' => 'doc',
        'rtf' => 'doc',
        
        // Spreadsheets
        'xls' => 'excel',
        'xlsx' => 'excel',
        'csv' => 'excel',
        
        // Others
        'zip' => 'archive',
        'rar' => 'archive',
        '7z' => 'archive',
    ];
    
    return $classes[$extension] ?? 'file'; // Default class
}

/**
 * Format file size in human readable format
 */
function formatFileSize($bytes) {
    if ($bytes == 0) return '0 Bytes';
    
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log($k));
    
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

/**
 * Get initials from name
 */
function getInitials($firstName, $lastName = '') {
    $initials = '';
    
    if ($firstName) {
        $initials .= strtoupper(substr($firstName, 0, 1));
    }
    
    if ($lastName) {
        $initials .= strtoupper(substr($lastName, 0, 1));
    }
    
    return $initials ?: 'U'; // Default to 'U' if no name
}

function documentUnreadComment($document_id)
{   
    return CaseDocumentCommentRead::where('case_document_id',$document_id)->where('user_id',auth()->user()->id)->count();
}


function getTimeAgo($date)
{
    $timestamp = Carbon::parse($date);
    $now = Carbon::now();
    $diffInSeconds = $now->diffInSeconds($timestamp);

    if ($diffInSeconds < 60) {
        return 'just now';
    }

    $diffInMinutes = $now->diffInMinutes($timestamp);
    if ($diffInMinutes < 60) {
        return $diffInMinutes . 'm ago';
    }

    $diffInHours = $now->diffInHours($timestamp);
    if ($diffInHours < 24) {
        return $diffInHours . 'h ago';
    }

    $diffInDays = $now->diffInDays($timestamp);
    return $diffInDays . 'd ago';
}

function countCase($type)
{
    $records = Cases::with([
                'services',
                'submitProposal',
                'professionalFavouriteCase' => function ($query) {
                    $query->where('user_id', auth()->id());
                }
            ])
            ->where("status","posted");
            

        if($type == "unread_case"){
            $records->whereDoesntHave('ProfessionalCaseViewed', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }

        if($type == "viewed_case"){
            $records->whereHas('ProfessionalCaseViewed', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }

        if($type == "proposal_sent"){
            $records->whereHas('submitProposal', function ($query) {
                $query->where('added_by', auth()->id());
            });
        }

        if($type == "favourite"){
            $records->whereHas('professionalFavouriteCase', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }
        
    $records = $records->get()->count();

    return $records;

}