@extends('layouts.admin')

@section('title', 'Nuevo Empleado')

@section('content_header')

@stop

@section('content')
<form action="{{ route('employees.store') }}" method="POST">
    @include('employees._form')
</form>
@stop
