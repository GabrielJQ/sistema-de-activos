@props(['label','name','icon','options'=>[],'selected'=>'','help'=>''])

<div class="col-md-6 col-sm-12 mb-3">
    <label for="{{ $name }}" class="form-label fw-semibold">{{ $label }}</label>
    <div class="input-group">
        <span class="input-group-text bg-soft text-muted"><i class="fas fa-{{ $icon }}"></i></span>
        <select name="{{ $name }}" id="{{ $name }}" class="form-select">
            <option value="">Seleccionar {{ strtolower($label) }}</option>
            @foreach($options as $key => $text)
                <option value="{{ $key }}" {{ $selected == $key ? 'selected' : '' }}>{{ $text }}</option>
            @endforeach
        </select>
    </div>
    @if($help)
        <small class="text-muted">{{ $help }}</small>
    @endif
    @error($name)
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>
