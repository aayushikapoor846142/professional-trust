@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')
@section('case-container')
@php 
$case_record = caseInfo($case_id);
@endphp
<div class="content">
    <div class="cds-fs-render-form-overview-profile">
        <div class="cds-fs-render-form-overview-profile-header">
            <div class="cds-fs-render-form-overview-profile-header-left">
            </div>
            <div class="cds-fs-render-form-overview-profile-header-right">
                <div class="cds-fs-render-form-overview-profile-header-right-date">
                </div>
            </div>
        </div>

        <div class="cds-fs-render-form-overview-profile-body">
            @if($record->request_type == "assesment-form-request")
                @include('admin-panel.08-cases.case-with-professionals.view-request-submitted-form');
            @endif

            @if($record->request_type == "document-request")
                @include('admin-panel.08-cases.case-with-professionals.view-request-submitted-document')
            @endif
            
        </div>
    </div>
</div>                                                      
@endsection


@section('javascript')

@endsection
