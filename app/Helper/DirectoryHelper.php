<?php

use App\Models\CaseWithProfessionals;

if (!function_exists("companyLogoDirUrl")) {
    function companyLogoDirUrl($file_name, $size = 'r')
    {
        $dir = companyLogoDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;

        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}
if (!function_exists("companyBannerDirUrl")) {
    function companyBannerDirUrl($file_name, $size = 'r')
    {
        $dir = companyBannerDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;

        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}
if (!function_exists("userDirUrl")) {
    function userDirUrl($file_name, $size = 'r')
    {

        $dir = userDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}
if (!function_exists("companyLogoDir")) {
    function companyLogoDir()
    {

        $dir = 'company_logo';
        return $dir;
    }
}
if (!function_exists("companyBannerDir")) {
    function companyBannerDir()
    {

        $dir = 'company_banner';
        return $dir;
    }
}
if (!function_exists("caseDocumentsDirUrl")) {
    function caseDocumentsDirUrl($file_name, $case_id, $size = '')
    { // r = regular t = thumb m = medium

        $dir = 'case-documents/' . $case_id;
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;

    }
}

if (!function_exists("caseDocumentsDir")) {

    function caseDocumentsDir($case_id)
    {

        $dir = 'cases/' . $case_id;
        return $dir;
    }
}


if (!function_exists("categoryDir")) {
    function categoryDir($unique_id = '', $role = 'user')
    {

        $dir = 'category';
        return $dir;
    }
}

if (!function_exists("categoryDirUrl")) {
    function categoryDirUrl($file_name, $size = 'r')
    {

        $dir = categoryDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("faqCategoryDir")) {
    function faqCategoryDir($unique_id = '', $role = 'user')
    {

        $dir = 'faqcategory';
        return $dir;
    }
}

if (!function_exists("faqCategoryDirUrl")) {
    function faqCategoryDirUrl($file_name, $size = 'r')
    {

        $dir = faqCategoryDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("immigrationTypeDir")) {
    function immigrationTypeDir($unique_id = '', $role = 'user')
    {

        $dir = 'immigrationType';
        return $dir;
    }
}

if (!function_exists("immigrationTypeDirUrl")) {
    function immigrationTypeDirUrl($file_name, $size = 'r')
    {

        $dir = immigrationTypeDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("immigrationServiceDir")) {
    function immigrationServiceDir($unique_id = '', $role = 'user')
    {

        $dir = 'immigrationService';
        return $dir;
    }
}

if (!function_exists("immigrationServiceDirUrl")) {
    function immigrationServiceDirUrl($file_name, $size = 'r')
    {

        $dir = immigrationServiceDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("feedDir")) {
    function feedDir($unique_id = '', $role = 'user')
    {

        $dir = 'feeds';
        return $dir;
    }
}

if (!function_exists("feedDirUrl")) {
    function feedDirUrl($file_name, $size = 'r')
    {

        $dir = feedDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("discussionDir")) {
    function discussionDir($unique_id = '', $role = 'user')
    {

        $dir = 'discussions';
        return $dir;
    }
}

if (!function_exists("discussionDirUrl")) {
    function discussionDirUrl($file_name, $size = 'r')
    {
        $dir = discussionDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("discussionCommentDir")) {
    function discussionCommentDir($file_thumbnail = false)
    {
        $dir = 'discussion-comments';
        if ($file_thumbnail) {
            $dir = 'discussion/file-thumbnail';
        }
        return $dir;
    }
}

if (!function_exists("discussionCommentDirUrl")) {
    function discussionCommentDirUrl($file_name, $size = 'r', $file_thumbnail = false)
    {
        $dir = discussionCommentDir($file_thumbnail);
        $token = apiKeys('MEDIA_TOKEN');
        if ($file_thumbnail) {
            $file_name = pathinfo($file_name, PATHINFO_FILENAME) . '.jpg';
        }
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("commentDir")) {
    function commentDir($file_thumbnail = false)
    {
        $dir = 'comments';
        if ($file_thumbnail) {
            $dir = 'comments/file-thumbnail';
        }
        return $dir;
    }
}

if (!function_exists("commentDirUrl")) {
    function commentDirUrl($file_name, $size = 'r', $file_thumbnail = false)
    {
        $dir = commentDir($file_thumbnail);
        if ($file_thumbnail) {
            $file_name = pathinfo($file_name, PATHINFO_FILENAME) . '.jpg';
        }
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}
if (!function_exists("userBannerDirUrl")) {
    function userBannerDirUrl($file_name, $size = 'r')
    {

        $dir = userBannerDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("userBannerDir")) {
    function userBannerDir($unique_id = '', $role = 'user')
    {

        $dir = 'userBanner';
        return $dir;
    }
}


if (!function_exists("professionalBarcodeDirUrl")) {
    function professionalBarcodeDirUrl($file_name, $size = 'r', $unique_id)
    {

        $dir = 'barcode/' . $unique_id;

        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("professionalDir")) {
    function professionalDir($domain = '')
    {
        if ($domain == '') {
            $domain = \Session::get("subdomain");
        }
        $dir = public_path("uploads/professional/" . $domain);

        return $dir;
    }
}
if (!function_exists("claimDir")) {
    function claimDir()
    {
        $dir = public_path("uploads/professional/");
        return $dir;
    }
}
if (!function_exists("claimDirUrl")) {
    function claimDirUrl()
    {
        $dir = "public/uploads/professional/claim";


        return $dir;
    }
}

if (!function_exists("uapDir")) {
    function uapDir()
    {
        $dir = public_path("uploads/uap_professional/");
        return $dir;
    }
}

if (!function_exists("prfessionalDocumentDir")) {
    function prfessionalDocumentDir()
    {
        $dir = public_path("uploads/professional_document/");
        return $dir;
    }
}

if (!function_exists("claimProfileDir")) {
    function claimProfileDir()
    {
        $dir = public_path("uploads/claim_profile/");
        return $dir;
    }
}

if (!function_exists("uapDirUrl")) {
    function uapDirUrl()
    {
        $dir = "public/uploads/uap_professional";

        return $dir;
    }
}
if (!function_exists("articleDirUrl")) {
    function articleDirUrl($file_name, $size = '')
    { // r = regular t = thumb m = medium


        $dir = articleDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}


if (!function_exists("groupChatDirUrl")) {
    function groupChatDirUrl($file_name, $size = '', $file_thumbnail = false)
    { // r = regular t = thumb m = medium


        $dir = groupChatDir($file_thumbnail);
        if ($file_thumbnail) {
            $file_name = pathinfo($file_name, PATHINFO_FILENAME) . '.jpg';
        }
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}
if (!function_exists("groupChatDir")) {
    function groupChatDir($file_thumbnail = false)
    {
        $dir = 'group-chat';
        if ($file_thumbnail) {
            $dir = 'group-chat/file-thumbnail';
        }
        return $dir;
    }
}

if (!function_exists("chatDirUrl")) {

    function chatDirUrl($file_name, $size = '', $file_thumbnail = false)
    { // r = regular t = thumb m = medium
        $dir = chatDir($file_thumbnail);
        $token = apiKeys('MEDIA_TOKEN');
        if ($file_thumbnail) {
            $file_name = pathinfo($file_name, PATHINFO_FILENAME) . '.jpg';
        }
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}



if (!function_exists("chatDir")) {
    function chatDir($file_thumbnail = false)
    {
        $dir = 'chat';
        if ($file_thumbnail) {
            $dir = 'chat/file-thumbnail';
        }
        return $dir;
    }
}

if (!function_exists("ourInitiativesDirUrl")) {
    function ourInitiativesDirUrl($file_name, $size = '')
    { // r = regular t = thumb m = medium


        $dir = ourInitiativesDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("ourInitiativesDir")) {
    function ourInitiativesDir()
    {


        $dir = 'our-initiatives';
        return $dir;
    }
}

if (!function_exists("articleDir")) {
    function articleDir()
    {

        $dir = 'articles';
        return $dir;
    }
}
if (!function_exists("newsDirUrl")) {

    function newsDirUrl($file_name, $size = '')
    { // r = regular t = thumb m = medium


        $dir = newsDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;

    }
}
if (!function_exists("newsDir")) {

    function newsDir()
    {
        $dir = 'news';
        return $dir;
    }
}
if (!function_exists("mediaDirUrl")) {

    function mediaDirUrl($file_name, $size = '')
    { // r = regular t = thumb m = medium
        $dir = mediaDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("mediaDir")) {

    function mediaDir()
    {
        $dir = 'media';
        return $dir;
    }
}


if (!function_exists("guideDirUrl")) {
    function guideDirUrl($file_name, $size = 'r')
    {

        $dir = guideDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}
if (!function_exists("guideDir")) {
    function guideDir()
    {
        $dir = 'guide';
        // if($size=="t"){
        //     $dir = public_path("uploads/guide/thumb");
        // }elseif($size=="m"){
        //     $dir = public_path("uploads/guide/medium");
        // }else{
        //     $dir = public_path("uploads/guide");
        // }
        return $dir;
    }
}
if (!function_exists("userDir")) {
    function userDir($unique_id = '', $role = 'user')
    {

        $dir = 'profilePicture';
        return $dir;
    }
}

if (!function_exists("investigatorEvidencesDir")) {
    function investigatorEvidencesDir()
    {
        $dir = public_path("uploads/investigator_evidences/");
        return $dir;
    }
}

if (!function_exists("customImageDir")) {

    function customImageDir()
    {
        $destinationPath = public_path("uploads/images");
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        return $destinationPath;
    }
}
if (!function_exists("customImageUrl")) {

    function customImageUrl($file = '')
    {
        $destinationPath = public_path("uploads/images");
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        if ($file != '' && file_exists($destinationPath . "/" . $file)) {
            $url = url("public/uploads/images/" . $file);
        } else {
            $url = url("assets/images/default.png");
        }

        return $url;
    }
}


if (!function_exists("investigatorEvidencesDir")) {
    function investigatorEvidencesDir()
    {
        $dir = public_path("uploads/investigator_evidences/");
        return $dir;
    }
}
if (!function_exists("uapViolationDir")) {
    function uapViolationDir()
    {
        $dir = public_path("uploads/uap_violation/");
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }
}


if (!function_exists("uapAssociateDir")) {
    function uapAssociateDir($image, $unique_id = '')
    {
        if ($unique_id == '') {
            $unique_id = \Auth::user()->unique_id;
        }

        $dir = preg_replace('/\b' . preg_quote('/api', '/') . '\b/', '', apiKeys('investigate_url')) . "/public/uploads/uap-associate/" . $image;
        return $dir;
    }
}

if (!function_exists("uapProfessionalImageDir")) {
    function uapProfessionalImageDir($image)
    {
        $dir = preg_replace('/\b' . preg_quote('/api', '/') . '\b/', '', apiKeys('investigate_url')) . "/public/uploads/uap-professionals/" . $image;
        return $dir;
    }
}

if (!function_exists("discussionCategoryDir")) {
    function discussionCategoryDir($unique_id = '', $role = 'user')
    {
        $dir = 'category';
        return $dir;
    }
}

if (!function_exists("discussionCategoryDirUrl")) {
    function discussionCategoryDirUrl($file_name, $size = 'r')
    {
        $dir = discussionCategoryDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}
if (!function_exists("announcementTypeDirUrl")) {

    function announcementTypeDirUrl($file_name, $size = '')
    { // r = regular t = thumb m = medium
        $dir = announcementTypeDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}
if (!function_exists("announcementTypeDir")) {

    function announcementTypeDir()
    {
        $dir = 'announcement-type';
        return $dir;
    }
}

if (!function_exists("otherFileDirUrl")) {
    function otherFileDirUrl($file_name, $size = 'r')
    {
        $dir = otherFileDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("seoDetailsDir")) {
    function seoDetailsDir($role = 'user', $unique_id = '', )
    {
        $dir = 'seo-details';
        return $dir;
    }
}

if (!function_exists("seoDetailsDirUrl")) {
    function seoDetailsDirUrl($file_name, $size = 'r')
    {
        $dir = seoDetailsDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("initiatorDirUrl")) {

    function initiatorDirUrl($file_name, $size = '')
    { // r = regular t = thumb m = medium
        $dir = initiatorDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("initiatorDir")) {

    function initiatorDir()
    {
        $dir = 'initiators';
        return $dir;
    }
}


if (!function_exists("siteImageDir")) {

    function siteImageDir()
    {
        $dir = 'images';
        return $dir;
    }
}

if (!function_exists("siteImageDirUrl")) {

    function siteImageDirUrl($file_name, $size = 'm')
    {

        $dir = siteImageDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("mainTrustvisoryUrl")) {
    function mainTrustvisoryUrl()
    {
        $trustvisoryUrl = "";
        if (request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1') {
            $trustvisoryUrl = env('TRUSTVISORY_URL');
        } else {

            $trustvisoryUrl = "https://trustvisory.com";
        }

        return $trustvisoryUrl;
    }
}


if (!function_exists("clientTrustvisoryUrl")) {
    function clientTrustvisoryUrl()
    {
        $trustvisoryUrl = "";
        if (request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1') {
            $trustvisoryUrl = env('CLIENT_URL');

        } else {
            $trustvisoryUrl = "https://client.trustvisory.com";
        }

        return $trustvisoryUrl;
    }
}

if (!function_exists("supportTrustvisoryUrl")) {
    function supportTrustvisoryUrl()
    {
        $trustvisoryUrl = "";
        if (request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1') {
            $trustvisoryUrl = env('SUPPORT_URL');

        } else {
            $trustvisoryUrl = "https://support.trustvisory.com";
        }

        return $trustvisoryUrl;
    }
}

if (!function_exists("professionalTrustvisoryUrl")) {
    function professionalTrustvisoryUrl()
    {
        $trustvisoryUrl = "";
        if (request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1') {
            $trustvisoryUrl = env('PROFESSIONAL_URL');

        } else {
            $trustvisoryUrl = "https://professionals.trustvisory.com";
        }

        return $trustvisoryUrl;
    }
}


if (!function_exists("associateTrustvisoryUrl")) {

    function associateTrustvisoryUrl()
    {
        $trustvisoryUrl = "";
        if (request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1') {
            $trustvisoryUrl = env('ASSOCIATE_URL');

        } else {
            $trustvisoryUrl = "https://associate.trustvisory.com";
        }

        return $trustvisoryUrl;
    }
}

if (!function_exists("otherFileDir")) {
    function otherFileDir($unique_id = '', $role = 'user')
    {
        $dir = 'other';
        return $dir;
    }
}

if (!function_exists("otherFileDirUrl")) {
    function otherFileDirUrl($file_name, $size = 'r')
    {
        $dir = otherFileDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}
if(!function_exists("companyDir")){
    function companyDir($unique_id = '', $role='user'){

        $dir = 'company-images/logo';
        return $dir;
    }
}


if (!function_exists("caseDocumentCommentDir")) {
    function caseDocumentCommentDir($document_id)
    {
        $dir = 'case-document-comments/'.$document_id;
        return $dir;
    }
}

if (!function_exists("caseDocumentCommentDirUrl")) {
    function caseDocumentCommentDirUrl($document_id,$file_name, $size = 'r')
    {
        $dir = caseDocumentCommentDir($document_id);
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}

if (!function_exists("assesstmentFormDir")) {
    function assesstmentFormDir($form_id)
    {
        $dir = 'assesstment-form/'.$form_id;
        return $dir;
    }
}

if (!function_exists("assesstmentFormDirUrl")) {
    function assesstmentFormDirUrl($form_id,$file_name, $size = 'r')
    {
        $dir = assesstmentFormDir($form_id);
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL') . 'get-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;

        if ($size != '') {
            $url .= "&s=" . $size;
        }
        return $url;
    }
}


if(!function_exists("ticketDir")){
    function ticketDir($unique_id = '', $role='user'){

        $dir = 'tickets';
        return $dir;
    }
}

if(!function_exists("ticketDirUrl")){
    function ticketDirUrl($file_name,$size='r'){
 
        $dir = ticketDir();
        $token = apiKeys('MEDIA_TOKEN');
        $url = apiKeys('MEDIA_UPLOAD_URL').'get-file-url?file='. $file_name."&file_path=".$dir."&t=".$token;
        if($size != ''){
            $url .="&s=".$size;
        }
        return $url;
    }
}