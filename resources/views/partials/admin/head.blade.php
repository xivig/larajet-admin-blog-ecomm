<!DOCTYPE html>
<html dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Xivig Executive Admin Portal">
    <meta name="author" content="Xivig">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <title>@yield('title', 'Xivig Executive Admin Portal')</title>

    <!-- Google Fonts Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Theme CSS -->
    <link href="{{ asset('xivig-public/assets/libs/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('xivig-public/assets/libs/chartist/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('xivig-public/assets/extra-libs/c3/c3.min.css') }}" rel="stylesheet">
    <link href="{{ asset('xivig-public/assets/extra-libs/jvector/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" />
    <link href="{{ asset('xivig-public/dist/css/style.min.css') }}" rel="stylesheet">
    <!-- Elite Design Tokens -->
    <link rel="stylesheet" href="{{ asset('xivig-blog/css/elite-design.css') }}">

    <style>
        body, .page-wrapper { font-family: 'Plus Jakarta Sans', sans-serif !important; }
        h1, h2, h3, h4, h5, h6, .page-title { font-family: 'Outfit', sans-serif !important; font-weight: 700; }
        .card { border-radius: 16px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important; }
        .table thead th { font-family: 'Outfit', sans-serif; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; }
    </style>
</head>
