<div class="cds-form-container select-dropdown mb-4">
    <div class="js-form-message">
        <div class="@if(isset($class)) {{$class }} @endif select2-input cds-multiselect add-multi ">
            @if(isset($label))
            <label>{{$label}}
                @if(isset($required) && $required)
                <span class="danger">*</span>
                @endif
            </label>
            @endif
            <div class="form-group form-floating">
                <select  @if(isset($disabled) && $disabled) disabled  @endif name="{{$name??''}}" @if(isset($events)) {{implode(" ", $events)}} @endif @if(isset($id)) id="{{$id}}" @endif  @if(isset($is_multiple) && $is_multiple) multiple  @endif class="@if(isset($required) && $required) required  @endif @if(isset($select_class)) {{$select_class }} @endif CDSComponents-hidden">
                    <option value="">Select Option</option>
                    @foreach($options as $option)
                    <option @if(isset($selected) && $option[$value_column] == $selected) selected    @endif value="{{$option[$value_column]}}">{{$option[$label_column]}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>