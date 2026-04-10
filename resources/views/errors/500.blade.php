@extends('layouts.app')
@section('content')
    <section class="page_404 cds-notAuto">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="CDSMainPage-404-page-section">
                        <div class="four_zero_four_bg">
                            <h1 class="text-center ">500</h1>
                        </div>
                        <div class="contant_box_404">
                            <h3 class="h2">
                            Look like you're lost
                            </h3>
                            <p>The page you are looking for is not available or has been moved!</p>
                            <a href="{{url('/')}}" class="link_404">Go to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('javascript')
@endsection