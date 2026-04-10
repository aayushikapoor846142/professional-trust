@if(count($records) > 0)
  
    @foreach($records as $key => $record)
    <div class="CdsCustomForm-list-item position-relative" data-type="{{ $record->form_type == 'step_form' ? 'step' : 'single' }}" data-unique-id="{{ $record->unique_id }}">
        <div class="CdsCustomForm-item-content">
            <h3 class="CdsCustomForm-item-title">{{ $record->name ?? 'Untitled Form' }}</h3>
            <div class="CdsCustomForm-item-meta">
                <span class="CdsCustomForm-meta-item">
                    @if($record->form_type == 'step_form')
                        <span class="CdsCustomForm-form-type-badge CdsCustomForm-step">🏷️ Step Form</span>
                    @else
                        <span class="CdsCustomForm-form-type-badge CdsCustomForm-single">🏷️ Single Form</span>
                    @endif
                </span>
                <span class="CdsCustomForm-meta-item">
                    📅 {{ \Carbon\Carbon::parse($record->created_at)->diffForHumans() }}
                </span>
            </div>
        </div>
        
        <div class="CdsCustomForm-item-actions">
            <a class="CdsTYButton-btn-primary" title="Preview" onclick="showPopup('{{ baseUrl('forms/predefined-render-form/'.$record->unique_id) }}')">
                Preview
            </a>
            <a class="CdsTYButton-btn-primary" title="Save templates"  onclick="confirmUseTemplateAction(this)"  data-href="{{baseUrl('forms/save-predefined-template/'.$record->unique_id)}}">Use template</a>
        </div>
    </div>
    @endforeach
@else
    <div class="CdsCustomForm-empty-state">
        <div class="CdsCustomForm-empty-icon">📋</div>
        <h3>No forms found</h3>
        <p>@if(request()->get('search')) Try adjusting your search criteria @else Create your first form to get started @endif</p>
    </div>
@endif

<script>
$(document).ready(function() {
    // Initialize tooltips if needed
    $('[title]').tooltip();
    
    // Animation on load
    $('.CdsCustomForm-list-item').each(function(index) {
        $(this).css('animation-delay', (index * 0.05) + 's');
    });
});
 function confirmUseTemplateAction(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to use this template?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "CdsTYButton-btn-primary",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            redirect(url);
        }
    });
}
</script>