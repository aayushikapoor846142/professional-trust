
@foreach($records as $record)
@php
    $isSelected = $record->unique_id == $main_service_id;
    $shouldDisable = $main_service_id != 0 && !$isSelected;
@endphp


<div class="CDSDashboardProfessionalServices02-option-card 
            {{ $isSelected ? 'CDSDashboardProfessionalServices02-selected' : '' }} 
            {{ $shouldDisable ? 'CDSDashboardProfessionalServices02-disabled' : '' }}"
     @if(!$shouldDisable)
        onclick="selectPathway('{{ $record->unique_id }}', event)"
     @endif
     style="{{ $shouldDisable ? 'pointer-events: none; opacity: 0.6;' : '' }}">
    
    <div class="CDSDashboardProfessionalServices02-option-checkbox"></div>
    <div class="CDSDashboardProfessionalServices02-option-info">
        <h4>{{ $record->name }}</h4>
        <!-- <p>Education permits</p> -->
    </div>
</div>

@endforeach
