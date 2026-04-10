<div class="cds-form-container mb-4">
    <div class="js-form-message">
        @if(isset($label))
        <div>
            <label>{{ $label }}
                @if(isset($required) && $required)
                    <span class="danger">*</span>
                @endif
            </label>
        </div>
        @endif
        <div class="radio-group @if(isset($class)) {{$class }} @endif">
            
            @foreach($options as $key => $option)
                <div class="form-check">
                    <!-- Hidden radio input -->
                    <input type="radio"
                        name="{{ $name ?? '' }}"
                        @if(isset($events)) {{implode(" ", $events)}} @endif
                        id="@if(isset($custom_radio))@if(isset($id)){{ $id }}@else{{ $option[$value_column]}}@endif @else{{ $name }}-{{ $key}}@endif"
                        value="{{ $option[$value_column] }}"
                        @if(isset($selected) && $option[$value_column] == $selected) checked @endif
                        class="radio-input @if(isset($radio_class)) {{$radio_class }} @endif @if(isset($required) && $required) required  @endif ">
                    <!-- Custom radio button label -->
                    <label for="@if(isset($custom_radio))@if(isset($id)){{ $id }}@else{{ $option['key']}}@endif @else{{ $name }}-{{ $key}}@endif">{{ $option[$label_column] }}</label>
                </div>
            @endforeach
        </div>
    </div>
</div>