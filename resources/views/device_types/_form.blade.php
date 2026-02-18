@php
    $isEdit = isset($deviceType);
@endphp

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <strong><i class="fas fa-exclamation-triangle me-2"></i> Por favor corrige los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<h6 class="section-title"><i class="fas fa-laptop-code me-2"></i> Información del Tipo de Dispositivo</h6>

{{-- Nombre del Tipo --}}
<div class="mb-3">
    <label for="equipo" class="form-label fw-bold">Nombre del Tipo</label>
    <div class="input-group">
        <span class="input-group-text bg-light text-muted"><i class="fas fa-laptop"></i></span>
        <input type="text" 
               name="equipo" 
               class="form-control @error('equipo') is-invalid @enderror" 
               id="equipo" 
               value="{{ old('equipo', $deviceType->equipo ?? '') }}" 
               placeholder="Ej: Laptop, Monitor">
        @error('equipo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Descripción --}}
<div class="mb-3">
    <label for="descripcion" class="form-label fw-bold">Descripción</label>
    <div class="input-group">
        <span class="input-group-text bg-light text-muted"><i class="fas fa-align-left"></i></span>
        <textarea name="descripcion" 
                  id="descripcion" 
                  class="form-control @error('descripcion') is-invalid @enderror" 
                  rows="3" 
                  required
                  placeholder="Breve descripción">{{ old('descripcion', $deviceType->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Imagen --}}
<div class="mb-3">
    <label for="image" class="form-label fw-bold">Imagen (opcional)</label>
    <div class="input-group">
        <span class="input-group-text bg-light text-muted"><i class="fas fa-image"></i></span>
        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror">
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    @if($isEdit && $deviceType->image_path)
        <div class="mt-2">
            <img src="{{ asset($deviceType->image_path) }}" 
                 alt="Imagen actual" 
                 class="img-thumbnail" 
                 style="max-height: 100px;">
            <div class="form-text">Imagen actual</div>
        </div>
    @endif
</div>

{{-- Botones --}}
<div class="d-flex justify-content-end mt-4 gap-2">
    <button type="submit" class="btn btn-guinda px-4 shadow-sm">
        <i class="fas fa-save me-1"></i> {{ $isEdit ? 'Actualizar' : 'Guardar' }}
    </button>
    <a href="{{ route('device_types.index') }}" class="btn btn-secondary px-4 shadow-sm">
        <i class="fas fa-times me-1"></i> Cancelar
    </a>
</div>

{{-- Estilos encapsulados para el form --}}
<style>
.bg-guinda { background-color: #611232 !important; color: white; }
.section-title {
    font-weight: 600;
    color: #611232;
    margin-bottom: 1rem;
    font-size: 1.15rem;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 0.5rem;
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
    background-color: #611232 !important;
    color: #fff !important;
    border: 1px solid #611232 !important;
}
.btn-guinda:hover {
    background-color: #4e0f27 !important;
    border-color: #4e0f27 !important;
    color: #fff !important;
}
</style>

{{-- Scripts de validación --}}
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validacion de imagen
    const imageInput = document.getElementById('image');
    if(imageInput){
        imageInput.addEventListener('change', function(e) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            const file = e.target.files[0];
        
            if (file && !allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no válido',
                    text: 'Solo se permiten imágenes (jpeg, png, jpg, gif, webp).',
                    confirmButtonColor: '#611232'
                });
                e.target.value = ''; 
            }
        });
    }

    // Validacion campos requeridos al submit
    const form = document.querySelector('form');
    if(form) {
        form.addEventListener('submit', function(e) {
            const equipo = document.getElementById('equipo');
            const descripcion = document.getElementById('descripcion');
            
            let errors = [];

            if (equipo && !equipo.value.trim()) {
                errors.push('El campo <b>Nombre del Tipo</b> es obligatorio.');
            }
            
            if (descripcion && !descripcion.value.trim()) {
                errors.push('El campo <b>Descripción</b> es obligatorio.');
            }

            if (errors.length > 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos incompletos',
                    html: errors.join('<br>'),
                    confirmButtonColor: '#611232'
                });
            }
        });
    }
});
</script>
@endpush
