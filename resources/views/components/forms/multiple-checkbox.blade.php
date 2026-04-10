<div class="cds-form-container cds-form-checkbox cds-form-multicheckbox">
    <div class="js-form-message">
        <div class="cds-check-box cds-checkbox-flex @if(isset($class)) {{$class }} @endif">
            @if(isset($label))
                <label>{{ $label }}
                    @if(isset($required) && $required)
                        <span class="danger">*</span>
                    @endif
                </label>
            @endif
            
            @foreach($options as $key => $option)
                <label class="checkbox">
                    <!-- Hidden radio input -->
                    <input type="checkbox"
                        name="{{ $name ?? '' }}"
                        @if(isset($events)) {{implode(" ", $events)}} @endif
                        id="@if(isset($custom_radio))@if(isset($id)){{ $id }}@else{{ $option[$value_column]}}@endif @else{{ $name }}-{{ $key}}@endif"
                        value="{{ $option[$value_column] }}"
                        @if(isset($selected) && is_array($selected) && in_array($option[$value_column], $selected)) checked @endif
                        class="radio-input @if(isset($radio_class)) {{$radio_class }} @endif @if(isset($required) && $required) required  @endif ">
                        <span class="checkmark"></span>
                    <!-- Custom radio button label -->
                    <label for="@if(isset($custom_radio))@if(isset($id)){{ $id }}@else{{ $option['key']}}@endif @else{{ $name }}-{{ $key}}@endif">{{ $option[$label_column] }}</label>
                </label>
            @endforeach
        </div>
    </div>
</div>