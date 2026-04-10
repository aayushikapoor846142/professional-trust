    <div class="cds-latest-feeds-container cds-feedSection">
        <div class="order-2 order-md-1">
            <section class="cds-ty-dashboard-heading">
                <div>
                    <h1>Latest {{$pageTitle}}</h1>
                    <p>Discover the latest updates and discussions</p>
                </div>
            </section>
            <section class="cds-feeds-post-container">
                <div>
                    <div onclick="showPopup('<?php echo baseUrl('feeds/add-new-feed') ?>')">
                        <div class="cds-feeds-post-content mb-0 d-block d-md-flex">
                          {!! getProfeilImage($user->profile_image,$user->id) !!}
                         
                            <div class="form-group">
                                <h6 class="post-text">Share your thoughts, updates, or what's inspiring you today!</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Dynamic Content Section -->
            <div id="records-container"></div>
            <div class="cds-ty-dashboard-box-footer">
                
            </div>
        </div>
        <div class="order-1 order-md-2">
            <div class="cds-feeds-suggestions">
                <div class="cds-feeds-suggestion-block">
                    <div class="cds-feeds-suggestion-block-info">
                        <div>
                            <p class="mb-0">Total Followers :</p>
                        </div>
                    </div>
                    <button>
                    {{ auth()->user()->following()->count() ?? '' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="cds-ty-dashboard-box" style="display: none;">
        <div class="cds-ty-dashboard-box-header">
            <div class="justify-content-between d-flex align-items-center">
                <div class="ch-head">
                    <i class="fas fa-table me-1"></i>
                    Post Feed
                </div>
                <div class="d-flex justify-content-between">
                    <div id="datatableCounterInfo" style="display: none">
                        <div class="align-items-center">
                            <span class="font-size-sm mr-3">
                            <span id="datatableCounter">0</span>
                            Selected
                            </span>
                            <a class="btn-multi-delete CdsTYButton-btn-primary CdsTYButton-border-thick ml-2" data-href="{{ baseUrl('category/delete-multiple') }}"
                                onclick="deleteMultiple(this)" href="javascript:;">
                            Delete
                            </a>
                        </div>
                    </div>
                    <div class="ch-action">
                        <button type="button" onclick="showForm()" class="CdsTYButton-btn-primary mx-2">
                        Add New
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="cds-ty-dashboard-box-body">
            <div class="search-area">
            </div>
            <div class="datatable-custom">
                <form id="form 123" style="display:none" class="js-validate " action="" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="media" class="col-form-label input-label">Media <span class="text-danger">(Size should be less than 2 MB)</span></label>
                                <div class="js-form-message">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="col-form-label input-label">Article</label>
                                <div class="js-form-message">
                                    <textarea type="text" class="form-control editor" name="post" id="post" placeholder="Please enter post" data-msg=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-start mt-4">
                        <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                    </div>
            </div>
        </div>
        </form>
        <br>
    </div>
<!-- Post Modal -->
<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title ">Create Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeModalButton"></button>
            </div>
            <div class="modal-body">
                <form id="form" class="js-validate " action="{{ baseUrl('feeds/save') }}" method="post"  enctype="multipart/form-data">
                    @csrf
                    <div class="cds-feeds-post-content">
                        <div class="cds-post-header">
                        @if ($user->profile_image != '')
                        <img id="showProfileImage" src="{{ userDirUrl($user->profile_image)  }}" alt="Profile Image">
                        @else
                        <img src="{{ url('assets/images/demo.jpg') }}" class="img-fluid" />
                        @endif
                        </div>
                        <div class="form-group js-form-message">
                            <textarea class="form-control post-textarea" name="post" placeholder="Share your thoughts, updates, or what's inspiring you today!"></textarea>
                        </div>
                    </div>
                    <div id="thumbnail-preview" style="margin-top: 10px;">
                        <!-- Thumbnail will be displayed here -->
                    </div>
                    <div class="cds-feeds-post-actionbtn">
                        <div class="cds-feeds-post-actionbtn-content">
                            <div class="form-group js-form-message">
                                <label for="media" class="col-form-label input-label position-relative">
                                <img src="{{url('assets/images/icons/gallery-icon.svg') }}" alt="Gallery Icon">
                                Media
                                <input type="file" name="media" id="media" class="form-control cds-feeds-image-add" 
                                    aria-label="Choose image" accept=".jpg,.jpeg,.png,.gif,.bmp,.svg,.webp"
                                    data-msg="Please select an image file (jpeg, png, etc.)">
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn add-CdsTYButton-btn-primary">Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="loader" class="loader" style="display:none; text-align:center;">
    <div class="spinner-border" role="status">
        <span class="sr-only"></span>
    </div>
    <div>Loading...</div>
</div>
@push('scripts')
<script type="text/javascript">
  function showForm() {
    $('#form').toggle();
  }
  $(document).ready(function() {
    $('#closeModalButton').click(function () {
      location.reload();
    });
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
          $('#loader').show();
        },
        success: function(response) {
          $('#loader').hide();
          if (response.status == true) {
            successMessage(response.message);
            redirect(response.redirect_back);
          } else {
            validation(response.message);
          }
        },
        error: function() {
          $('#loader').hide();
          internalError();
        }
      });
    });
  });
</script>
@endpush