@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ url('assets/plugins/select2/select2.min.css') }}">
 <section class="cds-t21n-breadcrumbs-section">
        <div class="container">
            <div class="row">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Contributions</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    {{-- content --}}
    <section id="cds-t21n-content-section" class="cds-content mt-md-4 mt-lg-5 mb-5 pt-4 pt-md-0">
        <div class="container">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="cds-contribution-list">
                        <div class="cds-contribution-head">
                            <div class="cds-head">Badge</div>
                            <div class="cds-head">Name</div>
                        </div>
                        @foreach(getSupportUsers() as $contribution)
                        <div class="cds-contribution-row">
                            <div class="cds-head">{{ $contribution->name }}</div>
                            <div class="cds-head">Name</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
