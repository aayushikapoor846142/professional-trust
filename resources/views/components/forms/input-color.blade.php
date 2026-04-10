<div class="cds-form-container mb-4">
<div class="js-form-message ">
    <div class="form-group form-floating @if(isset($class)) {{$class }} @endif">
        <input @if(isset($readonly) && $readonly) readonly  @endif class="form-control border-line   @if(isset($required) && $required) required  @endif" @if(isset($id)) id="{{$id}}" @endif type="color" name="{{$name??''}}" value="{{ $value ?? '' }}" @if(isset($events)) {{implode(" ", $events)}} @endif @if(isset($attributes)) {{$attributes}} @endif>
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
