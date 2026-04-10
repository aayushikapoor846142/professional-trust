<div class="cds-form-container mb-4">
<div class="js-form-message">
    <div class="form-group form-floating @if(isset($class)) {{$class }} @endif">
        <input class="form-control border-line @if(!isset($allow_html) || (isset($allow_html) && $allow_html == false)) html-not-allowed @endif   @if(isset($required) && $required) required  @endif @if(isset($numbers_class)) {{$numbers_class }} @endif"  @if(isset($id)) id="{{$id}}" @endif type="text" name="{{$name??''}}" value="{{ $value ?? '' }}" @if(isset($events)) {{implode(" ", $events)}} @endif @if(isset($attributes)) {{$attributes}} @endif>
        @if(isset($label))
        <label>{{$label}}
            @if(isset($required) && $required)
            <span class="danger">*</span>
            @endif
        </label>
        @endif
        <div class="input-group">
            <input @if(isset($readonly) && $readonly) readonly  @endif @if(isset($id)) id="{{$id}}" @endif type="number" name="{{$name??''}}" class="form-control @if(isset($required) && $required) required  @endif" value="{{ $value ?? '' }}" />
        </div>
    </div>
</div>
</div>