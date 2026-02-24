@extends('layouts.admin')

@section('title', 'Nuevo Usuario')

@section('content_header')
@stop

@section('content')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('users.store') }}" method="POST">
    @include('users._form')
</form>
@stop
