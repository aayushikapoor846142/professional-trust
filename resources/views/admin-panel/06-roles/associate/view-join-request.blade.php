@extends('components.custom-popup',['modalTitle'=>$pageTitle ?? 'Mark As Client'])
@section('custom-popup-content')
<div class="cds-form-container cds-ty-dashboard-box-body mb-0">

        <div class="row">
            <div class="col-xl-12">
                {!! $record->summary!!}                  
            </div>
          
        </div>        
        @section('custom-popup-footer')
        <div class="text-end">
            <a href="javascript:;" onclick="confirmAccept(this)" data-href="{{baseUrl('associates/accept-proposal/'.$record->unique_id)}}" class="btn btn-primary add-btn">Accept</a>
             <a href="javascript:;" onclick="confirmReject(this)" data-href="{{baseUrl('associates/reject-proposal/'.$record->unique_id)}}" class="btn btn-danger add-btn">Reject</a>
        </div>
        @endsection
   
</div>

<!-- End Content -->
<script>
$(document).ready(function () {

  
});
</script>
@endsection