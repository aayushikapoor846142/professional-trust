@extends('layouts.app')
@section('content')
<!-- texteditor css -->

<!-- <link rel="stylesheet" href="{{url('assets/plugins/dropzone/dropzone.min.css')}}"> -->

<main>
    <div class="formElements">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="py-2 text-center"><h1>Form Elements</h1></div>
                </div>
                <form>
                    <div class="row">
                        <div class="col-xl-4 col-md-6 col-lg-6">
                            {!! FormHelper::formInputText([
                                'name'=>"first_name",
                                'id'=>"first_name",
                                'input_class'=>"",
                                "label"=> "Text input",
                                "value"=> '',
                                "required"=>true,
                            ])!!}
                        </div>
                        <div class="col-xl-4 col-md-6 col-lg-6">
                            {!! FormHelper::formInputEmail([
                                'name'=>"email",
                                'id'=>"email",
                                "label"=>"Email input",
                                "required"=>true,
                                'events'=>['oninput=validateEmail(this)', 'onblur=validateEmail(this)']
                            ]) !!}
                        </div>
                        <div class="col-xl-4 col-md-6 col-lg-6 mb-4 mb-md-0">
                            {!! FormHelper::formPassordText([
                                'name'=>"password",
                                "id"=>"password",
                                "label"=>"Password",
                                "required"=>true,
                            ]) !!}
                        </div>
                        <div class="col-xl-4 col-md-6 col-lg-6">
                            {!! FormHelper::formInputNumber([
                                'name' => 'number',
                                'id' => 'number',
                                'label' => 'Number input',
                                "required" => true,
                                'value' => '',
                                'input_class' => '',
                                'events'=>['oninput=validateDigit(this)','onblur=validateDigit(this)'],
                            ]) !!}
                        </div>
                        <div class="col-xl-4 col-md-6 col-lg-6">
                            {!! FormHelper::formPhoneNo([
                                'name' => "phone_no",
                                'id' => "phone_no",
                                'country_code_name' => "country_code",
                                "label" => "Phone Number",
                                "value" => '',
                                "default_country_code"=> '+1',
                                "required" => true,
                                'events'=>['oninput=validatePhoneInput(this)']]
                            )!!}
                        </div>
                        <div class="col-xl-4 col-md-6 col-lg-6">
                            <div class="dob-block mb-4 mb-md-0">
                                {!! FormHelper::formDatepicker([
                                    'name' => 'date_of_birth',
                                    'id' => 'date_of_birth',
                                    'class' => 'select-date',
                                    'label' => 'Date of Birth',
                                    'value' => '',
                                    'required' => true,
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 col-lg-6">
                            {!! FormHelper::formSelect([
                                'name' => 'select',
                                'id' => 'select',
                                'label' => 'Select input',
                                'class' => 'select2-input ga-country',
                                'options' => FormHelper::selectRole(),
                                'value_column' => 'value',
                                'label_column' => 'label',                                
                                'is_multiple' => false,
                                'required' => true,
                            ]) !!}
                        </div>
                        <div class="col-xl-4 col-md-6 col-lg-6">
                            {!! FormHelper::formSelect([
                                'name' => 'languages[]',
                                'id' => 'languages',
                                'label' => 'Multi Select inputs',
                                'class' => 'select2-input cds-multiselect add-multi', // outer wrapper or label class
                                'select_class' => 'ga-country', // select tag class
                                'options' => [
                                ['name' => 'English'],
                                ['name' => 'Hindi'],
                                ['name' => 'French'],
                                ['name' => 'Spanish'],
                                ['name' => 'Angola'],
                                ['name' => 'Antigua and Barbuda'],
                                    ['name' => 'Argentina'],
                                    ['name' => 'Bahrain'],
                                    ['name' => 'Belize'],
                                    ['name' => 'Botswana'],
                                    ],
                                    'value_column' => 'name',
                                    'label_column' => 'name',
                                    'is_multiple' => true,
                                    "required" => true,
                            ]) !!}
                        </div>                                
                        <div class="col-xl-4 col-md-6 col-lg-6">
                            {!! FormHelper::formInputUrl([
                                'name'=>"url",
                                'id'=>"url",
                                "label"=>"Enter Url",
                                "required"=>true,
                            ]) !!}  
                        </div>                                
                        <div class="col-xl-6">
                            <div class="d-flex gap-2 align-items-center">
                                {!! FormHelper::formCheckbox([
                                    'name' => "checkbox",
                                    'id' => "checkbox",
                                    'value' => 1,
                                    'checked'=> '',
                                    'required' => true
                                ]) !!}
                                <label class="form-check-label" for="checkbox">Checkbox inputs</label>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <label class="form-check-label" for="checkbox">Radio inputs</label>
                            {!! FormHelper::formRadio([
                                'name' => 'settings',
                                'required' => true,
                                'radio_class' => '',                        
                                'options' => FormHelper::selectThreeGender(),
                                'value_column' => 'value',
                                'label_column' => 'label',
                                'id' => 'label',
                                'value' => 'label',
                            ]) !!}
                        </div>
                        <div class="col-xl-12">
                            {!! FormHelper::formTextarea([
                                'name'=>"textarea",
                                'id'=>"textarea",
                                "label"=>"Textarea input",
                                'value'=> '',
                                "required"=>true,
                            ]) !!}                  
                        </div>
                        <div class="col-xl-12">
                            <label class="col-form-label input-label">Text Editor<span class="danger">*</span></label>
                            {!! FormHelper::formTextarea([
                                'name' => 'texteditor',
                                'id' => 'texteditor',
                                'class' => 'cds-texteditor',
                                'textarea_class' => 'noval',
                                'required' =>  true,
                            ]) !!}
                        </div>
                        <div class="col-xl-12">
                            {!! FormHelper::formDropzone([
                                'name' => 'dropzone',
                                'id' => 'dropzone',
                                'class' => 'edit-discussion-media-dropzone',
                                'dropzone_class' => 'dz-images',
                                'required' => true,
                                'max_files' => 6,
                            ]) !!}
                        </div>
                        <div class="col-xl-12">
                            <label class="col-form-label input-label">File Dropzone<span class="danger">*</span></label>
                            {!! FormHelper::formDropzone([
                                'name' => 'proof_of_identify',
                                'id' => 'pf-file-dropzone',
                                'dropzone_class' => 'pf-file-dropzone',
                                'required' => true,
                                'max_files' => 10,
                            ]) !!}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>    
</main>

@endsection
@section('javascript')

<!-- texteditor js -->

<!-- dropzone -->
<!-- <script src="{{ url('assets/plugins/dropzone/dropzone.min.js') }}"></script> -->

<script>
    $(document).ready(function() {        
        dobDatePicker("date_of_birth"); //DOB
        initEditor("texteditor"); //texteditor

        Dropzone.autoDiscover = false;
        var timestamp = "{{time()}}";
        const pfDropzone = new Dropzone("#pf-file-dropzone", {
            url: SITEURL + "/upload-professional-document?_token=" + csrf_token + "&timestamp=" + timestamp + "&document_type=proof_of_identity",
            autoProcessQueue: false, // Prevent automatic upload
            addRemoveLinks: true,
            maxFilesize: 6,
            acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx',
            parallelUploads: 40,
            maxFiles: 60,
        });
        const dropzone = new Dropzone("#dropzone", {
            url: SITEURL + "/upload-professional-document?_token=" + csrf_token + "&timestamp=" + timestamp + "&document_type=proof_of_identity",
            autoProcessQueue: false, // Prevent automatic upload
            addRemoveLinks: true,
            maxFilesize: 6,
            acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx',
            parallelUploads: 40,
            maxFiles: 60,
        });
    });
    
</script>
@endsection
