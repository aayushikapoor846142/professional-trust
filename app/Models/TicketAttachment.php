<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unique_id',
        'ticket_id',
        'reply_id',
        'user_id',
        'file_name',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
        'description'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reply()
    {
        return $this->belongsTo(TicketReply::class, 'reply_id');
    }

    /**
     * Get the download URL for the attachment
     */
    public function getDownloadUrlAttribute()
    {
        return ticketDirUrl($this->file_name);
    }

    /**
     * Get the file icon based on file type
     */
    public function getFileIconAttribute()
    {
        $extension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'pdf':
                return 'fa-file-pdf';
            case 'doc':
            case 'docx':
                return 'fa-file-word';
            case 'xls':
            case 'xlsx':
                return 'fa-file-excel';
            case 'ppt':
            case 'pptx':
                return 'fa-file-powerpoint';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'bmp':
            case 'svg':
                return 'fa-file-image';
            case 'mp3':
            case 'wav':
            case 'ogg':
                return 'fa-file-audio';
            case 'mp4':
            case 'avi':
            case 'mov':
                return 'fa-file-video';
            case 'zip':
            case 'rar':
            case '7z':
                return 'fa-file-archive';
            case 'txt':
                return 'fa-file-text';
            default:
                return 'fa-file';
        }
    }
} 