@props(['label','name','icon','value'=>'','placeholder'=>''])

<div class="col-md-6 col-sm-12 mb-3">
    <label for="{{ $name }}" class="form-label fw-semibold">{{ $label }}</label>
    <div class="input-group">
        <span class="input-group-text bg-soft text-muted"><i class="fas fa-{{ $icon }}"></i></span>
        <input type="text" name="{{ $name }}" id="{{ $name }}" class="form-control" placeholder="{{ $placeholder }}" value="{{ $value }}">
    </div>
    @error($name)
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>
