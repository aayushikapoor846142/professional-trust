<div class="cds-form-container mb-4">
    <div class="js-form-message ">
        <div class="form-group form-floating @if(isset($class)) {{$class }} @endif">
            <textarea @if(isset($readonly) && $readonly) readonly  @endif class="form-control border-line textarea @if(!isset($allow_html) || (isset($allow_html) && $allow_html == false)) html-not-allowed @endif  @if(isset($required) && $required) required  @endif @if(isset($textarea_class)) {{$textarea_class }} @endif"
                @if(isset($id)) id="{{$id}}" @endif placeholder="Input description..." name="{{$name??''}}"
                 rows="3">{{ $value ?? '' }}</textarea>
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