@if($records->isNotEmpty())
   
    
    @foreach($records as $record)
        @php
            $isSelected = in_array($record->id, $parent_service ?? []);
        @endphp
        <div class="CDSDashboardProfessionalServices02-option-card {{$record->name}} {{ $isSelected ? 'CDSDashboardProfessionalServices02-selected CDSDashboardProfessionalServices02-disabled' : '' }}"
             @unless($isSelected)
                 onclick="toggleSubservice('{{ $record->unique_id }}')"
             @endunless
             style="{{ $isSelected ? 'pointer-events: none; opacity: 0.6;' : '' }}">
            <div class="CDSDashboardProfessionalServices02-option-checkbox"></div>
            <div class="CDSDashboardProfessionalServices02-option-info">
                <h4>{{ $record->name }}</h4>
                <p>Temporary work authorization</p>
            </div>
        </div>
    @endforeach
@else
    <span class="text-danger">Service not available</span>
@endif

<script>
    $('.btn-subpathway-search').on('click', function () {
        var searchVal = $('#sub-pathway-search').val().trim().toLowerCase();

        $('.CDSDashboardProfessionalServices02-option-card').each(function () {
            var classList = $(this).attr('class').toLowerCase();

            if (classList.includes(searchVal)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });


$('.btn-subpathway-clear').on('click', function () {
    $('#sub-pathway-search').val('');
    $('.CDSDashboardProfessionalServices02-option-card').show();
});

</script>