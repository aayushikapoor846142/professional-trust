<?php

namespace App\Helper;
use App\Models\Category;
use App\Models\Country;
class FormHelper
{
    // Form fields

    public static function formInputText($attrs)
    {
        $view = view('components.forms.input-text',$attrs)->render();
        return $view;
    }
    public static function supportCurrency(){
        $status[] = array('value'=>'CAD','label'=>'CAD',"symbol"=>"$");
        $status[] = array('value'=>'INR','label'=>'INR',"symbol"=>"₹");
        return $status;

    }
    public static function formInputNumber($attrs)
    {
        $view = view('components.forms.input-text',$attrs)->render();
        return $view;
    }

    public static function formInputEmail($attrs)
    {
        $view = view('components.forms.input-email',$attrs)->render();
        return $view;
    }

    public static function formTimepicker($attrs)
    {
        $view = view('components.forms.input-timepicker',$attrs)->render();
        return $view;
    }
    public static function formDatepicker($attrs)
    {
        $view = view('components.forms.input-datepicker',$attrs)->render();
        return $view;
    }
    public static function employmentDatePicker($attrs){
        $view = view('components.forms.input-datepicker',$attrs)->render();
        return $view;
    }

    public static function scheduleDatePicker($attrs){
        $view = view('components.forms.input-datepicker',$attrs)->render();
        return $view;
    }

    
    public static function formInputDob($attrs)
    {
        $view = view('components.forms.input-date-of-birth',$attrs)->render();
        return $view;
    }
    public static function formSelect($attrs)
    {
        $view = view('components.forms.select',$attrs)->render();
        return $view;
    }

    public static function formCheckbox($attrs)
    {
        $view = view('components.forms.checkbox',$attrs)->render();
        return $view;
    }  

    public static function formMultipleCheckbox($attrs)
    {
        $view = view('components.forms.multiple-checkbox',$attrs)->render();
        return $view;
    } 

    public static function formToogleCheckbox($attrs)
    {
        $view = view('components.forms.toogle-checkbox',$attrs)->render();
        return $view;
    }  
    public static function formRadio($attrs)
    {
        $view = view('components.forms.radio',$attrs)->render();
        return $view;
    }
    public static function formRadioSelect($attrs)
    {
        $view = view('components.forms.radio',$attrs)->render();
        return $view;
    }
    public static function formPhoneNo($attrs)
    {
        $view = view('components.forms.input-phoneno',$attrs)->render();
        return $view;
    }
    public static function formEditor($attrs)
    {
        $view = view('components.forms.editor',$attrs)->render();
        return $view;
    }
    public static function formTextarea($attrs)
    {
        $view = view('components.forms.textarea',$attrs)->render();
        return $view;
    }
    public static function formFile($attrs)
    {
        $view = view('components.forms.input-file',$attrs)->render();
        return $view;
    }
    
    public static function formSubmitButton($attrs)
    {
        $view = view('components.forms.submit-button',$attrs)->render();
        return $view;
    }
    public static function formCanceltButton($attrs)
    {
        $view = view('components.forms.cancel-button',$attrs)->render();
        return $view;
    }
    public static function formPassordText($attrs)
  {
      $view = view('components.forms.input-password',$attrs)->render();
      return $view;
  }
    public static function formInputUrl($attrs)
    {
        $view = view('components.forms.input-url',$attrs)->render();
        return $view;
    }
    public static function formDropzone($attrs)
    {
        $view = view('components.forms.dropzone',$attrs)->render();
        return $view;
    }

