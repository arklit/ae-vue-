<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    @include('components.common.meta')
    @vite('resources/scss/client/app.scss')
    {!! config('head') !!}
</head>
<body>
{!! config('body_start') !!}
<div class="page">
    <div id="app">
    </div>
    @include('components.header')
    <div id="app"></div>
    @include('components.common.seo-text-block')
    @include('components.footer')
</div>
@vite(['resources/js/client/app.js'])
{!! config('body_end') !!}
</body>
</html>
