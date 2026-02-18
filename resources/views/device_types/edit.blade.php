@extends('layouts.admin')

@section('title', 'Editar Tipo de Dispositivo')

@section('content')
<div class="d-flex justify-content-center py-4">
    <div class="card shadow-sm border-0 rounded-3 w-100" style="max-width: 800px;">
        <div class="card-header bg-guinda text-white fw-semibold fs-5 text-center rounded-top">
            <i class="fas fa-edit me-2"></i> Editar Tipo de Dispositivo
        </div>

        <div class="card-body">
            <form action="{{ route('device_types.update', $deviceType->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('device_types._form')
            </form>
        </div>
    </div>
</div>
@endsection