    // custom page static dropdown array
    public static function customPage(){
        $status[] = array('value'=>'kbase01-our-process','label'=>'kbase01-our-process');
        $status[] = array('value'=>'kbase01-proactive-alerts-and-public-awareness-system','label'=>'kbase01-proactive-alerts-and-public-awareness-system');
        $status[] = array('value'=>'kbase01-punjab','label'=>'kbase01-punjab');
        $status[] = array('value'=>'kbase01-uae-uap-profiling','label'=>'kbase01-uae-uap-profiling');
        $status[] = array('value'=>'kbase02','label'=>'kbase02');
        $status[] = array('value'=>'uae-corporate-categories','label'=>'uae-corporate-categories');
        $status[] = array('value'=>'uap-individuals-categories','label'=>'uap-individuals-categories');
        $status[] = array('value'=>'uap-uae-email-notification-process','label'=>'uap-uae-email-notification-process');
        return $status;
    }

    //eligible type static dropdown
       public static function eligibleType(){
        $status[] = array('value'=>'group_eligible','label'=>'Group Eligible');
        $status[] = array('value'=>'normal_eligible','label'=>'Normal Eligible');
        return $status;

    }
    public static function msgSettings(){
        $status[] = array('value'=>'anyone','label'=>'Anyone');
        $status[] = array('value'=>'nobody','label'=>'Nobody');
        $status[] = array('value'=>'my_contacts','label'=>'My Contacts');

        return $status;

    }


    public static function accountStatus(){
        $status[] = array('value'=>'active','label'=>'Active');
        $status[] = array('value'=>'inactive','label'=>'Inactive');
        $status[] = array('value'=>'suspend','label'=>'Suspend');
        $status[] = array('value'=>'pending','label'=>'Pending');
        $status[] = array('value'=>'draft','label'=>'Draft');
        return $status;

    }
    //
    
    public static function getRiskLevel(){
        foreach(getRiskLevel() as $val){
          $status[] = array('value'=>$val,'label'=>$val);

        }
        return $status;

    }
    
    
    
    public static function infoAnonymous(){
        $status[] = array('value'=>'Immigration fraud','label'=>'Immigration fraud');
        $status[] = array('value'=>'no','label'=>'No');
        return $status;

    }
    public static function quickTipscategory(){
          
        $status[] = array('value'=>'Immigration fraud','label'=>'Immigration fraud');
        $status[] = array('value'=>'Visa fraud','label'=>'Visa fraud');
        $status[] = array('value'=>'Job Offer fraud','label'=>'Job Offer fraud');
        $status[] = array('value'=>'Human trafficing','label'=>'Human trafficing');
        $status[] = array('value'=>'Illegal Status','label'=>'Illegal Status');
        $status[] = array('value'=>'Marriage fraud','label'=>'Marriage fraud');
        $status[] = array('value'=>'Document fraud','label'=>'Document fraud');
        $status[] = array('value'=>'Other','label'=>'Other');

        return $status;

    }
     public static function getStatus(){
        $status[] = array('value'=>'1','label'=>'Active');
        $status[] = array('value'=>'0','label'=>'Inactive');
        return $status;

    }

      // pubish status 
      public static function pubishStatus(){
        $status[] = array('value'=>'1','label'=>'Publish');
        $status[] = array('value'=>'0','label'=>'Unpublish');
        return $status;
    }

      // link type
      public static function linkType(){
        $status[] = array('value'=>'internal','label'=>'Internal');
        $status[] = array('value'=>'external','label'=>'External');
        $status[] = array('value'=>'static','label'=>'Static');
        return $status;
    }

