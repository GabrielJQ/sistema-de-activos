@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="wrapper-flex">
    {{-- Navbar --}}
    <nav class="login-navbar">
        <div class="container-nav">
            <img src="{{ asset('images/gobierno.png') }}" alt="Logo Gobierno">
            <img src="{{ asset('images/Joven-Mexicana.png') }}" alt="Escudo Joven Mexicana">
            <img src="{{ asset('images/logoAlimentacionBienestar1.png') }}" alt="ALIMENTACION PARA EL BIENESTAR">
            <img src="{{ asset('images/letrasAlimentacionBienestar.png') }}" alt="ALIMENTACION PARA EL BIENESTAR" class="logo-white">
            <div class="logo">SISTEMA DE ACTIVOS INFORMÁTICOS</div>
        </div>
    </nav>

    {{-- Contenido principal --}}
    <div class="main-content">
        <div class="login-wrapper">
            {{-- Panel izquierdo --}}
            <div class="login-left">
                <h1>Bienvenido/a</h1>
                <p>Gestiona tus activos informáticos de manera eficiente y segura con nuestro sistema.</p>
                <div class="social-links">
                    <a><i class="fab fa-twitter"></i></a>
                    <a><i class="fab fa-facebook-f"></i></a>
                    <a><i class="fab fa-youtube"></i></a>
                    <a><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            {{-- Panel derecho --}}
            <div class="login-right">
                <div class="login-card">
                    <h2 class="login-title">Iniciar Sesión</h2>

                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="input-group">
                            <label for="email"><span class="input-icon"><i class="fas fa-envelope"></i></span> Correo electrónico</label>
                            <input type="email" id="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="Ingresa tu correo" value="{{ old('email') }}" required autofocus>
                            @error('email') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-group" style="position:relative;">
                            <label for="password"><span class="input-icon"><i class="fas fa-lock"></i></span> Contraseña</label>
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Ingresa tu contraseña" required>
                            <span id="togglePassword"><i class="fa fa-eye"></i></span>
                            @error('password') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-right-to-bracket me-2"></i> Entrar
                        </button>

                        <div class="login-footnote">
                            <i class="fas fa-info-circle me-1"></i>
                            Si tienes problemas de acceso, contacta al área de Informática.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="login-footer">
        <p>&copy; {{ date('Y') }} Sistema de Activos Informáticos. Todos los derechos reservados.</p>
    </footer>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
});
</script>
@endsection
