@extends('front.menu')
@section('css')

@stop
@section('content')

<section class="parallax effect-section gray-bg" style="background:url('assets/img/new/about.jpg'); 
backgrround-size:cover;
background-position:left; background-color:rgba(0, 0, 0, .8); background-blend-mode:overlay; color:white;">
    <div class="mask white-bg opacity-8"></div>
        <div class="container position-relative">
            <div class="row screen-65 align-items-center justify-content-center p-100px-tb">
                <div class="col-lg-10 text-center">
                    <h6 class="text-white font-w-500">{{$set->title}}</h6>
                    <h1 class="text-white m-20px-b">{{__('About')}} {{$set->site_name}}</h1>
                </div>
            </div>
        </div>
        <div id="jarallax-container-0" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; overflow: hidden; pointer-events: none; z-index: -100;"><div style="background-size: cover; background-image: url(&quot;file:///Users/mac/Documents/Templates/themeforest-cDhKHVPF-raino-multipurpose-responsive-template/template/static/img/1600x900.jpg&quot;); position: absolute; top: 0px; left: 0px; width: 1440px; height: 420px; overflow: hidden; pointer-events: none; margin-top: 31.5px; transform: translate3d(0px, -74.5px, 0px); background-position: 50% 50%; background-repeat: no-repeat no-repeat;">
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row flex-row-reverse">
            <div class="col-lg-12 mb-4 align-items-center">
               <p>{!!getAbout()->about!!}</p>
            </div>
        </div>
    </div>
</section>





@if(count(getReview())>0)
<section class="testimonial-section">
    <div class="large-container">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="">
                        <h2 style="font-size: 30px; inline-size:400px">{{getUi()->s7_title}}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="thumb-layer paroller">
                        {{-- <figure class="image"> --}}
                            <img src="{{asset('asset/images/'.getUi()->s7_image)}}" alt="">
                        {{-- </figure>  --}}
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="testimonial-carousel owl-carousel owl-theme">
                        @foreach(getReview() as $vreview)
                        <!-- Testimonial Block -->
                        <div class="testimonial-block">
                            <div class="inner-box">
                                <div class="text">{{$vreview->review}}</div>
                                <div class="info-box">
                                    <div class="thumb"><img src="{{asset('asset/review/'.$vreview->image_link)}}" alt=""></div>
                                    <h4 class="name">{{$vreview->name}}</h4>
                                    <span class="designation">{{$vreview->occupation}}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach            
                    </div>
                </div>
            </div>
        </div>

        

        
    </div>
</section>
@endif




@stop


@include('partials.main')