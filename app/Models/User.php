<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\BaseModel;
//use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AssociateAddresses;
use App\Models\AssociateBillingDetails;
use App\Models\AssociateDetails;
use App\Models\AssociateDocuments;
use App\Models\AssociateEducation;
use App\Models\AssociateService;
use App\Models\AutoLoginToken;
use App\Models\CaseModel;
use App\Models\CaseComment;
use App\Models\CdsProfessionalCompany;
use App\Models\CdsProfessionalDocuments;
use App\Models\CdsProfessionalLicense;
use App\Models\Chat;
use App\Models\ChatGroup;
use App\Models\ChatInvitation;
use App\Models\ChatMessage;
use App\Models\ChatMessageRead;
use App\Models\ChatNotification;
use App\Models\ChatRequest;
use App\Models\ClaimProfile;
use App\Models\CompanyLocations;
use App\Models\CompanySetting;
use App\Models\DiscussionBoard;
use App\Models\DiscussionBoardComment;
use App\Models\DomainVerify;
use App\Models\DraftChatMessage;
use App\Models\EvidenceComments;
use App\Models\Feeds;
use App\Models\FeedComments;
use App\Models\FeedFavourite;
use App\Models\FeedLikes;
use App\Models\Follow;
use App\Models\FormReply;
use App\Models\GroupJoinRequest;
use App\Models\GroupMembers;
use App\Models\GroupMessages;
use App\Models\GroupMessageReaction;
use App\Models\GroupMessagesRead;
use App\Models\Invoice;
use App\Models\MemberInDiscussion;
use App\Models\MessageCentreReaction;
use App\Models\MessageSettings;
use App\Models\OtherProfessionalDetail;
use App\Models\PaymentTransaction;
use App\Models\Professional;
use App\Models\ProfessionalService;
use App\Models\ProfessionalServicePrice;
use App\Models\ProfessionalSite;
use App\Models\ProfessionalSubServices;
use App\Models\ReviewsInvitations;
use App\Models\SendForms;
use App\Models\StaffUser;
use App\Models\SubscriptionInvoiceHistory;
use App\Models\UserDetails;
use App\Models\UserLoginActivity;
use App\Models\UserSubscriptionHistory;
use Illuminate\Support\Facades\Hash;
use App\Models\UserPrivacySettings;
use App\Models\ProfessionalJoiningRequest;

class User extends Authenticatable
{
    //
    use HasApiTokens, HasFactory,Notifiable,SoftDeletes;
// Billable, 
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
     public function getRelatedProfessionalUniqueId()
    {
        if ($this->role === 'professional') {
            return $this->unique_id;
        }

        $professionalId = StaffUser::where('user_id', $this->id)->value('added_by');

        return User::where('id', $professionalId)->value('unique_id');
    }
     public function getRelatedProfessionalId()
    {
        if ($this->role === 'professional') {
            return $this->id;
        }

        $professionalId = StaffUser::where('user_id', $this->id)->value('added_by');

        return $professionalId;
    }

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'last_activity',
        'is_login',
        'first_name',
        'last_name',
        'country_code',
        'phone_no',
        'name',
        'role',
        'email',
        'password',
        'token',
        'stripe_id',
        'timezone',
        'banner_image',
        'social_connect',
        'provider_id',
        'provider'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    static function deleteRecord($id)
    {
        deleteRecordAndFolder($id, 'user');
        $record = User::where("unique_id", $id)->first();
        UserDetails::where("user_id", $record->id)->delete();
        if (!$record) {
            return;
        }

        $userId = $record->id;
        // Delete related records from all models
        AssociateAddresses::where('user_id', $userId)->delete();
        AssociateBillingDetails::where('user_id', $userId)->delete();
        AssociateDetails::where('user_id', $userId)->delete();
        AssociateDocuments::where('user_id', $userId)->delete();
        AssociateEducation::where('user_id', $userId)->delete();
        AssociateService::where('added_by', $userId)->delete();
        AutoLoginToken::where('user_id', $userId)->delete();
        Cases::where('added_by', $userId)->delete();
        CaseComment::where('added_by', $userId)->delete();
        CdsProfessionalCompany::where('user_id', $userId)->delete();
        CdsProfessionalDocuments::where('user_id', $userId)->delete();
        CdsProfessionalLicense::where('user_id', $userId)->delete();
        Chat::where('user1_id', $userId)->orWhere('user2_id', $userId)->delete();
        ChatGroup::where('added_by', $userId)->delete();
        ChatInvitation::where('added_by', $userId)->delete();
        ChatMessage::where('sent_by', $userId)->delete();
        ChatMessageRead::where('receiver_id', $userId)->delete();
        ChatNotification::where('user_id', $userId)->delete();
        ChatRequest::where('sender_id', $userId)->orWhere('receiver_id', $userId)->delete();
        ClaimProfile::where('added_by', $userId)->delete();
        CompanyLocations::where('user_id', $userId)->delete();
        CompanySetting::where('updated_by', $userId)->delete();
        DiscussionBoard::where('added_by', $userId)->delete();
        DiscussionBoardComment::where('added_by', $userId)->delete();
        DomainVerify::where('user_id', $userId)->delete();
        DraftChatMessage::where('user_id', $userId)->delete();
        EvidenceComments::where('added_by', $userId)->delete();
        Feeds::where('added_by', $userId)->delete();
        FeedComments::where('added_by', $userId)->delete();
        FeedFavourite::where('user_id', $userId)->delete();
        FeedLikes::where('added_by', $userId)->delete();
        Follow::where('follower_id', $userId)->orWhere('followee_id', $userId)->delete();
        FormReply::where('user_id', $userId)->delete();
        GroupJoinRequest::where('requested_by', $userId)->delete();
        GroupMembers::where('user_id', $userId)->delete();
        GroupMessages::where('user_id', $userId)->delete();
        GroupMessageReaction::where('added_by', $userId)->delete();
        GroupMessagesRead::where('user_id', $userId)->delete();
        Invoice::where('user_id', $userId)->delete();
        MemberInDiscussion::where('member_id', $userId)->delete();
        MessageCentreReaction::where('added_by', $userId)->delete();
        MessageSettings::where('user_id', $userId)->delete();
        OtherProfessionalDetail::where('added_by', $userId)->delete();
        PaymentTransaction::where('user_id', $userId)->delete();
        ProfessionalServices::where('user_id', $userId)->delete();
        ProfessionalServicePrice::where('added_by', $userId)->delete();
        ProfessionalSite::where('added_by', $userId)->delete();
        ProfessionalSubServices::where('user_id', $userId)->orWhere('added_by', $userId)->delete();
        ReviewsInvitations::where('added_by', $userId)->delete();
        SendForms::where('user_id', $userId)->delete();
        StaffUser::where('user_id', $userId)->delete();
        SubscriptionInvoiceHistory::where('user_id', $userId)->delete();
        UserDetails::where('user_id', $userId)->delete();
        UserLoginActivity::where('user_id', $userId)->delete();
        UserSubscriptionHistory::where('user_id', $userId)->delete();
        StaffUser::where('user_id', $userId)->delete();

        User::where("unique_id", $id)->delete();
    }


    static function restoreRecord($id)
    {
        // Restore the main User record
        $user = User::withTrashed()->where("unique_id", $id)->first();

        if (!$user) {
            return;
        }

        $user->restore();
        $userId = $user->id;

        // Restore related records
        AssociateAddresses::withTrashed()->where('user_id', $userId)->restore();
        AssociateBillingDetails::withTrashed()->where('user_id', $userId)->restore();
        AssociateDetails::withTrashed()->where('user_id', $userId)->restore();
        AssociateDocuments::withTrashed()->where('user_id', $userId)->restore();
        AssociateEducation::withTrashed()->where('user_id', $userId)->restore();
        AssociateService::withTrashed()->where('added_by', $userId)->restore();
        AutoLoginToken::withTrashed()->where('user_id', $userId)->restore();
        Cases::withTrashed()->where('added_by', $userId)->restore();
        CaseComment::withTrashed()->where('added_by', $userId)->restore();
        CdsProfessionalCompany::withTrashed()->where('user_id', $userId)->restore();
        CdsProfessionalDocuments::withTrashed()->where('user_id', $userId)->restore();
        CdsProfessionalLicense::withTrashed()->where('user_id', $userId)->restore();
        Chat::withTrashed()->where('user1_id', $userId)->orWhere('user2_id', $userId)->restore();
        ChatGroup::withTrashed()->where('added_by', $userId)->restore();
        ChatInvitation::withTrashed()->where('added_by', $userId)->restore();
        ChatMessage::withTrashed()->where('sent_by', $userId)->restore();
        ChatMessageRead::withTrashed()->where('receiver_id', $userId)->restore();
        ChatNotification::withTrashed()->where('user_id', $userId)->restore();
        ChatRequest::withTrashed()->where('sender_id', $userId)->orWhere('receiver_id', $userId)->restore();
        ClaimProfile::withTrashed()->where('added_by', $userId)->restore();
        CompanyLocations::withTrashed()->where('user_id', $userId)->restore();
        CompanySetting::withTrashed()->where('updated_by', $userId)->restore();
        DiscussionBoard::withTrashed()->where('added_by', $userId)->restore();
        DiscussionBoardComment::withTrashed()->where('added_by', $userId)->restore();
        DomainVerify::withTrashed()->where('user_id', $userId)->restore();
        DraftChatMessage::withTrashed()->where('user_id', $userId)->restore();
        EvidenceComments::withTrashed()->where('added_by', $userId)->restore();
        Feeds::withTrashed()->where('added_by', $userId)->restore();
        FeedComments::withTrashed()->where('added_by', $userId)->restore();
        FeedFavourite::withTrashed()->where('user_id', $userId)->restore();
        FeedLikes::withTrashed()->where('added_by', $userId)->restore();
        Follow::withTrashed()->where('follower_id', $userId)->orWhere('followee_id', $userId)->restore();
        FormReply::withTrashed()->where('user_id', $userId)->restore();
        GroupJoinRequest::withTrashed()->where('requested_by', $userId)->restore();
        GroupMembers::withTrashed()->where('user_id', $userId)->restore();
        GroupMessages::withTrashed()->where('user_id', $userId)->restore();
        GroupMessageReaction::withTrashed()->where('added_by', $userId)->restore();
        GroupMessagesRead::withTrashed()->where('user_id', $userId)->restore();
        Invoice::withTrashed()->where('user_id', $userId)->restore();
        MemberInDiscussion::withTrashed()->where('member_id', $userId)->restore();
        MessageCentreReaction::withTrashed()->where('added_by', $userId)->restore();
        MessageSettings::withTrashed()->where('user_id', $userId)->restore();
        OtherProfessionalDetail::withTrashed()->where('added_by', $userId)->restore();
        PaymentTransaction::withTrashed()->where('user_id', $userId)->restore();
        ProfessionalServices::withTrashed()->where('user_id', $userId)->restore();
        ProfessionalServicePrice::withTrashed()->where('added_by', $userId)->restore();
        ProfessionalSite::withTrashed()->where('added_by', $userId)->restore();
        ProfessionalSubServices::withTrashed()->where('user_id', $userId)->orWhere('added_by', $userId)->restore();
        ReviewsInvitations::withTrashed()->where('added_by', $userId)->restore();
        SendForms::withTrashed()->where('user_id', $userId)->restore();
        StaffUser::withTrashed()->where('user_id', $userId)->restore();
        SubscriptionInvoiceHistory::withTrashed()->where('user_id', $userId)->restore();
        UserDetails::withTrashed()->where('user_id', $userId)->restore();
        UserLoginActivity::withTrashed()->where('user_id', $userId)->restore();
        UserSubscriptionHistory::withTrashed()->where('user_id', $userId)->restore();
        StaffUser::withTrashed()->where('user_id', $userId)->restore();
        User::onlyTrashed()->where("id", $user->id)->restore();
    }

