@extends('adminlte::page')

@push('css')
    @if(session()->has('smiab_access_token'))
        <meta name="smiab-token" content="{{ session('smiab_access_token') }}">
    @endif

    <link rel="stylesheet" href="{{ asset('css/panelPrincipal.css') }}">
@endpush