
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="" />
<meta name="author" content="" />
<base id="siteurl" href="{{ url('/') }}/" />
<title>TrustVisory | {{ $pageTitle??'Dashboard' }}</title>
<link href="{{url('assets/css/base-12.css')}}" rel="stylesheet" />
   <link href="{{url('assets/css/styles.css')}}" rel="stylesheet" />
    <link href="{{url('assets/css/02-CDS-sections.css')}}" rel="stylesheet" />
    <link href="{{url('assets/css/03-CDS-navigation.css')}}" rel="stylesheet" />
    <link href="{{url('assets/css/04-CDS-dashboard.css')}}" rel="stylesheet" />
	<link href="{{url('assets/css/28-CDS-buttons.css')}}" rel="stylesheet" />
	<link href="{{url('assets/css/28-CDS-overview.css')}}" rel="stylesheet" /><link href="{{url('assets/css/09-CDS-interface-icons.css')}}" rel="stylesheet" />
<link href="{{url('assets/css/09-CDS-editor-icons.css')}}" rel="stylesheet" />
	<link href="{{url('assets/css/041-CDS-container-layout.css')}}" rel="stylesheet" />
	<link href="{{url('assets/css/34-CDS-filter.css')}}" rel="stylesheet" />
	<link href="{{url('assets/css/34-CDS-common-filter.css')}}" rel="stylesheet" />
    <link href="{{url('assets/css/34-CDS-menu-dropdown.css')}}" rel="stylesheet" />
<link href="{{url('assets/css/framework.css')}}" rel="stylesheet" />
<link href="{{url('/assets/css/sidebar.css?v='.mt_rand()) }}" rel="stylesheet" />
<link href="{{url('/assets/css/h-file.css?v='.mt_rand()) }}" rel="stylesheet" />
<link href="{{url('assets/css/admin-styles.css?v='.mt_rand())}}" rel="stylesheet" />
<link href="{{url('assets/css/feeds.css?v='.mt_rand())}}" rel="stylesheet" />
<link href="{{url('assets/css/cases.css?v='.mt_rand())}}" rel="stylesheet" />
<link type="text/css" rel="stylesheet" href="{{url('assets/css/custom.css?v='.mt_rand())}}">
<link type="text/css" rel="stylesheet" href="{{url('assets/css/CDS-loader-styles.css')}}">
<link href="{{url('assets/css/admin-responsive.css')}}" rel="stylesheet" />
    <!-- UTILITIES -->
<link href="{{url('assets/css/timepicker.css')}}" rel="stylesheet" />

<link href="{{url('assets/css/popup-modal.css?v='.mt_rand())}}" rel="stylesheet" />
<link href="{{url('assets/css/cds-custom-popup-modal.css')}}" rel="stylesheet" />
<link rel="stylesheet" href="{{url('assets/plugins/select2/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{url('assets/plugins/sweetalert2/sweetalert2.min.css')}}">

<link type="text/css" rel="stylesheet" href="{{url('assets/css/intlTelInput.min.css')}}">
<link rel="stylesheet" href="{{url('assets/plugins/toastr/toastr.css')}}">
<link rel="stylesheet" href="{{url('assets/plugins/dropzone/dropzone.min.css')}}">
<link rel="stylesheet" href="{{url('assets/plugins/jquery-ui/jquery-ui.min.css')}}">
<link href="{{url('assets/css/flatpickr.min.css?v='.time()) }}" rel="stylesheet" />
<link href="{{url('assets/css/flaticon.css')}}" rel="stylesheet" />
<link href="{{url('assets/css/discussions-styles.css')}}" rel="stylesheet" />
<link type="text/css" rel="stylesheet" href="{{url('assets/css/support.css')}}">
<!-- CHAT-UTILITIES -->
<link href="{{ url('assets/plugins/chatapp/chatapp.css?v='.mt_rand()) }}" rel="stylesheet" />
<link href="{{url('assets/plugins/chatapp/chatbot.css?v='.mt_rand())}}" rel="stylesheet" />
<link href="{{ url('assets/plugins/chatapp/emojis/css/style.css?v='.mt_rand()) }}" rel="stylesheet" />
<link href="{{url('assets/css/05-tables.css')}}" rel="stylesheet" />
<link href="{{ url('assets/css/custom-datepicker.css?v='.mt_rand()) }}" rel="stylesheet" />

<script>
@php
$loader_html = minify_html(view("components.skelenton-loader.message-skeletonloader")->render());
@endphp
var CHAT_LOADER = '{!! $loader_html !!}';
var openBots = [];
var ACTIVE_CHAT = 0;
var ACTIVE_GROUP_CHAT = 0;
var BASEURL = "{{ baseUrl('') }}";
var SITEURL = "{{ url('') }}";
var csrf_token = "{{ csrf_token() }}";
var assetBaseUrl = "{{ url('public') }}";
var PSKEY = "{{ apiKeys('PUSHER_APP_KEY') }}";
var PSCLS = "{{ apiKeys('PUSHER_APP_CLUSTER') }}";
@if(auth()-> check())
const currentUserId = {{ auth()->user()->id  }}
const currentUserName = '{{ auth()->user()->first_name." ".auth()->user()->last_name }}';
@endif
</script>

