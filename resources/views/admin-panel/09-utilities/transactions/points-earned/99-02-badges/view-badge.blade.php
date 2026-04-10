@extends('layouts.app')

@section('content')
<section class="cds-t212-content-section-page-title">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="cds-t212-content-section-page-title-segment">
                    <div class="cds-t212-content-section-page-title-segment-list row">
                        <div class="cds-t212-content-section-page-title-segment-heading  col-md-8 col-lg-8">
                            <span> Content </span>
                            <h3>Badge</h3>
                        </div>
                        <div class="cds-t212-content-section-page-title-segment-action  col-md-4 col-lg-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="cds-t21n-content-section">
    <div class="container">       
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="cds-t212-content-section-main-div">
                    {!! $badge_html !!}
                </div>
            </div>           
        </div>
    </div>
</section>
<!-- End Content -->
@endsection