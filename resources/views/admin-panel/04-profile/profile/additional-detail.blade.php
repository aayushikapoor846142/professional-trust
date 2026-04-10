<div class="cds-containerbody">
    <form id="form" class="js-validate" action="{{baseUrl('/professional-submit-profile')}}" method="post">
        @csrf
        <input type="hidden" name="type" value="additional_detail">
        <div id="form-render"></div>
    </form>
</div>
@push("scripts")
<link href="{{ url('assets/plugins/form-generator/css/cds-form-generator.css') }}" rel="stylesheet" />
<script src="{{ url('assets/plugins/form-generator/js/jquery-ui.js') }}"></script>
<script src="{{ url('assets/plugins/form-generator/js/form-generator.js') }}"></script>
<script>
    var formJson = '{!! $user_details->additional_detail_form!!}';
    var defaultValues = '{!!$last_saved!!}';
    $(document).ready(function() {
        var fr = $('#form-render').formRender({
            formType: "{{$user_details->additional_detail_type}}",
            formJson: formJson,
            formID:"",
            ajax_call: false,
            defaultValues: defaultValues,
            saveUrl: "{{baseUrl('/professional-submit-profile')}}",
        });
    });
</script>
@endpush