@extends('layouts.admin')

@section('title', 'Nuevo Departamento')

@section('content')
<div class="d-flex justify-content-center py-4">
    <div class="card shadow-sm border-0 rounded-4 w-100" style="max-width: 900px;">
        {{-- Cabecera con color guinda forzado --}}
        <div class="card-header text-white fw-semibold fs-4 text-center rounded-top bg-guinda-force">
            <i class="fas fa-plus-circle me-2"></i> Nuevo Departamento
        </div>
        <div class="card-body">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
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
