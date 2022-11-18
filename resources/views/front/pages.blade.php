@extends('front.menu')
@section('css')

@stop
@section('content')
<section class="parallax effect-section gray-bg">
    <div class="mask white-bg opacity-8"></div>
        <div class="container position-relative">
            <div class="row screen-65 align-items-center justify-content-center pb-4">
                <div class="col-lg-10 text-center">
                    <h6 class=" fw-bold">{{$set->title}}</h6>
                    <h1 class=" mb-4">{{$page->title}}</h1>
                </div>
            </div>
        </div>
        <div id="jarallax-container-0" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; overflow: hidden; pointer-events: none; z-index: -100;"><div style="background-size: cover; background-image: url(&quot;file:///Users/mac/Documents/Templates/themeforest-cDhKHVPF-raino-multipurpose-responsive-template/template/static/img/1600x900.jpg&quot;); position: absolute; top: 0px; left: 0px; width: 1440px; height: 420px; overflow: hidden; pointer-events: none; margin-top: 31.5px; transform: translate3d(0px, -74.5px, 0px); background-position: 50% 50%; background-repeat: no-repeat no-repeat;">
        </div>
    </div>
</section>
<section class="pb-5">
    <div class="container">
        <div class="row flex-row-reverse">
            <div class="col-lg-12 mb-3 align-items-center">
                <p>{!!$page->content!!}</p>
            </div>
        </div>
    </div>
</section>
@stop