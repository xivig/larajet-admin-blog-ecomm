<section class="page-title bg-1">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="block text-center">
                    <span class="text-white">@yield('page-title')</span>
                    <h1 class="text-capitalize mb-4 text-lg">@yield('page-heading')</h1>
                    <ul class="list-inline">
                        <li class="list-inline-item"><a href="{{url('/')}}" class="text-white">Home</a></li>
                        <li class="list-inline-item"><span class="text-white">/</span></li>
                        <li class="list-inline-item text-white-50">@yield('page-title')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
