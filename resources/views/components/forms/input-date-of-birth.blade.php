<div class="cds-form-container mb-4">
    <div class="js-form-message">
        <div class="@if(isset($class)) {{$class }} @endif is-invalid">
            @if(isset($label))
            <label class="dob-label">{{$label}}
                @if(isset($required) && $required)
                <span class="danger">*</span>
                @endif
            </label>
            @endif
            <div class="select-picker cust-date">
                <input @if(isset($readonly) && $readonly) readonly @endif @if(isset($id)) id="{{$id}}" @endif
                    type="text" name="{{$name??''}}" class="form-control dob-input border-line required dob @if(!isset($allow_html) || (isset($allow_html) && $allow_html == false)) html-not-allowed @endif  @if(isset($dob_class)) {{$dob_class }} @endif"
                    placeholder="{{$placeholder??''}} Select Date" value="{{ $value ?? '' }}">
            </div>
        </div>
    </div>
</div>