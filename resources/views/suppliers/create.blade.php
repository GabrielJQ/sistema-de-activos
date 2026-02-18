@extends('layouts.admin')

@section('title', 'Crear Proveedor')

@section('content')
<div class="d-flex justify-content-center py-4">
    <div class="card shadow-sm border-0 rounded-4 w-100" style="max-width: 800px;">

        <div class="card-header bg-guinda text-white fw-semibold fs-5 text-center rounded-top">
            <i class="fas fa-plus-circle me-2"></i> Crear Proveedor
        </div>

        <div class="card-body">
            <form action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('suppliers._form')
            </form>
        </div>
    </div>
</div>
@endsection
