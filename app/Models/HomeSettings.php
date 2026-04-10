<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeSettings extends Model
{
    use HasFactory,SoftDeletes;
    // The attributes that are mass assignable.
    protected $table="home_settings";
    protected $fillable = ['unique_id', 'title_1','title_2','title_3','date_1','date_2','date_3','initiative_1_desc',
    'uap_sec_desc_3','uap_sec_desc_2','uap_sec_desc_1','uap_sec_title_3','uap_sec_title_2'
    ,'uap_sec_title_1','initiative_2_desc','initiative_3_desc','initiative_4_desc',
    'initiative_1_title','initiative_2_title','initiative_3_title','initiative_4_title',
    'about_title','about_subtitle','about_desc','about_subtitle_2','updated_by',
    'uap_sec_main_title','uap_sec_main_desc','counter_title','counter_desc'
    ,'uap_sec_sub_title_1', 'uap_sec_sub_title_2','uap_sec_sub_title_3'];
    protected $encodedAttributes = ['unique_id', 'title_1','title_2','title_3','date_1',
    'date_2','initiative_1_desc','initiative_2_desc','initiative_3_desc',
    'initiative_4_desc','initiative_1_title','initiative_2_title','initiative_3_title',
    'initiative_4_title','about_title','about_subtitle','about_desc','about_subtitle_2',
    'uap_sec_desc_3','uap_sec_desc_2','uap_sec_desc_1','uap_sec_title_3','uap_sec_title_2'
    ,'uap_sec_title_1','updated_by','counter_title','counter_desc','uap_sec_sub_title_1',
    'uap_sec_sub_title_2','uap_sec_sub_title_3'];
    static function deleteRecord($id)
    {
        HomeSettings::where("id", $id)->delete();
    }
}
