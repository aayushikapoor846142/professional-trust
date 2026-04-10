@extends('layouts.app')
@section('content')

<main>
    <section class="page_404">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="CDSMainPage-404-page-section">
                        <div class="four_zero_four_bg">
                            <h1 class="text-center ">404</h1>
                        </div>

                        <div class="contant_box_404">
                            <h3 class="h2">
                                Looks like you are lost!
                            </h3>

                            <p>The page you are looking for could not be found or is currently unavailable.!</p>

                            <a href="{{url('/')}}" class="link_404">Go to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
@section('javascript')
@endsection