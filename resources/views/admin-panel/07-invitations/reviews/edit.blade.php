@extends('admin-panel.layouts.app')
@section('content')
<div class="ch-action">
                    <a href="{{ baseUrl('review-received') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                </div>
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <form id="form" class="cds-editReviewForm js-validate" action="{{ baseUrl('/review-received/update/'.$record->unique_id) }}" method="post">
                        @csrf
                        <div class="row justify-content-md-between">
                            <div class="col-md-12">
                                <label class="col-form-label input-label">Rating</label>
                                <div class="js-form-message">
                                    <div class="rating">
                                        <input type="number" name="rating" hidden value="{{$record->rating}}">
                                        @for($i=1;$i<=$record->rating;$i++)
                                            <i class='fa-solid fa-star star'></i>
                                        @endfor
                                        @for($i=1;$i<=5-$record->rating;$i++)
                                            <i class='fa-regular fa-star star'></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="col-md-12">
                                <label class="col-form-label input-label">Review <span class="danger">*</span></label>
                                {!! FormHelper::formTextarea([
                                    'name' => 'review',
                                    'id' => 'review',
                                    'class' => 'cds-texteditor',
                                    'textarea_class' => 'noval',
                                    'required' =>  true,
                                    'value' => $record->review ?? '',
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="CdsTYButton-btn-primary add-btn">Save</button>
                        </div>
                    </form>
			</div>
	
	</div>
  </div>
</div>				

@endsection
@section('javascript')
<script>
if ($("#review").val() !== undefined) {
    initEditor("review");
}
$(document).ready(function() {
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
<script type="text/javascript">
const stars = document.querySelectorAll('.star');
const ratingInput = document.querySelector('input[name="rating"]');
stars.forEach((star, idx) => {
    star.addEventListener('click', function () {
        ratingInput.value = idx + 1;
        stars.forEach(s => s.classList.replace('fa-solid', 'fa-regular'));
        for (let i = 0; i <= idx; i++) {
            stars[i].classList.replace('fa-regular', 'fa-solid');
        }
    });
    star.addEventListener('mouseover', function () {
        stars.forEach(s => s.classList.replace('fa-solid', 'fa-regular'));
        for (let i = 0; i <= idx; i++) {
            stars[i].classList.replace('fa-regular', 'fa-solid');
        }
    });
    star.addEventListener('mouseout', function () {
        let selectedRating = ratingInput.value;
        stars.forEach(s => s.classList.replace('fa-solid', 'fa-regular'));
        for (let i = 0; i < selectedRating; i++) {
            stars[i].classList.replace('fa-regular', 'fa-solid');
        }
    });
});
</script>
@endsection