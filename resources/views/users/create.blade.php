@extends('layouts.admin')

@section('title', 'Nuevo Usuario')

@section('content_header')
@stop

@section('content')
<form action="{{ route('users.store') }}" method="POST">
    @include('users._form')
</form>
@stop
