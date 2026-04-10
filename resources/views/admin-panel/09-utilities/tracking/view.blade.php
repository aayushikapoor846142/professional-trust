@extends('admin-panel.layouts.app')

@section('content')
<div class="tracking-container">
    <div class="cds-main-layout-header">
        <div class="breadcrumb-conatiner">
            <ol class="breadcrumb">
                <i class="fa-regular fa-grid-2"></i>
                <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('/') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$pageTitle}}</li>
            </ol>
        </div>
        <div class="cds-heading">
            <div class="cds-heading-icon">
                <i class="fa-light fa-pen"></i>
            </div>
            <h1>{{$pageTitle}}</h1>
        </div>
    </div>
    <div class="cds-ty-case-tracking-title"><h6>Investigation Tracker</h6></div>
    @if(!empty($record)) {!! getTrackingResult($record) !!} @endif
</div>
<div class="cds-comment-box comment_div mt-4">
    <form id="comment-form" class="js-validate" action="{{ baseUrl('save-uap-comment') }}" method="post">
        @csrf

        <input type="hidden" name="ref_user_id" id="ref_user_id" value="{{$ref_user_id}}" />
        <input type="hidden" name="uap_id" id="uap_id" value="{{$uap_id}}" />
        <div class="row">
            <div class="col-md-12">
                <fieldset>
                    <legend>Comment</legend>
                    <div class="form-group">
                        <!-- <label for="" class="col-form-label fs-4">Comment</label> -->
                        <div class="js-form-message">
                            <textarea type="text" class="form-control required" name="comment" id="comment" placeholder="Enter comment" data-msg=""></textarea>
                        </div>
                    </div>
                    <div class="form-group text-center mt-3">
                        <button type="submit" class="btn-red add-btn">Save</button>
                    </div>
                </fieldset>
            </div>
        </div>
    </form>
</div>

<div class="cds-comment-box show_comment_div">
    <div class="main-comment-div" id="main-comment-div"></div>
    <a href="javascript:;" class="d-block mt-3 text-center cds-ty-33-review-more-comment" id="commentLink">Load More <i class="fa-solid fa-angle-right"></i></a>
</div>
<input type="hidden" name="uap_comment_id" id="uap_comment_id" value="{{$uap_id}}" />

@endsection


@section('javascript')
<script>
      $("#comment-form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("comment-form");
            if(!is_valid){
                return false;
            }
            var formData = new FormData($(this)[0]);
            var url = $("#comment-form").attr('action');
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
                    if(response.status != 0){
                        successMessage(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    }else{
                        $('.alert-danger-lbl').html(response.message);
                    }
                },
                error: function() {
                    internalError();
                }
            });

        });
        loadData();
        var page = 1;
    let loading = false;

    function loadData(page = 1) {
        $.ajax({
            type: "POST",
            url: BASEURL + '/get-uap-comment?page=' + page,
            data: {
                _token: csrf_token,
                uap_id: $("#uap_comment_id").val()
            },
            dataType: 'json',
            success: function (data) {
                if (data.contents.trim() === "") {
                    // No data found, display a message to the user
                    //$(".load-more").html('No more data to load...');
                    loading = true; // Prevent further requests
                    $("#commentLink").addClass('d-none');
                } else {

                    if (page === 1) {

                        // If it's the first page of search results, replace existing content
                        $(".main-comment-div").html(data.contents);
                    } else {

                        // Otherwise, append the data
                        $(".main-comment-div").append(data.contents);
                    }


                    var next_page = data.current_page + 1;

                    if (data.last_page >= next_page) {
                        loading = false; // Allow further requests
                    } else {
                        loading = true; // Prevent further requests
                    }

                    if (data.last_page == 0) {
                        $(".load-more").html('No more data to load...');
                        $("#commentLink").addClass('d-none');
                    }
                    if (data.current_page == data.last_page) {
                        $("#commentLink").addClass('d-none');
                    }

                    $(".data-result").html(data.total_records);
                    $(".current-page").html(data.current_page);
                    $(".last-page").html(data.last_page);
                }

            },
            error: function (xhr, status, error) {
                console.log("Error: " + error);
            }
        });
    }

    $("#commentLink").click(function (event) {
        if (!loading) {
            loading = true;
            page++;
            loadData(page);
        }
    });
</script>

@endsection