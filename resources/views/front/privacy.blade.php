@extends('front.menu')
@section('css')

@stop
@section('content')
<section class="parallax effect-section gray-bg" style="background:url('assets/img/new/prvacy.jpg'); 
backgrround-size:cover;
background-position:center; background-color:rgba(0, 0, 0, .8); background-blend-mode:overlay; color:white;">
    <div class="mask white-bg opacity-8"></div>
        <div class="container position-relative">
            <div class="row screen-65 align-items-center justify-content-center p-100px-tb">
                <div class="col-lg-10 text-center">
                    <h6 class="text-white font-w-500">{{$set->title}}</h6>
                    <h1 class="text-white m-20px-b">{{__('Privacy policy')}}</h1>
                </div>
            </div>
        </div>
        <div id="jarallax-container-0" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; overflow: hidden; pointer-events: none; z-index: -100;"><div style="background-size: cover; background-image: url(&quot;file:///Users/mac/Documents/Templates/themeforest-cDhKHVPF-raino-multipurpose-responsive-template/template/static/img/1600x900.jpg&quot;); position: absolute; top: 0px; left: 0px; width: 1440px; height: 420px; overflow: hidden; pointer-events: none; margin-top: 31.5px; transform: translate3d(0px, -74.5px, 0px); background-position: 50% 50%; background-repeat: no-repeat no-repeat;">
        </div>
    </div>
</section>


<section class="job-application-area pt-110 pb-120 bg_white">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="job-description-widget">
                    <div class="single-description-para">
                        <h6>{{__('Privacy policy')}}</h6>
                        <p>{!!getAbout()->privacy_policy!!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop