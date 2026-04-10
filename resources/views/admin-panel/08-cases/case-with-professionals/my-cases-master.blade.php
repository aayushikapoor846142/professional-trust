@extends('admin-panel.layouts.app')
@section('styles')
<link href="{{ url('assets/css/13-CDS-case-stages.css') }}" rel="stylesheet" />
<link href="{{ url('assets/css/13-CDS-case-with-professional-overview.css') }}" rel="stylesheet" />
 <link href="{{ url('assets/css/26-CDS-case-request.css') }}" rel="stylesheet" />
  <link href="{{ url('assets/css/26-CDS-case-view-request.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="{{ url('assets/css/15-CDS-case-documents.css') }}" />
  <link href="{{ url('assets/css/12-CDS-support-payment.css') }}" rel="stylesheet" />
@endsection
@section('content')
@php 
$case_record = caseInfo($case_id);

@endphp
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 <form id="search-form">
                                    @csrf
                                    <div class="input-group mb-3">
                                        {!! FormHelper::formInputText([
                                        'name' => 'search',
                                        'label' => 'Search By Name'
                                        ]) !!}
                                        <button class="btn btn-secondary"><i class="fa fa-search"></i></button>
                                    </div>
                                </form>
<div class="p-3">
                        @if ($case_record)
                        <!-- Case Details -->
                        <div class="group-chat-title mb-0">
                            <h2>My Case Details</h2>
                        </div>
                        <div class="cds-service cds-serviceDetails mt-3">
                            <div class="cds-serviceHeader">
                                <h3 class="mb-0 service-title">Main Service Name: {{ $case_record->services->name ?? '' }}</h3>
                            </div>
                            <div class="case-details cds-serviceBody">
                                <h3 class="subservice-title">Sub Service Name: {{ $case_record->subServices->name ?? '' }}</h3>
                                <div class="cds-wrap-content">
                                    <ul>
                                        <li class="cdsli1"><strong>Case Name:</strong> <span class="d-block caseName">{{ $case_record->case_title ?? '' }}</span> </li>
                                        <li class="cdsli2"><strong>Case Status:</strong> <span class="d-block caseStaus">{{ $case_record->status ?? '' }}</span></li>
                                        <li class="cdsli3"><strong>Posted on:</strong> <span class="d-block postDate">{{ $case_record->created_at->format('d M Y') ?? '' }}</span></li>
                                    </ul>
                                </div>
                            </div>            
                        </div>
                        @endif
                    </div>   <div class="CdsCaseOverview-tab-nav">
                            <a class="CdsCaseOverview-tab-item {{request()->route()->getName() =='panel.case-with-professionals.view' ? 'CdsCaseOverview-active' : ''}}" href="{{baseUrl('case-with-professionals/view/'.$case_record->unique_id)}}">Overview</a>
   @if(checkPrivilege([
                                'route_prefix' => 'panel.case-with-professionals',
                                'module' => 'professional-case-with-professionals',
                                'action' => 'retain-agreements'
                            ]))
                            <a class="CdsCaseOverview-tab-item {{request()->route()->getName() == 'panel.case-with-professionals.retain-agreements.retain-agreements' ? 'CdsCaseOverview-active' : ''}}" href="{{baseUrl('case-with-professionals/retain-agreements/'.$case_record->unique_id)}}">Retain Agreements</a>
                            @endif

                             @if(checkPrivilege([
                                'route_prefix' => 'panel.case-with-professionals',
                                'module' => 'professional-case-with-professionals',
                                'action' => 'activities'
                            ]))
                            <a class="CdsCaseOverview-tab-item " href="{{baseUrl('case-with-professionals/view/'.$case_record->unique_id)}}">Activities</a>
                                 @endif

                            @if(checkPrivilege([
                                'route_prefix' => 'panel.case-with-professionals',
                                'module' => 'professional-case-with-professionals',
                                'action' => 'send-request'
                            ]))
                                <a class="CdsCaseOverview-tab-item {{request()->route()->getName() =='panel.case-with-professionals.send-request' ? 'CdsCaseOverview-active' : ''}}" href="{{baseUrl('case-with-professionals/send-request/'.$case_record->unique_id)}}">Requests</a>
                            @endif

                            @if(checkPrivilege([
                                'route_prefix' => 'panel.case-with-professionals',
                                'module' => 'professional-case-with-professionals',
                                'action' => 'documents'
                            ]))
                                <a class="CdsCaseOverview-tab-item  {{request()->route()->getName() =='panel.case-with-professionals.documents' ? 'CdsCaseOverview-active' : ''}}"  href="{{baseUrl('case-with-professionals/documents/'.$case_record->unique_id)}}">Documents</a>

                                <a class="CdsCaseOverview-tab-item {{request()->route()->getName() =='panel.case-with-professionals.encryptedDocuments' ? 'CdsCaseOverview-active' : ''}}" href="{{baseUrl('case-with-professionals/encrypted-documents/'.$case_record->unique_id)}}">Encrypted Documents</a>
                            @endif

                              @if(checkPrivilege([
                                'route_prefix' => 'panel.case-with-professionals',
                                'module' => 'professional-case-with-professionals',
                                'action' => 'case-stages'
                            ]))
                            
                                <a class="CdsCaseOverview-tab-item {{request()->route()->getName() =='panel.case-with-professionals.stages.list' ? 'CdsCaseOverview-active' : ''}}"  href="{{baseUrl('case-with-professionals/stages/'.$case_record->unique_id)}}">Case Stages</a>
                            @endif
                            <a class="CdsCaseOverview-tab-item " href="{{baseUrl('case-with-professionals/invoices/'.$case_record->unique_id)}}">Invoice</a>
                            @if(checkPrivilege([
                                'route_prefix' => 'panel.case-with-professionals',
                                'module' => 'professional-case-with-professionals',
                                'action' => 'message'
                            ]))
                                <a class="CdsCaseOverview-tab-item {{request()->route()->getName() =='panel.case-with-professionals.messages.list' ? 'CdsCaseOverview-active' : ''}}" href="{{baseUrl('case-with-professionals/messages/'.$case_record->unique_id)}}">Messages</a>
                            @endif

                           
                        </div>
                
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
   @yield('case-container')
			</div>
	
	</div>
  </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">

    $(document).ready(function() {
    initEditor("description");
        $("#form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("form");
            if (!is_valid) {
                return false;
            }
            var formData = new FormData($(this)[0]);
            var url = $("#form").attr('action');
            $.ajax({
                url: url,
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        redirect(response.redirect_back);
                    } else {
                        validation(response.message);
                    }
                },
                error: function() {
                    internalError();
                }
            });

        });
    });
 

</script>
@endpush
