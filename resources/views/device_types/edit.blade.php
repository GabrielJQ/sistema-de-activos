@extends('layouts.admin')

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

                <h6 class="section-title"><i class="fas fa-laptop-code me-2"></i> Información del Tipo de Dispositivo</h6>

                {{-- Nombre del Tipo --}}
                <div class="mb-3">
                    <label for="equipo" class="form-label fw-bold">Nombre del Tipo</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-laptop"></i></span>
                        <input type="text" name="equipo" class="form-control" id="equipo" 
                               value="{{ old('equipo', $deviceType->equipo) }}" required placeholder="Ej: Laptop, Monitor">
                    </div>
                </div>

                {{-- Descripción --}}
                <div class="mb-3">
                    <label for="descripcion" class="form-label fw-bold">Descripción</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-align-left"></i></span>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" placeholder="Breve descripción">{{ old('descripcion', $deviceType->descripcion) }}</textarea>
                    </div>
                </div>

                {{-- Imagen --}}
                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">Imagen (opcional)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-image"></i></span>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                </div>

                {{-- Botones --}}
                <div class="d-flex flex-column flex-md-row gap-2 mt-3 justify-content-end">
                    <button type="submit" class="btn btn-guinda px-4 shadow-sm flex-fill">
                        <i class="fas fa-save me-1"></i> Actualizar
                    </button>
                    <a href="{{ route('device_types.index') }}" class="btn btn-secondary px-4 shadow-sm flex-fill">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@section('js')
<script>
document.getElementById('image').addEventListener('change', function(e) {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
    const file = e.target.files[0];

    if (file && !allowedTypes.includes(file.type)) {
        alert('Solo se permiten imágenes (jpeg, png, jpg, gif, webp).');
        e.target.value = ''; // limpiar input
    }
});
</script>
@endsection

<style>
.bg-guinda { background-color: #611232 !important; }
.section-title {
    font-weight: 600;
    color: #611232;
    margin-bottom: 1rem;
    font-size: 1.15rem;
}
.form-control {
    border-radius: 0.5rem !important;
    border: 2px solid #b0b0b0 !important;
    padding: 0.55rem 0.75rem !important;
    transition: all 0.3s ease;
}
.form-control:focus {
    border-color: #611232 !important;
    box-shadow: 0 0 0 0.2rem rgba(97, 18, 50, 0.25);
}
.input-group-text {
    border-radius: 0.5rem 0 0 0.5rem;
    border: 2px solid #b0b0b0;
    background-color: #f8f9fa;
}
.input-group:focus-within .input-group-text {
    border-color: #611232;
}
.btn-guinda {
    background-color: #611232;
    color: #fff;
}
.btn-guinda:hover {
    background-color: #4e0f27;
    color: #fff;
}
</style>
@endsection
