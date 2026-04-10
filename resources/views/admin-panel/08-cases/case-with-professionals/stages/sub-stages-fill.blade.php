<div class="CdsDashboardCaseStages-list-view-expanded-details">
    <div style="padding: 20px; background: #f8f9fa; margin-top: 12px; border-radius: 8px;">
        <h5 style="margin-bottom: 12px;">Additional Details</h5>
        <div style="margin-top: 16px;">
            <label style="font-size: 12px; color: #6c757d;">Notes</label>
            <p style="margin: 4px 0; color: #495057;">Please ensure all forms are filled accurately. Contact legal team if assistance needed.</p>
        </div>
        @if($records->stage_type == "case-document")
            @include('admin-panel.08-cases.case-with-professionals.stages.uploaded-document')
        @endif
        @if($records->stage_type == "fill-form")
            @if($records->form_reply != '')
                <label>Form:</label>{{$form->name}} 
                <a href="{{baseUrl('/case-with-professionals/stages/view-form/'.$records->unique_id)}}" class="CdsTYButton-btn-primary">View</a>
            @else
                <span class="text-danger">Assessment form not submitted</span>
            @endif
        @endif
        @if($records->stage_type == "payment")
            @include('admin-panel.08-cases.case-with-professionals.stages.payment')
        @endif
    </div>
</div>