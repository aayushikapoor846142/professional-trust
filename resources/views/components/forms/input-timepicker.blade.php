<div class="cds-form-container">
    <div class="js-form-message">
        <div class="@if(isset($class)) {{$class }} @endif is-invalid">
            @if(isset($label))
            <label class="dob-label">{{$label}}
                @if(isset($required) && $required)
                <span class="danger">*</span>
                @endif
            </label>
            @endif
            <input @if(isset($id)) id="{{$id}}" @endif type="text" name="{{$name??''}}" class="form-control input-times border-line @if(!isset($allow_html) || (isset($allow_html) && $allow_html == false)) html-not-allowed @endif  @if(isset($required) && $required) required  @endif" value="{{ $value ?? '' }}" placeholder="Select Time ">
            
        </div>
    </div>
</div>