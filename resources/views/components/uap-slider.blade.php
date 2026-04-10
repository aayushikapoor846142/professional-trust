<div class="cds-ty-home-uap-section-item-list-container">
    <div class="cds-ty-home-uap-section-item-list-container-main">
        <div class="cds-ty-home-uap-section-item-list-container-main-header">
            <div class="cds-uap-filter">
                <div class="select-custom noborder required">
                    <select class="cust-select" name="uap_slider" id="uap_slider">
                        <option value="recently_added">Recently Added</option>
                        <option value="most_critical">Most Critical </option>
                    </select>
                </div>

            </div>
            <div class="uap-slide-nav d-flex">
                <button type="button" class="btn btn-secondary cds-uap-slide-prev me-2"><i
                        class="fa fa-chevron-left"></i></button>
                <button type="button" class="btn btn-secondary cds-uap-slide-next"><i
                        class="fa fa-chevron-right"></i></button>
                <button type="button" class="cds-uap-slide-view-more cds-uap-slide-view-more-btn"
                    style="display:none">View More</button>
            </div>
        </div>

        <div class="cds-ty-home-uap-section-item-list-container-main-body">
            <div class="slider-wrapper">
                <div class="uap-sliders">
                </div>
            </div>
        </div>

    </div>

</div>
<div class="row cd-uap-slider-area">
    <div class="col-xl-9 col-md-12 col-lg-9 cds-uap-top-nav uap-slide-navbar">

    </div>
    <div class="col-xl-3 col-md-12 col-lg-3">
        <!-- <span class="title">Sort by:</span> -->

    </div>
</div>

@push("scripts")
<script>
    let uap_per_slide = 8;
    window.addEventListener('resize', uapPerSlide);
    uapPerSlide();

    function uapPerSlide() {
        let screen_width = $(window).width();
        if (screen_width <= 450) {
            uap_per_slide = 2;
        } else if (screen_width >= 451 && screen_width <= 768) {
            uap_per_slide = 2;
        } else if (screen_width >= 769 && screen_width <= 1120) {
            uap_per_slide = 4;
        } else if (screen_width >= 1121 && screen_width <= 1440) {
            uap_per_slide = 6;
        } else if (screen_width >= 1441) {
            uap_per_slide = 6;
        }
        loadUapSlider();
    }
    let uap_slide_page = 1;
    $(document).on('click', '.cds-uap-slide-next', function () {
        uap_slide_page++;
        loadUapSlider(uap_slide_page);
    });
    $(document).on('click', '.cds-uap-slide-prev', function () {
        uap_slide_page--;
        loadUapSlider(uap_slide_page);
    });
    $(document).on('click', '.cds-uap-slide-view-more', function () {
        var type = $("#uap_slider").val();
        var url = "{{url('/unauthorised-practitioners?filter=')}}" + type;
        redirect(url);
    });
    $(document).on('change', '#uap_slider', function () {
        uap_slide_page = 1;
        loadUapSlider(uap_slide_page);
    });

    function loadUapSlider(page = 1) {
        $.ajax({
            type: 'POST',
            // url: url,
            url: SITEURL + '/show-uap-slider?page=' + page,
            data: {
                _token: csrf_token,
                per_page: uap_per_slide,
                value: $("#uap_slider").val()
            },
            beforeSend: function () {
                $(".uap-sliders").html(
                    "<div class='text-center'><i class='fa fa-spin fa-spinner fa-2x'></i></div>");
            },
            success: function (response) {
                if (response.status === true) {
                    uap_slide_page = response.current_page;

                    $(".uap-sliders").html(response.contents);
                    if (response.current_page < 3) {
                        $(".cds-uap-slide-next").show();
                        $(".cds-uap-slide-view-more").hide();
                    } else {
                        $(".cds-uap-slide-next").hide();
                        $(".cds-uap-slide-view-more").show();
                    }
                    if (response.last_page == response.current_page) {
                        $(".cds-uap-slide-prev").attr('disabled', 'disabled');
                        $(".cds-uap-slide-next").hide();
                        $(".cds-uap-slide-view-more").show();
                    }
                }
            },
            error: function (xhr) {

            }
        });
    }

</script>

@endpush
