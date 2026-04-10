@foreach($records as $key => $record)
<div class="CdsModule-card">
    <div class="CdsModule-card-header">
        <div class="CdsModule-module-logo">{{ substr($record->name, 0, 2) }}</div>
        <div class="CdsModule-module-info">
            <div class="CdsModule-module-name">{{ $record->name ?? '' }}</div>
        </div>
    </div>
    <div class="CdsModule-details">
        <div class="CdsModule-detail-row">
            <span class="CdsModule-label">Module Name:</span>
            <span class="CdsModule-value">{{ $record->name ?? '' }}</span>
        </div>
        <div class="CdsModule-detail-row">
            <span class="CdsModule-label">Slug:</span>
            <span class="CdsModule-value">{{ $record->slug ?? '' }}</span>
        </div>
        <div class="CdsModule-detail-row">
            <span class="CdsModule-label">Added By:</span>
            <span class="CdsModule-value">{{ $record->user->first_name ?? '' }} {{ $record->user->last_name ?? '' }}</span>
        </div>
        <div class="CdsModule-detail-row">
            <span class="CdsModule-label">Actions:</span>
            <span class="CdsModule-value">{{ count($record->moduleAction) ?? 0 }} actions</span>
        </div>
    </div>
    <div class="CdsModule-actions">
        <a href="javascript:;" onclick="cdsModuleOpenModal('{{ $record->unique_id }}')" class="CdsModule-action-btn CdsModule-btn-edit">
            <span class="CdsModule-icon CdsModule-icon-edit"></span>
            Edit
        </a>
        <a href="javascript:;" onclick="confirmAction(this)" 
           data-href="{{ baseUrl('module/delete/'.$record->unique_id) }}"
           class="CdsModule-action-btn CdsModule-btn-delete">
            <span class="CdsModule-icon CdsModule-icon-delete"></span>
            Delete
        </a>
    </div>
</div>
@endforeach

<script type="text/javascript">
$(document).ready(function() {
    // Update the module count
    const moduleCount = document.querySelectorAll('.CdsModule-card').length;
    document.getElementById('cdsModuleCount').textContent = moduleCount;
});
</script>