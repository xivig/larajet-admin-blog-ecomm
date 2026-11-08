@include('blog.partials.head')
@include('blog.partials.header')

@if(($title ?? '') !== 'Home page')
    @include('blog.partials.common')
@endif
@yield('content')
@include('blog.partials.footer')

