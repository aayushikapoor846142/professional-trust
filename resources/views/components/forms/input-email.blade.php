<div class="cds-form-container mb-4">
<div class="js-form-message">
    <div class="form-group form-floating @if(isset($class)) {{$class }} @endif">
        <input  @if(isset($readonly) && $readonly) readonly  @endif class="form-control @if(!isset($allow_html) || (isset($allow_html) && $allow_html == false)) html-not-allowed @endif  @if(isset($email_class)){{$email_class}}@endif border-line  @if(isset($required) && $required) required @endif "  @if(isset($id)) id="{{$id}}" @endif type="email" name="{{$name??''}}" value="{{ $value ?? '' }}" @if(isset($events)) {{implode(" ", $events)}} @endif >
        @if(isset($label))
        <label>{{$label}}
            @if(isset($required) && $required)
            <span class="danger">*</span>
            @endif
        </label>
        @endif
    </div>
</div>
</div>