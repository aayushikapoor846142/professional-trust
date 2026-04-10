<div class="cds-form-container">
<div class="js-form-message">
    <div class="form-group form-floating wrap-input @if(isset($class)) {{$class }} @endif">
        <input class="form-control border-line password-input @if(!isset($allow_html) || (isset($allow_html) && $allow_html == false)) html-not-allowed @endif  @if(isset($required) && $required) required  @endif @if(isset($password_class)) {{$password_class }} @endif"  @if(isset($id)) id="{{$id}}" @endif type="password" name="{{$name??''}}" value="{{ $value ?? '' }}" @if(isset($events)) {{implode(" ", $events)}} @endif >
        @if(isset($label))
        <label>{{$label}}
            @if(isset($required) && $required)
            <span class="danger">*</span>
            @endif
        </label>
        @endif

        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <button type="button" class="btn-show-pass ico-20 floating-pass-icon">
            <span class="flaticon-visibility eye-pass"></span>
        </button>

        
    </div>
</div>
</div>