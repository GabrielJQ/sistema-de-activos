@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('content_header')
@stop

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('users.update', $user) }}" method="POST">
    @method('PUT')
    @include('users._form')
</form>
@stop
