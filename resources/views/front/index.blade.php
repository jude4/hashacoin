@extends('front.menu')
@section('css')

@stop
@section('content')
    {{-- <section class="effect-section dark-bg" style="background-size: cover; background-image: url('{{asset('asset/images/front.svg')}}'); background-position: center center; background-repeat: no-repeat no-repeat;">
    <div class="container">
        <div class="row full-screen align-items-center p-50px-tb lg-p-100px-t justify-content-center">
            <div class="col-lg-6 m-50px-tb md-m-20px-t">
                <p class="typed white-bg p-15px-lr d-inline-block dark-color border-radius-15">{{$set->title}}</p>
                <h1 class="display-4 m-20px-b text-white">{{getUi()->header_title}}</h1>
                <p class="lead m-35px-b text-white">{{getUi()->header_body}}</p>
                <div class="p-20px-t m-btn-wide">
                    @if (Auth::guard('user')->check())
                    <a class="m-btn m-btn-radius m-btn m-btn-theme-light" href="{{route('user.dashboard')}}">
                        <span class="m-btn-inner-text">{{__('Dashboard') }}</span>
                        <span class="m-btn-inner-icon arrow"></span>
                    </a>
                    @else
                    <a class="m-btn m-btn-radius m-btn-t-white m-10px-r" href="{{route('login')}}">
                        <span class="m-btn-inner-text">{{__('Sign In')}}</span>
                        <span class="m-btn-inner-icon arrow"></span>
                    </a>
                    <a class="m-btn m-btn-radius m-btn m-btn-theme-light" href="{{route('register')}}">
                        <span class="m-btn-inner-text">{{__('Get Started')}}</span>
                    </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 m-15px-tb">
                <img class="max-width-100" src="{{asset('asset/images/'.getUi()->s4_image)}}" title="" alt="">
            </div>
        </div>
    </div>
</section>
<section class="section p-0px-t section-top-up-100">
    <div class="container">
        <div class="row">
            @foreach (getService() as $val)
            <div class="col-sm-6 col-lg-3 m-15px-tb">
                <div class="p-25px-lr p-35px-tb gray-bg hover-top border-radius-15">
                    <h5 class="m-10px-b">{{$val->title}}</h5>
                    <p class="m-0px">{{$val->details}}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<div class="p-40px-tb border-top-1 border-bottom-1 border-color-gray">
    <div class="container">
        <div class="owl-carousel owl-loaded owl-drag" data-items="7" data-nav-dots="false" data-md-items="6" data-sm-items="5" data-xs-items="4" data-xx-items="3" data-space="30" data-autoplay="true">
            @foreach (getBrands() as $val)
            <div class="p8">
                <img src="{{asset('asset/brands/'.$val->image)}}" title="" alt="">
            </div>
            @endforeach
        </div>
    </div>
</div>

<section class="section effect-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 m-15px-tb">
                <h6 class="theme-color m-10px-b">We are {{$set->site_name}}</h6>
                <h3 class="h1 m-20px-b">{{getUi()->s8_title}}</h3>
                <p class="m-0px">{{getUi()->s8_body}}</p>
                <div class="p-25px-t row">
                    <div class="col-sm-6">
                        <ul class="list-type-01">
                            <li>Card Issuing</li>
                            <li>Open Banking</li>
                            <li>Mobile Money</li>
                            <li>Card Payment</li>
                            <li>Multiple currencies</li>
                            <li>Resell Virtual card</li>
                            <li>Transfer Money to Beneficiary</li>
                        </ul>
                    </div>
                    <div class="col-sm-6">
                        <ul class="list-type-01">
                            <li>HTML & API checkout</li>
                            <li>Inline Js & Plugins</li>
                            <li>Currency exchange</li>
                            <li>Payment links</li>
                            <li>Instant Checkout</li>
                            <li>Multiple business account</li>
                        </ul>
                    </div>
                </div>
                <div class="p-30px-t">
                    <a class="m-link-theme" href="{{route('developers')}}">Getting Started Docs</a>
                </div>
            </div>
            <div class="col-lg-6 m-15px-tb text-center">
                <img src="{{asset('asset/images/'.getUi()->s3_image)}}" class="rounded" title="" alt="">
            </div>
        </div>
    </div>
</section>

@if (count(getReview()) > 0)
<section class="p-50px-t">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-6">
                <img src="{{asset('asset/images/'.getUi()->s7_image)}}" title="" alt="">
            </div>
            <div class="col-lg-6 m-15px-tb">
                <h3 class="h1">{{getUi()->s3_title}}</h3>
                <p class="font-2 p-0px-t">{{getUi()->s3_body}}</p>
                <div class="border-left-2 border-color-theme p-25px-l m-35px-t">
                    <h6 class="font-2">{{$set->title}}</h6>
                    <p>{{getUi()->s6_title}}</p>
                </div>
                <div class="p-20px-t">
                    <a class="m-btn m-btn-radius m-btn m-btn-theme-light" href="{{route('about')}}">
                        <span class="m-btn-inner-text">More About Us</span>
                        <span class="m-btn-inner-icon arrow"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
<section class="section gray-bg">
    <div class="container">
        <div class="row justify-content-center md-m-25px-b m-40px-b">
            <div class="col-lg-6 text-center">
                <h3 class="h1 m-0px">{{getUi()->s6_body}}</h3>
                <div class="p-20px-t">
                    <a class="m-btn m-btn-dark m-btn-radius" href="{{route('register')}}">{{__('Sign Up for Free')}} </a>
                </div>
            </div>
        </div>
    </div>
</section> --}}



    <section class="banner-area-2 " id="banner_animation"
        style="background-size: auto; background-position: top left; background-image:asset/images/front.svg; padding-top:6rem;padding-bottom:6rem;" >
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="banner-content">
                        <img data-parallax='{"x": 0, "y": 250, "rotateZ":0}' class="shape" src="assets/img/banner/shape-3.png"
                            alt="shape">
                        <span class="btn " style="background: #f8f1f1;border-radius:20px;color:#000">{{$set->title}}</span>
                        <h1 class="wow fadeInUp mb-2 fw-bolder" style="font-size:46px;">{{getUi()->header_title}}</h1>
                        <p class="lead">{{getUi()->header_body}}</p>
                        <div class="p-5 m-btn-wide">
                            @if (Auth::guard('user')->check())
                                <a class="btn btn-warning" href="{{route('user.dashboard')}}">
                                    <span class="m-btn-inner-text">{{__('Dashboard') }}</span>
                                    <span class="m-btn-inner-icon arrow"></span>
                                </a>
                                @else
                                <a class="theme-btn theme-btn-primary  mt-30" href="{{route('login')}}" style="width:30%; ">
                                    <span class="m-btn-inner-text">{{__('Sign In')}}</span>
                                    <span class="m-btn-inner-icon arrow"></span>
                                </a>
                                <a class="theme-btn theme-btn-primary   mt-30" href="{{route('register')}}" style="width:50%; ">
                                    <span class="m-btn-inner-text">{{__('Get Started')}}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class=" col-md-6 col-lg-6 pt-40">
                    <div class="banner-img">
                        <img class="main-img img-fluid wow fadeInRight" src="{{asset('asset/images/'.getUi()->s4_image)}}" alt="banner-img">
                        <div class="shapes">
                            <img data-parallax='{"x": 0, "y": 130, "rotateZ":0}' class="shape-1"
                                src="assets/img/banner/shape-1.png" alt="shape">
                            <img data-parallax='{"x": 0, "y": -130, "rotateZ":0}' class="shape-2"
                                src="assets/img/banner/shape-2.png" alt="shape">
                            <img data-parallax='{"x": 250, "y":0, "rotateZ":0}' class="shape-3" src="assets/img/banner/shape-4.png"
                                alt="shape">
                            <img data-parallax='{"x": -200, "y": 250, "rotateZ":0}' class="shape-4"
                                src="assets/img/banner/shape-5.png" alt="shape">
                            <img class="shape-5" src="assets/img/banner/shape-6.png" alt="shape">
                            <img class="shape-6" src="assets/img/banner/shape-7.png" alt="shape">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- banner section -->

    
    

    <section class="feature-area-2 py-5" id="feature">
        <div class="container">
            <div class="feature">
                <div class="row gy-lg-0 gy-4">
                    @foreach (getService() as $val)
                    <div class="col-12 col-lg-3">
                        <div class="feature-widget-2 align-items-center wow fadeInRight feature-1" data-wow-delay="0.1s">

                            <div class="shapes">
                                <img src="/assets/img/feature/shape-6.png"  alt="shape">
                                <img src="/assets/img/feature/shape-10.png" alt="shape">
                                <img src="/assets/img/feature/shape-4.png" alt="shape">
                                <img src="/assets/img/feature/shape-3.png" alt="shape">
                                <img src="/assets/img/feature/shape-12.png" alt="shape">
                                <img src="/assets/img/feature/shape-12.png" alt="shape">
                            </div>

                            <div class="feature-img">
                                <img src="/assetsimg/feature/icon-5.png" alt="">
                            </div>
                            <div class="feature-content">
                                <h6>{{$val->title}}</h6>
                                <p> {{$val->details}}</p>
                            </div>
                        </div>
                    </div>
                    
                    @endforeach

                    {{-- <div class="col-12 col-lg-3">
                        <div class="feature-widget-2 align-items-center wow fadeInRight feature-2" data-wow-delay="0.3s">
                            <div class="shapes">
                                <img src="/assets/img/feature/shape-13.png" alt="shape">
                                <img src="/assets/img/feature/shape-14.png" alt="shape">
                                <img src="/assets/img/feature/shape-15.png" alt="shape">
                                <img src="/assets/img/feature/shape-11.png" alt="shape">
                                <img src="/assets/img/feature/shape-12.png" alt="shape">
                                <img src="/assets/img/feature/shape-5.png " alt="shape">
                            </div>
                            <div class="feature-img">
                                <img src="/assets/img/feature/icon-6.png" alt="">
                            </div>
                            <div class="feature-content">
                                <p>FROM 7.50%</p>
                                <h6>Offer Low Interest</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="feature-widget-2 align-items-center wow fadeInRight feature-3" data-wow-delay="0.5s">
                            <div class="shapes">
                                <img src="/assets/img/feature/shape-1.png" alt="shape">
                                <img src="/assets/img/feature/shape-2.png" alt="shape">
                                <img src="/assets/img/feature/shape-6.png" alt="shape">
                                <img src="/assets/img/feature/shape-9.png" alt="shape">
                                <img src="/assets/img/feature/shape-11.png" alt="shape">
                            </div>
                            <div class="feature-img">
                                <img src="/assets/img/feature/icon-7.png" alt="">
                            </div>
                            <div class="feature-content">
                                <p>7 DAYS PROCESS</p>
                                <h6>Fast & Easy Process</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="feature-widget-2 align-items-center wow fadeInRight feature-2" data-wow-delay="0.3s">
                            <div class="shapes">
                                <img src="/assets/img/feature/shape-13.png" alt="shape">
                                <img src="/assets/img/feature/shape-14.png" alt="shape">
                                <img src="/assets/img/feature/shape-15.png" alt="shape">
                                <img src="/assets/img/feature/shape-11.png" alt="shape">
                                <img src="/assets/img/feature/shape-12.png" alt="shape">
                                <img src="/assets/img/feature/shape-5.png " alt="shape">
                            </div>
                            <div class="feature-img">
                                <img src="/assets/img/feature/icon-6.png" alt="">
                            </div>
                            <div class="feature-content">
                                <p>FROM 7.50%</p>
                                <h6>Offer Low Interest</h6>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
            {{-- <div class="fast-e-loan pt-130">
                <div class="row gy-xl-0 gy-4">
                    <div class="col-xl-5 ">
                        <div class="section-title text-start">
                            <h2 class="mb-3">Fast, secure & easy loans in just 7 days</h2>
                            <p>Need some fast cash? Bad cradit history? We dont mind about your past,
                                just the
                                future.
                                Try loan start and feel secure in your future.</p>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 offset-xl-1 ">
                        <div class="apply-topics">
                            <ul>
                                <li><i class="icon_box-checked"></i>Get up to $15,000 Cash Fast</li>
                                <li><i class="icon_box-checked"></i>15 MinuteOnline Application</li>
                                <li><i class="icon_box-checked"></i>Centrelink Considered*</li>
                                <li><i class="icon_box-checked"></i>Bad Credit Considered2</li>
                            </ul>
                            <a href="loan.html" class="theme-btn mt-20 w-100">Apply for loans</a>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 ">
                        <div class="learn-more">
                            <ul>
                                <li><span>1.</span> Subject to verifcation, suitability and affordability</li>
                                <li class="mt-20"><span>2.</span> Your income from Centrelink must be less then 50%
                                    of your total
                                    income in
                                    order to qialify.</li>
                            </ul>
                            <a href="#" class="theme-btn theme-btn-light w-100 mt-30">Learn more</a>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </section>

    
    



    <section class="feature-area-2 py-5" id="feature">
        <div class="container">
            
            <div class="fast-e-loan ">
                <div class="row gy-xl-0 gy-4">
                    <div class="col-12 col-xl-7 col-md-6 ">
                        <img src="{{asset('asset/images/'.getUi()->s3_image)}}" class="rounded img-fluid" title="" alt="">
                    </div>

                    <div class="col-12 col-xl-5 col-md-6  ">
                        <div class="section-title text-start">
                            <h6 class="mb-3">We are {{$set->site_name}}</h6>
                            <h3 class="mb-3">{{getUi()->s8_title}}</h3>
                            <p >{{getUi()->s8_body}}</p>
                        </div>
                        <div class="apply-topics pt-3">
                            <div class="row">
                                <div class="col-6">
                                    <ul>
                                        <li><i class="icon_box-checked"></i> Card Issuing</li>
                                        <li><i class="icon_box-checked"></i> Open Banking</li>
                                        <li><i class="icon_box-checked"></i> Mobile Money</li>
                                        <li><i class="icon_box-checked"></i> Card Payment</li>
                                        <li><i class="icon_box-checked"></i> Multiple currencies</li>
                                        <li><i class="icon_box-checked"></i> Resell Virtual card</li>
                                    </ul>
                                    
                                </div>
                                <div class="col-6">
                                    <ul>
                                        <li><i class="icon_box-checked"></i> HTML & API checkout</li>
                                        <li><i class="icon_box-checked"></i> Inline Js & Plugins</li>
                                        <li><i class="icon_box-checked"></i> Currency exchange</li>
                                        <li><i class="icon_box-checked"></i> Payment links</li>
                                        <li><i class="icon_box-checked"></i> Instant Checkout</li>
                                        <li><i class="icon_box-checked"></i> Multiple business account</li>
                                        <li><i class="icon_box-checked"></i> Transfer Money to Beneficiary</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="pt-1">
                                <a class="theme-btn-2 mt-55" href="{{route('developers')}}">
                                    <span class="arrow">
                                        <span class="horizontal-line"></span>
                                    </span>Getting Started Docs
                                </a>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
 
    
    @if (count(getReview()) > 0)
    <section class="feature-area-2 py-5" id="feature">
        <div class="container">
            
            <div class="fast-e-loan ">
                <div class="row gy-xl-0 gy-4">
                    <div class="col-xl-6 col-md-6  order-md-1 order-2">
                        <div class="section-title text-start">
                            <h3 class="h1">{{getUi()->s3_title}}</h3>
                            <p class="pt-1">{{getUi()->s3_body}}</p>
                            <div class="learn-more pt-3 pb-3" >
                                <ul class="" style="border-left:2px solid  #0050b2;">
                                    <li>
                                        <h6 class="font-2">{{$set->title}}</h6>
                                        <p>{{getUi()->s6_title}}</p>
                                    </li>
                                </ul>
                            </div>
                            <div class="pt-2">
                                {{-- <a class="m-btn m-btn-radius m-btn m-btn-theme-light" href="{{route('about')}}"> --}}
                                    <a class="theme-btn-2 mt-55" href="{{route('about')}}">
                                        <span class="arrow">
                                            <span class="horizontal-line"></span>
                                        </span>More About Us
                                    </a>
                                    {{-- <span class="m-btn-inner-icon arrow"></span>
                                </a> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 order-md-2 order-1">
                        <img src="{{asset('asset/images/'.getUi()->s7_image)}}" class="img-fluid" title="" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    



    {{-- <section class="manage-c-finance pt-125 pb-140">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="section-title">
                        <h2 class="wow fadeInUp">Lorem ipsum dolor sit amet.</h2>
                        <p class="wow fadeInUp" data-wow-delay="0.3s">To replace or complement your bank</p>
                    </div>
                </div>
            </div>

            <div class="row pt-60 gy-4 gy-xl-0">
                <div class="col-xl-3 col-md-6">
                    <div class="feature-card-widget-2 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="icon-bg-1">

                            <img src="img/corporate-finance/icon-5.svg" alt="icon">
                        </div>
                        <h5>Freelancers</h5>
                        <p>The best business account to send and receive payments on a daily basis.</p>
                        <a href="#" class="theme-btn theme-btn-outlined mt-45">Find Out More</a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="feature-card-widget-2 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="icon-bg-2">

                            <img src="img/corporate-finance/icon-2.svg" alt="icon">
                        </div>
                        <h5>SMBs & Startups</h5>
                        <p>Optimize your team's expenses by always staying in control.</p>
                        <a href="#" class="theme-btn theme-btn-outlined mt-45">Find Out More</a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="feature-card-widget-2 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="icon-bg-3">

                            <img src="img/corporate-finance/icon-3.svg" alt="icon">
                        </div>
                        <h5>Business Founders</h5>
                        <p>Open a business account for the online deposit of your share capital.</p>
                        <a href="#" class="theme-btn theme-btn-outlined mt-45">Find Out More</a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="feature-card-widget-2 wow fadeInUp" data-wow-delay="0.7s">
                        <div class="icon-bg-4">

                            <img src="img/corporate-finance/icon-4.svg" alt="icon">
                        </div>
                        <h5>Microbusinesses</h5>
                        <p>Stay focused on your core business by managing your finances and accounting.</p>
                        <a href="#" class="theme-btn theme-btn-outlined mt-45">Find Out More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>         --}}


    <section class="security-area">
        <div class="security-priority pt-90 pb-95 text-center">
            <div class="shapes">
                <img src="/assets/img/security-tips/shape-1.png" alt="shape">
                <img src="/assets/img/security-tips/shape-2.png" alt="shape">
                <img src="/assets/img/security-tips/shape-3.png" alt="shape">
                <img src="/assets/img/security-tips/shape-4.png" alt="shape">
                <img src="/assets/img/security-tips/shape-5.png" alt="shape">
                <img src="/assets/img/security-tips/shape-6.png" alt="shape">
                <img src="/assets/img/security-tips/shape-1.png" alt="shape">
                <img data-parallax='{"x": -60, "y": 150, "rotateZ":-15}' src="/assets/img/security-tips/shape-7.png"
                    alt="shape">
                <img data-parallax='{"x": 0, "y": -150, "rotateZ":0}' src="/assets/img/security-tips/shape-8.png" alt="shape">
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-6 mx-auto">
                        <img src="/assets/img/security-tips/security-priority.png" alt="icon">
                        <h2 class="mt-4 mb-3">{{getUi()->s6_body}}</h2>
                        <a class="theme-btn-2 mt-55 text-white" href="{{route('register')}}">
                            <span class="arrow">
                                <span class="horizontal-line"></span>
                            </span>{{__('Sign Up for Free')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section>           
        <div class="container">
            <div class="row">
                <div class="col-12 pt-5">
                    <h3 class="font-2">Trusted by:</h3>
                </div>
                <div class="col-12">
                    <div class="owl-slider">
                        <div id="carousel" class="owl-carousel carousel-section">
                            @foreach (getBrands() as $val)
                            <div class="item">
                                <img src="{{asset('asset/brands/'.$val->image)}}" class="img-fluid w-50" title="" alt="">
                            </div>
                            @endforeach
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </section>


@stop
