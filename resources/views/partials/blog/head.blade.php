<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

  <!-- ** Basic Page Needs ** -->
  <meta charset="utf-8">
  <title>@yield('title', 'Xivig - Modern Elite Architecture')</title>

  <!-- ** Mobile Specific Metas ** -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Xivig - Modern High-Performance Laravel 13 Powered Web Architecture">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <meta name="author" content="Xivig">
  <meta name="generator" content="Xivig Elite Architecture">

  <!-- theme meta -->
  <meta name="theme-name" content="xivig" />

  <!-- Google Fonts Outfit & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- bootstrap.min css -->
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/bootstrap/bootstrap.min.css')}}">
  <!-- Icon Font Css -->
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/themify/css/themify-icons.css')}}">
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/fontawesome/css/all.min.css')}}">
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/magnific-popup/magnific-popup.css')}}">
  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/slick/slick.css')}}">
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/slick/slick-theme.css')}}">

  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="{{asset('xivig-blog/css/style.css')}}">
  <!-- Elite Modern Design System -->
  <link rel="stylesheet" href="{{asset('xivig-blog/css/elite-design.css')}}">

  <!--Favicon-->
  <link rel="icon" href="{{asset('xivig-blog/images/favicon.png')}}" type="image/x-icon">

</head>
