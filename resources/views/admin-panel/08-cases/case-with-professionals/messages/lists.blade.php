@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')
@section('case-container')


<div class="cds-fs-case-details-overview-panel">
    <div class="cds-fs-case-details-overview-panel-main">
        <div class="cds-fs-case-details-overview-panel-header"></div>
        <div class="cds-fs-case-details-overview-panel-body">
            <div class="row align-items-center mb-2">
                <div class="col-sm mb-2 mb-sm-0">
                    <h2 class="h4 mb-0">{{$pageTitle}}</h2>
                </div>

                <div class="col-sm-auto">
                    @if(empty($cases->caseChats))
                        <a href="{{ baseUrl('case-with-professionals/messages/add/' . $case_id)}}"  class="CdsTYButton-btn-primary">  Start a chat</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

                                                                
@endsection

@section('javascript')


@endsection