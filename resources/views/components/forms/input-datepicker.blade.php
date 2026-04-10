<div class="cds-form-container">
    <div class="js-form-message">
        <div class="form-group form-floating  @if(isset($class)) {{$class }} @endif is-invalid">
            @if(isset($label))
            <label>{{$label}}
                @if(isset($required) && $required)
                <span class="danger">*</span>
                @endif
            </label>
            @endif
            <input @if(isset($id)) {{ $disabled??'' }} id="{{$id}}" @endif type="text" name="{{$name??''}}" class="form-control border-line @if(!isset($allow_html) || (isset($allow_html) && $allow_html == false)) html-not-allowed @endif  @if(isset($required) && $required) required  @endif" value="{{ $value ?? '' }}" placeholder="Select Date ">
            
        </div>
    </div>
</div>