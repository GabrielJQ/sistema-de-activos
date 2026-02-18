<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'SAI'))</title>

    {{-- Font Awesome desde CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- CSS personalizado de cada página --}}
    @yield('css')
</head>

<body class="hold-transition login-page">

    {{-- Contenido dinámico --}}
    @yield('content')

    {{-- Scripts desde CDN para evitar errores 404 --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Scripts personalizados de cada página --}}
    @yield('scripts')
</body>

</html>