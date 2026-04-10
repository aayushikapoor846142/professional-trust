@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('accounts') !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/common-table-redesign.css') }}">
@endsection
@section('content')
<div class="container-fluid">
    <section class="cds-ty-dashboard-breadcrumb-container">
        <div class="cds-main-layout-header">
            <div class="breadcrumb-conatiner">
                <ol class="breadcrumb">
                    <i class="fa-grid-2 fa-regular"></i>
                    <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('/') }}">Dashboard</a></li>
                    <li class="active breadcrumb-item" aria-current="page">{{$pageTitle}}</li>
                </ol>
            </div>
            <div class="cds-heading">
                <div class="cds-heading-icon">
                    <i class="fa-light fa-pen"></i>
                </div>
                <h1>{{$pageTitle}}</h1>
            </div>
        </div>
    </section>
</div>
<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="cds-ty-dashboard-box">
                
                <div class="cds-ty-dashboard-box-header">
                    <div class="cds-ty-dashboard-box-body">
                        <b>Associate:</b>{{$caseJoinRequest->associate->first_name}}{{$caseJoinRequest->associate->last_name}}</br>
                        <b>Case Title:</b>{{$caseJoinRequest->leadCase->case_title}}</br>
                        <b>Case Description:</b>{{$caseJoinRequest->leadCase->case_description}}</br>
                        <b>Parent Service:</b>{{$caseJoinRequest->leadCase->services->name}}</br>
                        <b>Sub Service:</b>{{$caseJoinRequest->leadCase->subServices->name}}</br>
                        <b>Summary:</b>{{$caseJoinRequest->summary}}</br>

                        @if($caseJoinRequest->status == 0)
                        <a onclick="openCustomPopup(this)" href="javascript:;" data-href="{{baseUrl('case-join-requests/accept-modal/'.$caseJoinRequest->unique_id)}}" class="btn btn-success">Accept</a>

                        <a onclick="confirmReject(this)" href="javascript:;" data-href="{{baseUrl('case-join-requests/reject/'.$caseJoinRequest->unique_id)}}" class="btn btn-danger">Reject</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    	function confirmReject(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to reject?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-primary",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            redirect(url);
        }
    });
}
function confirmAccept(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to accept?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-primary",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            redirect(url);
        }
    });
}
</script>
@endsection