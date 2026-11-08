<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark" data-theme="dark">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <meta name="generator" content="Xivig Elite Architecture">
  <meta name="theme-name" content="xivig" />

  <!-- Full-Fledged SEO & Social Intelligence Suite -->
  @include('partials.seo-meta')

  <!-- Google Fonts Outfit & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- bootstrap.min css -->
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/bootstrap/bootstrap.min.css')}}">
  <!-- Bootstrap Icons (Latest v1.13.1) -->
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/bootstrap-icons/bootstrap-icons.min.css')}}">
  <!-- Icon Font Css -->
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/themify/css/themify-icons.css')}}">
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/fontawesome/css/all.min.css')}}">
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/magnific-popup/magnific-popup.css')}}">
  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/slick/slick.css')}}">
  <link rel="stylesheet" href="{{asset('xivig-blog/plugins/slick/slick-theme.css')}}">

  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="{{asset('xivig-blog/css/style.css')}}?v={{ filemtime(public_path('xivig-blog/css/style.css')) }}">
  <!-- Elite Modern Design System -->
  <link rel="stylesheet" href="{{asset('xivig-blog/css/elite-design.css')}}?v={{ filemtime(public_path('xivig-blog/css/elite-design.css')) }}">

  <!-- Theme Controller Pre-loader & Extension Error Guard -->
  <script>
    (function() {
      var saved = localStorage.getItem('xivig-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      document.documentElement.setAttribute('data-bs-theme', saved);
      document.documentElement.setAttribute('data-theme', saved);

      window.addEventListener('unhandledrejection', function(e) {
        if (e.reason && (e.reason.message || '').includes('Could not establish connection')) {
          e.preventDefault();
        }
      });
      window.addEventListener('error', function(e) {
        if ((e.message || '').includes('Could not establish connection')) {
          e.preventDefault();
        }
      });
    })();
  </script>

  <!--Favicon-->
  <link rel="icon" href="{{asset('xivig-blog/images/favicon.png')}}" type="image/x-icon">

</head>
