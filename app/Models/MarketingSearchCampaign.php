<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class MarketingSearchCampaign extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $table = 'marketing_search_campaigns';

    protected $fillable = [
        'unique_id',
        'url',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_terms',
        'marketing_search_terms',
    ];

    protected $encodedAttributes =[
        'unique_id',
        'url',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_terms',
        'marketing_search_terms',
    ];

}
