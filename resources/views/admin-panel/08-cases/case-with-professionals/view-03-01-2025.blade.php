@extends('admin-panel.layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="cds-ty-dashboard-box">
                <div class="cds-ty-dashboard-box-header">
                    <div class="search-area cds-form-container">
                        <div class="row justify-content-end">
                            <div class="col-xl-6 col-md-6 col-sm-8 col-lg-6">
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
                            </div>
                        </div>
                    </div>
                    <div class="p-3">
                        @if ($record)
                        <!-- Case Details -->
                        <div class="group-chat-title mb-0">
                            <h2>My Case Details</h2>
                            <a href="{{baseUrl('my-cases/send-request/'.$record->unique_id)}}" class="CdsTYButton-btn-primary">Send Request</a>
                        </div>
                        <div class="cds-service cds-serviceDetails mt-3">
                            <div class="cds-serviceHeader">
                                <h3 class="service-title mb-0">Main Service Name: {{ $record->services->name ?? '' }}</h3>
                            </div>
                            <div class="cds-serviceBody case-details">
                                <h3 class="subservice-title">Sub Service Name: {{ $record->subServices->name ?? '' }}</h3>
                                <div class="cds-wrap-content">
                                    <ul>
                                        <li class="cdsli1"><strong>Case Name:</strong> <span class="d-block caseName">{{ $record->case_title ?? '' }}</span> </li>
                                        <li class="cdsli2"><strong>Case Status:</strong> <span class="d-block caseStaus">{{ $record->status ?? '' }}</span></li>
                                        <li class="cdsli3"><strong>Posted on:</strong> <span class="d-block postDate">{{ $record->created_at->format('d M Y') ?? '' }}</span></li>
                                        <li class="cdsli4"><strong>Description:</strong> <span class="d-block postDate"> {!! html_entity_decode($record->case_description ?? '') !!}</span></li>
                                    </ul>
                                </div>
                            </div>            
                        </div>
                        @endif
                    </div>    
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Content -->
@endsection

@section('javascript')
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
@endsection
