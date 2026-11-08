<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

  <!-- ** Basic Page Needs ** -->
  <meta charset="utf-8">
  <title>@yield('title', 'Home page')</title>

  <!-- ** Mobile Specific Metas ** -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Agency HTML Template">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <meta name="author" content="Xivig">
  <meta name="generator" content="Xivig Html5 Agency Template v1.0">

  <!-- theme meta -->
  <meta name="theme-name" content="xivig" />

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

  <!--Favicon-->
  <link rel="icon" href="{{asset('xivig-blog/images/favicon.png')}}" type="image/x-icon">

</head>
