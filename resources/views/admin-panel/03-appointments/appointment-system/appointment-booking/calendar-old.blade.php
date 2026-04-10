@extends('admin-panel.layouts.app')

@section('content')
<!-- Content -->

<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="cds-ty-dashboard-box">
                <div class="cds-ty-dashboard-box-header">
                    <div class="cds-form-container search-area">
                        <div class="row justify-content-end">
                            <div class="col-lg-6 col-md-6 col-sm-8 col-xl-6">
                                <form id="search-form" style="display:none;">
                                    @csrf
                                    <div class="input-group mb-3">
                                        {!! FormHelper::formInputText([
                                        'name' => 'search',
                                        'label' => 'Search By Name'
                                        ]) !!}
                                        <button class="btn btn-secondary"><i class="fa fa-search"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="calendar" class="px-3 px-md-4 pb-3"></div>
            </div>
        </div>
    </div>
</div>
<!-- End Content -->
@endsection

@section('javascript')
<script src="{{url('assets/vendor/moment/moment-with-locales.min.js')}}"></script>
<script src="{{url('assets/vendor/fullcalendar-latest/dist/index.global.min.js')}}"></script>

<script>
$(document).ready(function() {
  loadCalendar();
});
function loadCalendar() {
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
      // initialView: 'dayGridMonth',
      rerenderDelay:10,
      events:"{{ baseUrl('appointments/appointment-booking/fetch-appointments') }}",
      dayRender: function(date, cell){
        var maxDate = new Date();
        if (date < maxDate){
            $(cell).addClass('disabled bg-light alert');
        }
     },
    });
    
    setTimeout(function(){
      calendar.render();
    },1500);
}
</script>
@endsection