<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','name','category_id','article_type_id','slug','description','summary','images','reading_time','added_by','status','files','show_on_home',"is_featured"];
    protected $encodedAttributes = ['unique_id','name','category_id','article_type_id','slug','description','summary','images','reading_time','added_by','status','files'];

    public function scopeVisibleToUser($query, $userId)
    {
        $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: show their own + their professional's records
            return $query->where(function ($q) use ($userId, $professionalId) {
                $q->where('added_by', $userId)
                ->orWhere('added_by', $professionalId);
            });
        } else {
            // Professional: show their own + all their staff's records
            $staffIds = \App\Models\StaffUser::where('added_by', $userId)->pluck('user_id');

            return $query->where(function ($q) use ($userId, $staffIds) {
                $q->where('added_by', $userId);

                if ($staffIds->isNotEmpty()) {
                    $q->orWhereIn('added_by', $staffIds);
                }
            });
        }
    }

    public function isEditableBy($userId)
    {
        $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: can only edit their own records (not professional's)
            return $this->added_by == $userId;
        } else {
            // Professional: can edit their own and their staff's records
            $staffIds = \App\Models\StaffUser::where('added_by', $userId)->pluck('user_id')->toArray();
            return $this->added_by == $userId || in_array($this->added_by, $staffIds);
        }
    }
    
    public function articleType()
    {
        return $this->belongsTo('App\Models\ArticleType', 'article_type_id');
    }
    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }
      public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    static function deleteRecord($id)
    {
        Article::where("id", $id)->delete();
    }

    static function removeFiles($file_name)
    {
        $path = articleDir();
        mediaDeleteApi($path,$file_name);
        // if(file_exists(articleDir('r').$file_name)){
        //     unlink(articleDir('r').$file_name);
        // }
        // if(file_exists(filename: articleDir('m').$file_name)){
        //     unlink(articleDir('m').$file_name);
        // }
        // if(file_exists(articleDir('t').$file_name)){
        //     unlink(articleDir('t').$file_name);
        // }
    }

    public function seoDetails()
    {
        return $this->hasOne('App\Models\SeoDetails', 'reference_id', 'id')->where("module_type", "article");
    }
}
