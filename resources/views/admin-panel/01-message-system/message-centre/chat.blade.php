@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('message') !!}
@endsection
@section('content')

@include('admin-panel.01-message-system.message-centre.chat_message')
@endsection