@extends('layouts.admin')

@section('title', 'Editar Empleado')

@section('content_header')
@stop

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('employees.update', $employee) }}" method="POST">
    @method('PUT')
    @include('employees._form')
</form>
@stop