    protected static function boot()
    {
        parent::boot();
    
        // Before creating
        static::creating(function ($object) {
            $object->unique_id = randomNumber();
            // $object->user_location = json_encode(detectUserLocation());
            $object->token = randomString();
        });
    
        // After created
        static::created(function ($user) {
            $apiData = [
                'user_id'      => $user->unique_id,
                'first_name'   => $user->first_name ?? '',
                'last_name'    => $user->last_name ?? '',
                'email'        => $user->email ?? '',
                'country_code' => $user->country_code ?? '',
                'phone_no'     => $user->phone_no ?? '',
                'role'         => $user->role ?? '',
                'added_by'     => $user->added_by ?? \Auth::id(),
                'updated_by'   => $user->updated_by ?? \Auth::id(),
                'gender'       => $user->gender ?? '',
                'password'     => $user->password ?? '',
            ];
          
            securityApi("save-user", $apiData);
        });
    
        // Before updating
        static::updating(function ($object) {
            if (empty($object->unique_id) || $object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }

            if (empty($object->token) || $object->token == '') {
                $object->token = randomString();
            }
            
        //  $object->user_location = json_encode(detectUserLocation());
           
        });
       
        // After updated — also call same API
        static::updated(function ($user) {
            $apiData = [
                'user_id'      => $user->unique_id,
                'first_name'   => $user->first_name ?? '',
                'last_name'    => $user->last_name ?? '',
                'email'        => $user->email ?? '',
                'country_code' => $user->country_code ?? '',
                'phone_no'     => $user->phone_no ?? '',
                'role'         => $user->role ?? '',
                'added_by'     => $user->added_by ?? \Auth::id(),
                'updated_by'   => $user->updated_by ?? \Auth::id(),
                'gender'       => $user->gender ?? '',
                'password'     => $user->password ?? '',
            ];
           
            securityApi("save-user", $apiData);
        });
    }

    public function userDetail()
    {
        return $this->hasOne('App\Models\UserDetails', 'user_id');
    }
    public function associateDetail()
    {
        return $this->hasOne('App\Models\AssociateDetails', 'user_id');
    }

    public function associateFeesTransaction()
    {
        return $this->hasOne('App\Models\PaymentTransaction', 'user_id')->where("transaction_for", "associate_application_fees")->orderBy("id", "desc");
    }

