<div class="cds-form-container mb-4">
<div class="form-group card-box-round @if(isset($class)) {{$class }} @endif">
    @if(isset($label))
    <label>{{$label}}
        @if(isset($required) && $required)
        <span class="danger">*</span>
        @endif
    </label>
    @endif
    <div class="quill-custom">
        <textarea class="editor @if(isset($editor_class)) {{$editor_class }} @endif" @if(isset($id)) id="{{$id}}" @endif name="{{$name??''}}">
        {{ $value ?? '' }}
        </textarea>
    </div>
</div>
</div>