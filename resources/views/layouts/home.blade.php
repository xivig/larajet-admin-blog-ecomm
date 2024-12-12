@include('partials.blog.head')
@include('partials.blog.header')

@if(($title ?? '') !== 'Home page')
    @include('partials.blog.common')
@endif
@yield('content')
@include('partials.blog.footer')
