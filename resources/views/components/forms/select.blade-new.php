{{--
<div class="cds-form-container select-dropdown mb-4">
    <div class="js-form-message">
        <div class="@if(isset($class)) {{$class }} @endif">
            @if(isset($label))
            <label>{{$label}}
                @if(isset($required) && $required)
                <span class="danger">*</span>
                @endif
            </label>
            @endif
            <div class="form-group form-floating">
                <select @if(isset($disabled) && $disabled) disabled  @endif name="{{$name??''}}" @if(isset($events)) {{implode(" ", $events)}} @endif @if(isset($id)) id="{{$id}}" @endif  @if(isset($is_multiple) && $is_multiple) multiple  @endif class="@if(isset($required) && $required) required  @endif @if(isset($select_class)) {{$select_class }} @endif">
                
                @if(isset($is_multiple) && $is_multiple)
                
                    @foreach($options as $option)
                    <option @if(isset($selected) && in_array($option[$value_column],$selected)) selected @endif value="{{$option[$value_column]}}">{{$option[$label_column]}}</option>
                    @endforeach
                @else
                    <option value="">Select Option</option>
                    @foreach($options as $option)
                    <option @if(isset($selected) && $option[$value_column] == $selected) selected    @endif value="{{$option[$value_column]}}">{{$option[$label_column]}}</option>
                    @endforeach
                @endif
                
                </select>
            </div>
        </div>
    </div>
</div>
--}}

<div class="cds-form-container select-dropdown mb-4">
    <div class="js-form-message">
        <div class="@if(isset($class)) {{$class }} @endif">
            @if(isset($label))
            <label>{{$label}}
                @if(isset($required) && $required)
                <span class="danger">*</span>
                @endif
            </label>
            @endif
            <div class="form-group form-floating">
                <select  @if(isset($disabled) && $disabled) disabled  @endif name="{{$name??''}}" @if(isset($events)) {{implode(" ", $events)}} @endif @if(isset($id)) id="{{$id}}" @endif  @if(isset($is_multiple) && $is_multiple) multiple  @endif class="@if(isset($required) && $required) required  @endif @if(isset($select_class)) {{$select_class }} @endif CDSComponents-hidden @if(isset($is_multiple) && $is_multiple) CDSComponents-MultiSelect @else CDSComponents-SingleSelect  @endif">
                    <option value="">Select Option</option>
                    @foreach($options as $option)
                    <option @if(isset($selected) && $option[$value_column] == $selected) selected    @endif value="{{$option[$value_column]}}">{{$option[$label_column]}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>