    public function cdsCompanyDetail()
    {
        return $this->hasOne('App\Models\CdsProfessionalCompany','user_id');
    }

    public function status()
    {
        return $this->belongsTo(User::class, 'status');
    }

    public function personalLocation()
    {
        return $this->hasOne('App\Models\CompanyLocations','user_id')->where('type_label','personal');
    }
    public function companyLocation()
    {
        return $this->hasMany('App\Models\CompanyLocations','user_id')->where('type_label','company');
    }

    public function associateLocation()
    {
        return $this->hasMany('App\Models\AssociateAddresses', 'user_id');
    }

    public function associateDocuments()
    {
        return $this->hasMany('App\Models\AssociateDocuments', 'user_id');
    }

    public function associateEducation()
    {
        return $this->hasMany('App\Models\AssociateEducation', 'user_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followee_id')
                        ->withPivot('unique_id') // Add the pivot column
                        ->withTimestamps();

    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followee_id', 'follower_id');
    }
    public function chatRequestsAsSenderOrReceiver()
    {
    return $this->hasMany('App\Models\ChatRequest', 'sender_id')
                ->orWhere('receiver_id', $this->id);
    }

    public function hasAccessToModule($moduleSlug)
    {
        if ($this->membershipPlan) {
            return $this->membershipPlan->modules->pluck('slug')->contains($moduleSlug);
        }
        return false;
    }
    public function UserSubscriptionHistory()
    {
        return $this->hasOne('App\Models\UserSubscriptionHistory', 'user_id', 'id')->latest('created_at');
    }
    
    public function getActivesubscriptionHistory()
    {
        return $this->hasOne('App\Models\UserSubscriptionHistory', 'user_id', 'id')->where('subscription_status', 'active');
    }
    
    public function userPrivacySettings()
    {
        return $this->hasMany(UserPrivacySettings::class, 'user_id', 'id');
    }

    public function userPrivacySettingForFeed()
    {
        return $this->hasOne(UserPrivacySettings::class, 'user_id', 'id')
            ->whereHas('modulePrivacyOptions', function ($query) {
                $query->where('action_slug', 'mention-permission');
            });
    }    

    public function userPrivacySettingForHideFeed()
    {
        return $this->hasOne(UserPrivacySettings::class, 'user_id', 'id')
            ->whereHas('modulePrivacyOptions', function ($query) {
                $query->where('action_slug', 'hide-post');
            });
    }  

    
     public function staffUser()
    {
        return $this->hasOne('App\Models\StaffUser', 'user_id', 'id');
    }

    public function professionalLicense()
    {
        return $this->hasOne(CdsProfessionalLicense::class, 'user_id');
    }

   

    public function primaryCompanyAddress(){
        return $this->hasOne(CompanyLocations::class, 'user_id')->where('is_primary',1);
    }

    public function memberInDiscussions()
{
    return $this->hasMany(MemberInDiscussion::class, 'member_id');
}

public function discussionBoardComments()
{
    return $this->hasMany(DiscussionBoardComment::class, 'added_by');
}
public function isStaffUser(){
    return $this->hasOne(StaffUser::class, 'user_id');
}

public function passwordHistories()
{
    return $this->hasMany(PasswordHistory::class);
}

// Function to check password history
public function isPasswordInHistory($newPassword)
{
    return $this->passwordHistories()->pluck('password')->contains(function ($oldPassword) use ($newPassword) {
        return Hash::check($newPassword, $oldPassword);
    });
}



public function storePasswordHistory()
{
    $this->passwordHistories()->create([
        'password' => $this->password,
    ]); 
    }

    public function userPrivacySettingForGroup()
    {
        return $this->hasOne(UserPrivacySettings::class, 'user_id', 'id')
            ->whereHas('modulePrivacyOptions', function ($query) {
                $query->where('action_slug', 'allow-to-add-in-group');
            });
    }

    public function caseWithProfessional()
    {
        return $this->belongsTo(CaseWithProfessionals::class, 'professional_id', 'id');
    }

    public function  userConnections()
    {
        return $this->hasMany(FeedsConnection::class, 'user_id');
    }

    public function cdsCompanyDetails()
    {
        return $this->hasMany('App\Models\CdsProfessionalCompany','user_id');
    }

    public function clientTotalPostedCases()
    {
        return $this->hasMany('App\Models\Cases','added_by');
    }

    public function bankingDetails()
    {
        return $this->hasMany(UserBankingDetails::class);
    }

    public function activeBankingDetail()
    {
        return $this->hasOne(UserBankingDetails::class)->where('is_active', true);
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }
    
    public function professionalJoiningRequest()
    {
        return $this->belongsTo(ProfessionalJoiningRequest::class, 'id', 'associate_id');
    }
    
    public function associateAgreement()
    {
        return $this->belongsTo(ProfessionalAssociateAgreement::class, 'id', 'associate_id');
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
}

    