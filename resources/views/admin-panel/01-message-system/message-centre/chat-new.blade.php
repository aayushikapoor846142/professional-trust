@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('message') !!}
@endsection
@section('content')

@include('admin-panel.01-message-system.message-centre.components.breadcrumb')

@include('admin-panel.01-message-system.message-centre.components.chat-container')

@php
$loader_html = minify_html(view("components.skelenton-loader.message-skeletonloader")->render());
@endphp

@endsection

@section('javascript')
@include('admin-panel.01-message-system.message-centre.scripts.chat-socket')
@include('admin-panel.01-message-system.message-centre.scripts.message-handlers')
@endsection 