    // gender
    public static function gender(){
        $status[] = array('value'=>'male','label'=>'Male');
        $status[] = array('value'=>'female','label'=>'Female');
        return $status;
    }
    // register/professional Choose Options
    public static function chooseOptions(){
        $status[] = array('value'=>'new_signup','label'=>'New Professional Signup');
        $status[] = array('value'=>'claim_profile','label'=>'Claim Profile');
        $status[] = array('value'=>'report_profile','label'=>'Report Profile');
        return $status;
    }
    public static function selectCategory(){

    $categories = Category::all();

    // Initialize an empty array for the options
    $status = [];

    // Add dynamic options from the database
        foreach ($categories as $category) {
            $status[] = [
                'value' => $category->id, // Use ID or any unique field as the value
                'label' => $category->name, // Use the category name or relevant field for the label
            ];
        }
    return $status;

    }
    // term & condtion
    public static function checkTermCond(){
        $status[] = array('value'=>'1','label'=>'By submitting this form, you accept our');
        return $status;
    }
    // three radio btn
    public static function selectAppointmentMode(){
        $status[] = array('value'=>'online','label'=>'Online');
        $status[] = array('value'=>'onsite','label'=>'Onsite');
        return $status;
    }
    public static function selectThreeGender(){
        $status[] = array('value'=>'male','label'=>'Male');
        $status[] = array('value'=>'female','label'=>'Female');
        $status[] = array('value'=>'no_identity','label'=>'Don\'t want to identify');
        return $status;
    }

      // seelct role 
      public static function selectRole(){
        $status[] = array('value'=>'manager','label'=>'Manager');
        $status[] = array('value'=>'investigator','label'=>'Investigator');
        $status[] = array('value'=>'data-analyst','label'=>'Data analyst');
        return $status;
    }
    /* --- SelectCountry --- */
    public static function formSelectCountry($attributes = []) {
        $name = $attributes['name'] ?? '';
        $label = $attributes['label'] ?? null;
        $class = $attributes['class'] ?? '';
        $required = $attributes['required'] ?? false;
        $options = $attributes['options'] ?? [];
        $value_column = $attributes['value_column'] ?? 'value';
        $label_column = $attributes['label_column'] ?? 'label';
        $selected = $attributes['selected'] ?? null;
        $is_multiple = $attributes['is_multiple'] ?? false;

        // Start building the HTML for the select element
        $select = '<div class="js-form-message">';
        $select .= '<div class="select-wrapper' . ($class ? ' ' . $class : '') . '">';
        
        if ($label) {
            $select .= '<label>' . $label;
            if ($required) {
                $select .= '<span class="danger">*</span>';
            }
            $select .= '</label>';
        }

        $select .= '<div class="select-custom form-control' . ($required ? ' required' : '') . '">';
        $select .= '<select name="' . $name . '"' . ($is_multiple ? ' multiple' : '') . '>';

        // Add the default "Select" option
        $select .= '<option value="">Select Country</option>';

        // Loop through the options
        foreach ($options as $option) {
            $selected_attr = ($selected == $option->$value_column) ? ' selected' : '';
            $select .= '<option value="' . $option->$value_column . '"' . $selected_attr . '>' . $option->$label_column . '</option>';
        }

        $select .= '</select>';
        $select .= '</div>';
        $select .= '</div>';
        $select .= '</div>';

        return $select;
    }

    /* */
    public static function groupType(){
        $status[] = array('value'=>'private','label'=>'Private');
        $status[] = array('value'=>'public','label'=>'Public');
        return $status;
    }
    /* */
    
    
    public static function selectTimeDurationType(){
        $status[] = array('value'=>'Minutes','label'=>'minutes');
        $status[] = array('value'=>'Hours','label'=>'hours');
        return $status;
    }
    public static function ownerType(){
        $status[] = array('value'=>'Self Employed','label'=>'Self Employed');
        $status[] = array('value'=>'Employed','label'=>'Employed');
        return $status;
    }
    /* */
    public static function ownerCompanyType(){
        $status[] = array('value'=>'Sole proprietorship','label'=>'Sole proprietorship');
        $status[] = array('value'=>'Partnership','label'=>'Partnership');
        $status[] = array('value'=>'Private limited company','label'=>'Private limited company');
        $status[] = array('value'=>'Limited liability partnership (LLP)','label'=>'Limited liability partnership (LLP)');
        $status[] = array('value'=>'Limited Liability Company (LLC)','label'=>'Limited Liability Company (LLC)');
        $status[] = array('value'=>'Other','label'=>'Other');
        return $status;
    }
    public static function get_Staffroles(){
        $status[] = array('value'=>'client','label'=>'client');
        $status[] = array('value'=>'professional','label'=>'professional');
        $status[] = array('value'=>'manager','label'=>'manager');
        $status[] = array('value'=>'investigator','label'=>'investigator');
        $status[] = array('value'=>'data-analyst','label'=>'data-analyst');
        return $status;
    }

