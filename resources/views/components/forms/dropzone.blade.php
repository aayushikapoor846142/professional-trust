<div class="cds-form-container mb-4">
<div class="js-form-message">
    <div class="my-dropzone @if(isset($class)) {{$class }} @endif is-invalid">
        @if(isset($label))
        <label>{{$label}}
            @if(isset($required) && $required)
            <span class="danger">*</span>
            @endif
        </label>
        @endif
        <div class="fv-row">
            <div class="dropzone  @if(isset($dropzone_class)) {{$dropzone_class}} @endif @if(isset($required) && $required) required  @endif" @if(isset($id)) id="{{$id}}" @endif>
                <div class="dz-message needsclick">
                    <aside class="mb-3">                        
                        <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="44" height="44" rx="22" fill="#F1F1F3"/>
                        <path d="M22 22V31M22 22L26 26M22 22L18 26M13.8824 25C12.7147 23.7255 12 22.014 12 20.1324C12 16.1933 15.132 13 18.9956 13C22.2933 13 25.2243 15.2521 25.9635 18.3829C28.2974 17.5168 30.8781 18.7437 31.7276 21.1232C32.4501 23.1471 31.6876 25.3529 30.0075 26.5" stroke="#212529" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </aside>
                    <div class="upload-body">
                        <h3 class="font16 fw600 mb-0"><u class="fw-medium">Click to upload</u> <span class="gray-txt">or drag and drop files</span></h3>
                        {{--<span class="para">Upload up to 10 files</span>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>