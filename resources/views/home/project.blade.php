@extends('home')
@section('title', 'Latest works')
@section('page-title', 'Latest works')
@section('page-heading', 'Portfolio')
@section('content')
<!-- section portfolio start -->
<section class="section portfolio pb-0">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-7 text-center">
				<div class="section-title">
					<span class="h6 text-color">Our works</span>
					<h2 class="mt-3 content-title">We have done lots of works, lets check some</h2>
				</div>
			</div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="row portfolio-gallery">
			<div class="col-lg-4 col-md-6">
				<div class="portflio-item position-relative mb-4">
					<a href="{{asset('xivig-blog/images/portfolio/1.jpg')}}" class="popup-gallery">
						<img src="{{asset('xivig-blog/images/portfolio/1.jpg')}}" alt="portfolio" class="img-fluid w-100">

						<i class="ti-plus overlay-item"></i>
						<div class="portfolio-item-content">
							<h3 class="mb-0 text-white">Project california</h3>
							<p class="text-white-50">Web Development</p>
						</div>
					</a>
				</div>
			</div>

			<div class="col-lg-4 col-md-6">
				<div class="portflio-item position-relative mb-4">
					<a href="{{asset('xivig-blog/images/portfolio/2.jpg')}}" class="popup-gallery">
						<img src="{{asset('xivig-blog/images/portfolio/2.jpg')}}" alt="portfolio" class="img-fluid w-100">

						<i class="ti-plus overlay-item"></i>
						<div class="portfolio-item-content">
							<h3 class="mb-0 text-white">Project california</h3>
							<p class="text-white-50">Web Development</p>
						</div>
					</a>
				</div>
			</div>

			<div class="col-lg-4 col-md-6">
				<div class="portflio-item position-relative mb-4">
					<a href="{{asset('xivig-blog/images/portfolio/3.jpg')}}" class="popup-gallery">
						<img src="{{asset('xivig-blog/images/portfolio/3.jpg')}}" alt="portfolio" class="img-fluid w-100">

						<i class="ti-plus overlay-item"></i>
						<div class="portfolio-item-content">
							<h3 class="mb-0 text-white">Project california</h3>
							<p class="text-white-50">Web Development</p>
						</div>
					</a>
				</div>
			</div>

			<div class="col-lg-4 col-md-6">
				<div class="portflio-item position-relative mb-4">
					<a href="{{asset('xivig-blog/images/portfolio/4.jpg')}}" class="popup-gallery">
						<img src="{{asset('xivig-blog/images/portfolio/4.jpg')}}" alt="portfolio" class="img-fluid w-100">

						<i class="ti-plus overlay-item"></i>
						<div class="portfolio-item-content">
							<h3 class="mb-0 text-white">Project california</h3>
							<p class="text-white-50">Web Development</p>
						</div>
					</a>
				</div>
			</div>

			<div class="col-lg-4 col-md-6">
				<div class="portflio-item position-relative  mb-4">
					<a href="{{asset('xivig-blog/images/portfolio/5.jpg')}}" class="popup-gallery">
						<img src="{{asset('xivig-blog/images/portfolio/5.jpg')}}" alt="portfolio" class="img-fluid w-100">

						<i class="ti-plus overlay-item"></i>
						<div class="portfolio-item-content">
							<h3 class="mb-0 text-white">Project california</h3>
							<p class="text-white-50">Web Development</p>
						</div>
					</a>
				</div>
			</div>

			<div class="col-lg-4 col-md-6">
				<div class="portflio-item position-relative mb-4">
					<a href="{{asset('xivig-blog/images/portfolio/6.jpg')}}" class="popup-gallery">
						<img src="{{asset('xivig-blog/images/portfolio/6.jpg')}}" alt="portfolio" class="img-fluid w-100">

						<i class="ti-plus overlay-item"></i>
						<div class="portfolio-item-content">
							<h3 class="mb-0 text-white">Project california</h3>
							<p class="text-white-50">Web Development</p>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- section portfolio END -->
@endsection
