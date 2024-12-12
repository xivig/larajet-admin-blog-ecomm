@use('Illuminate\Support\Str')
@extends('blog')
@section('title', 'Blog page')
{{--@section('title', 'Contact Us')--}}
{{--@section('page-title', 'Contact Us')--}}
{{--@section('page-heading', 'Get in Touch')--}}
@section('content')
    <section class="section blog-wrap bg-gray">
        <div class="container">
            @if(count($posts)> 0)
                <div class="row">
                    @foreach($posts as $post)

                        <div class="col-lg-6 col-md-6 mb-5">
                            <div class="blog-item">

                                <img loading="lazy" src="{{$post->image->link}}" alt="blog"
                                     class="img-fluid rounded">

                                <div class="blog-item-content bg-white p-4">
                                    <div class="blog-item-meta  py-1 px-2">
                                        <span class="text-primary text-capitalize mr-3"><i class="ti-pencil-alt mr-2"></i>{{$post->category->name}}</span>
                                        <span class="text-danger text-capitalize mr-3"><i class="ti-user mr-2"></i>{{$post->user ? $post->user->name : ''}}</span>
                                        <span class="text-warning text-capitalize mr-3"><i class="ti-bookmark mr-2"></i>{{$post->tag->name}}</span>
                                        <span class="text-success text-capitalize mr-3"><i class="ti-crown mr-2"></i>{{$post->author->nickname ?? ''}}</span>
                                    </div>

                                    <h3 class="mt-3 mb-3"><a
                                            href="{{route('single-post', $post->slug)}}">{{Str::limit($post->title, 20, '...')}}</a>
                                    </h3>
                                    <p class="mb-4">{{Str::limit($post->excerpt, 50, '...')}}</p>

                                    <a href="{{route('single-post', $post->slug)}}"
                                       class="btn btn-small btn-main btn-round-full">Learn
                                        More</a>
                                </div>
                            </div>
                        </div>

                    @endforeach


                </div>
                <div class="row justify-content-center mt-5">
                    <div class="col-lg-6 text-center">

                        <nav class="navigation pagination d-inline-block">
                            <div class="nav-links">

                                {{--                            <a class="prev page-numbers" href="#">Prev</a>--}}
                                {{--                            <span aria-current="page" class="page-numbers current">1</span>--}}
                                {{--                            <a class="page-numbers" href="#">2</a>--}}
                                {{ $posts->links() }}
                                {{--                            <a class="next page-numbers" href="#">Next</a>--}}
                            </div>
                        </nav>
                    </div>
                </div>
            @else
                <div class="text-center text-warning">
                    <h3>No post Found</h3>
                </div>

            @endif

        </div>
    </section>
@endsection