    public static function sendInvitationType(){
        $status[] = array('value'=>'send-individual','label'=>'Send Individual');
        $status[] = array('value'=>'send-csv','label'=>'Send Csv');
        return $status;
    }

     //articale static dropdown
    public static function articleStatus(){
        $status[] = array('value'=>'public','label'=>'Public');
        $status[] = array('value'=>'private','label'=>'Private');
        $status[] = array('value'=>'inactive','label'=>'Inactive');
        return $status;
    }

    //send request of cases status
    public static function requestStatus(){
        $status[] = array('value'=>'pending','label'=>'Pending');
        $status[] = array('value'=>'in-complete','label'=>'Incomplete');
        $status[] = array('value'=>'cancelled','label'=>'Cancelled');
        $status[] = array('value'=>'complete','label'=>'Complete');
        return $status;
    }

      //send request of cases type
      public static function requestType(){
        $status[] = array('value'=>'document-request','label'=>'Document Request');
        $status[] = array('value'=>'information-request','label'=>'Information Request');
        $status[] = array('value'=>'payment-request','label'=>'Payment Request');
        $status[] = array('value'=>'assesment-form-request','label'=>'Assesment Form Request');
        return $status;
    }

     //send request of cases type
    function subStageType(){
        $status[] = array('value'=>'fill-form','label'=>'Fill Form');
        $status[] = array('value'=>'case-document','label'=>'Case Document');
        $status[] = array('value'=>'payment','label'=>'Payment');
        return $status;
    }

    public static function getLocationStatus(){
        $status[] = array('value'=>'active','label'=>'Active');
        $status[] = array('value'=>'inactive','label'=>'Inactive');
        return $status;

    }

    public static function getTimezone(){
        $timezones = array();
        foreach(\DateTimeZone::listIdentifiers() as $timezone){
            $timezones[] = array("label"=>$timezone,"value"=>$timezone);
        }
        return $timezones;
    }

    public static function getFeedSettings(){
        $option[] = array('value'=>'public','label'=>'Public');
        $option[] = array('value'=>'connections','label'=>'Connections');
        $option[] = array('value'=>'followers','label'=>'Followers');
        return $option;

    }

    public static function caseDocumentFolderOption(){
        $option[] = array('value'=>'predefined','label'=>'Predefined');
        $option[] = array('value'=>'new','label'=>'New');
        return $option;
    }

    public static function signatureType(){
        $status[] = array('value'=>'Manually','label'=>'Manually');
        $status[] = array('value'=>'Digital','label'=>'Digital');
        return $status;
    }

    public static function ticketCommentStatus(){           
        $jobType[] = array('value'=>'open','label'=>'Mark as Open');
        $jobType[] = array('value'=>'in_progress','label'=>'Mark as In Progress');
        $jobType[] = array('value'=>'waiting_for_customer','label'=>'Mark as Waiting for Customer');
        $jobType[] = array('value'=>'resolved','label'=>'Mark as Resolved');
        $jobType[] = array('value'=>'closed','label'=>'Mark as Closed');
        return $jobType;
    }

    public static function formType(){
        $status[] = array('value'=>'single_form','label'=>'Single Form');
        $status[] = array('value'=>'step_form','label'=>'Step Form');
        return $status;
    }

    public static function selectCountry(){
       $countries = Country::all();
       $status = [];
       foreach($countries as $country){
        $status[] = array('value'=>$country->id,'label'=>$country->name);
       }
       return $status;
    }
}