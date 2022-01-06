<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors" />
    <meta name="generator" content="Hugo 0.88.1" />
    <title>
        {{ $title ? $title : '' }} | {{ config('dz.admin.title') }} |
        {{-- @stack('title') --}}
    </title>

    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/10cf85d80f.js" crossorigin="anonymous"></script>

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

    </style>

    @foreach (config('dz.admin.global.css') as $item)
        <link rel="stylesheet" href="{{ $item }}">
    @endforeach

    {{-- @foreach (config('dz.admin.pagelevel.css') as $item)
        <link rel="stylesheet" href="{{ $item }}">
    @endforeach --}}

    <!-- Custom styles for this template -->
    {{-- <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet" /> --}}
</head>

<body>
