<div class="cds-form-checkbox">
    @php 
    $chkid = "chk-".mt_rand();
    @endphp
    <div class="cds-check-box cds-checkbox-flex @if(isset($class)) {{$class }} @endif">
        @if(isset($label))
            <label>{{ $label }}
                @if(isset($required) && $required)
                    <span class="danger">*</span>
                @endif
            </label>
        @endif        
        <label for="{{$chkid}}" class="checkbox">
            <input id="{{$chkid}}" type="checkbox"
            @if(isset($data_attr)) {{ $data_attr }} @endif
            name="{{ $name ?? '' }}"
            value="{{ $value ?? '' }}"   
            
            class="checkbox @if(isset($checkbox_class)) {{$checkbox_class }} @endif" @if(isset($checked) && $value == $checked) checked    @endif  @if(isset($disabled) && $disabled == "true") disabled @endif>
            <span class="checkmark"></span>
        </label>
    </div>
</div>