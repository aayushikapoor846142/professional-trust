@if(isset($dashboard_layout))
 
@include("admin-panel.layouts.".$dashboard_layout.".app")
@else
@include("admin-panel.layouts.layout-01.app")
@endif