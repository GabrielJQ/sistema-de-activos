@extends('layouts.admin')

@section('title', 'Editar Departamento')

@section('content')
<div class="d-flex justify-content-center py-4">
    <div class="card shadow-sm border-0 rounded-4 w-100" style="max-width: 900px;">
        {{-- Cabecera con color guinda forzado --}}
        <div class="card-header text-white fw-semibold fs-4 text-center rounded-top bg-guinda-force">
            <i class="fas fa-edit me-2"></i> Editar Departamento
        </div>
        <div class="card-body">
            <form action="{{ route('departments.update', $department->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('departments._form')
            </form>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.bg-guinda-force {
    background-color: #611232 !important;
    color: #fff !important;
}
</style>
@stop
