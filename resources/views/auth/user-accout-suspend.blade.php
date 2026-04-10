@extends('layouts.app')

@section('content')
<section class="page_404">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="cds-page-404-container-wrap">
                    <div class="cds-page-404-container-wrap-header">

                    </div>

                    <div class="cds-page-404-container-wrap-body">
                        <h3 class="h2">
                            Look like your account is suspended or inactive.
                        </h3>
                        <p></p>
                        <a href="mailto:help@trustvisory.com" class="link_404">Contact support at
                            help@trustvisory.com</a>
                        <img src="{{url('assets/images/404-error.gif') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')

<script>

</script>
@endsection