<div class="CDSPostCaseNotifications-list-view-live-updates-toggle">
    @php 
        $chkid = "chk-" . mt_rand();
    @endphp

    <label class="CDSPostCaseNotifications-list-view-toggle-switch">
        {{--<input 
            type="checkbox" 
            id="{{ $chkid }}"
            @if(isset($data_attr)) {!! $data_attr !!} @endif
            data-value="{{ $value }} {{ $checked ?? '' }}"
            name="{{ $name ?? '' }}" 
            value="{{ $value ?? '' }}"  
            @if(isset($checked) && $value == $checked) checked @endif 
            class="{{ $checkbox_class ?? '' }}"
        >--}}
        
        {!! FormHelper::formCheckbox([
            'name' => $name ?? '',
            'checkbox_class' => $checkbox_class ?? '',
            'id' => $chkid ?? '',
            'data-value' => $value . ' ' . ($checked ?? ''),
            'value' => $value ?? '',
            'checked' => isset($checked) && $value == $checked,
            'data_attr' => isset($data_attr) ? $data_attr : null
        ]) !!}

        <span class="CDSPostCaseNotifications-list-view-toggle-slider"></span>
    </label>
    
    @if(isset($label))
        <span class="CDSPostCaseNotifications-list-view-toggle-label">
            {{ $label }}
            @if(isset($required) && $required)
                <span class="danger">*</span>
            @endif
        </span>
    @endif
</div>
