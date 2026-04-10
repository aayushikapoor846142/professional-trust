@php 
$index = mt_rand(1000,9999);
@endphp
<div class="row google-address-area">
    <input type="hidden" name="company_add[{{$index}}][company_location_unique_id]" value="0">
    <div class="col-md-6">
        {!! FormHelper::formInputText([
            'name' => "company_add[$index][address1]",
            "label"=> "Address1",
            'input_class'=>"google-address",
            'value' =>'',
            "required"=>true,
        ])!!}          
    </div>  
    <div class="col-md-6">
        {!! FormHelper::formInputText([
            'name' => "company_add[$index][address2]",
            "label"=> "Address2",
            'input_class'=>"google-address",
            'value' => '',
        ])!!}          
    </div>  
    <div class="col-xl-6 col-md-6 col-lg-6 col-sm-6">
        {!! FormHelper::formInputText([
            'name' => "company_add[$index][city]",
            "label"=> "City",
            'input_class'=>"ga-city",
            'value' => '',
            'events'=>['oninput=validateName(this)'],
            "required"=>true,
        ])!!}
    </div>
    <div class="col-xl-6 col-md-6 col-lg-6 col-sm-6">
        {!! FormHelper::formInputText([
            'name' => "company_add[$index][state]",
            "label"=> "State",
            'input_class'=>"ga-state",
            'value' => '',
            'events'=>['oninput=validateName(this)'],
            "required"=>true,
        ])!!}
    </div>
    <div class="col-xl-6 col-md-6 col-lg-6 col-sm-6">
        {!! FormHelper::formSelect([
            'name' => "company_add[$index][country]",
            'id' => 'country',
            'label' => 'Country',
            'class' => 'select2-input',
            'input_class'=>"ga-country",
            'options' => $countries,
            'value_column' => 'name',
            'label_column' => 'name',
            'selected' => '',
            'is_multiple' => false,
            'required' => true,
        ]) !!}
    </div> 
    <div class="col-xl-12">
        {!! FormHelper::formInputText([
                'name' => "company_add[$index][zipcode]",
                "label"=>"Zip Code",
                'id'=>"zip_code", 
                "value" =>'',
                "required"=>true,
                'input_class'=>"ga-pincode",
                'events'=>['oninput=validateZipCode(this)', 'onblur=validateZipCode(this)']])
        !!}
    </div>
</div>