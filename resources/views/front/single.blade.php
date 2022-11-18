@extends('front.menu')
@section('css')

@stop
@section('content')
{{-- <section class="parallax effect-section gray-bg">
    <div class="container position-relative">
        <div class="row screen-40 align-items-center justify-content-center p-150px-t">
            <div class="col-lg-10 text-center">
                <h6 class="white-color-black font-w-500">{{$set->title}}</h6>
                <h1 class="display-4 black-color m-20px-b">{{$post->title}}</h1>
            </div>
        </div>
    </div>
</section>
<section class="section gray-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 p-40px-r lg-p-15px-r md-m-15px-tb">
                <div class="article-img">
                    <img src="{{asset('asset/thumbnails/'.$post->image)}}" title="{{$post->title}}" alt="{{$post->title}}">
                </div>
                <div class="article box-shadow">
                    <div class="article-content">
                        <p>{!!$post->details!!}</p>
                    </div>
                </div>
            </div>
            @include('partials.sidebar')
        </div>
    </div>
</section> --}}

 <!-- BreadCrumb start-->
 <section class="breadcrumb-area" id="banner_animation2">
    <div class="breadcrumb-widget breadcrumb-widget-2  pt-200 pb-145"
        style="background-image: url('/assets/img/breadcrumb/bg-3.jpg');">

        <div class="shapes">
            <div class="one-shape shape-3" data-parallax='{"x": -100, "y": 0, "rotateZ":0}'>
                <img src="/assets/img/breadcrumb/Polygon-3.png" alt="shape">
            </div>
            <div class="one-shape shape-4" data-parallax='{"x": -200, "y": 0, "rotateZ":0}'>
                <img src="/assets/img/breadcrumb/Polygon-4.png" alt="shape">
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="breadcrumb-content pt-50">
                        <h6 class="fw-bold text-white">{{$set->title}}</h6>
                        <h1 class="">{{$post->title}}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- BreadCrumb end-->


<!-- Blog Details start-->
<section class="pt-120 pb-120 bg_disable">
    <div class="container">
        <div class="row gy-lg-0 gy-4">
            <div class="col-lg-7">

                <div class="post-details-widget pb-70 border-bottom position-relative">
                    <div>
                        <img class="post-img w-100" src="{{asset('asset/thumbnails/'.$post->image)}}" title="{{$post->title}}" alt="{{$post->title}}">
                    </div>
                    <p>{!!$post->details!!}</p>
                </div>
                
                
            </div>

            @include('partials.sidebar')
        </div>
    </div>
</section>

<!-- Blog Details end-->
@stop