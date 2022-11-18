{{-- <!doctype html>
<html class="no-js" lang="en">

<head>
    <base href="{{url('/')}}" />
    <title>{{ $title }} - {{$set->site_name}}</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="index, follow">
    <meta name="apple-mobile-web-app-title" content="{{$set->site_name}}" />
    <meta name="application-name" content="{{$set->site_name}}" />
    <meta name="msapplication-TileColor" content="#ffffff" />
    <meta name="description" content="{{$set->site_desc}}" />
    <link rel="shortcut icon" href="{{asset('asset/'.$logo->image_link2)}}" />
    <link href="{{asset('asset/fonts/fontawesome/css/all.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('asset/static/plugin/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/plugin/font-awesome/css/all.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/plugin/et-line/style.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/plugin/themify-icons/themify-icons.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/plugin/ionicons/css/ionicons.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/plugin/owl-carousel/css/owl.carousel.min.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/plugin/magnific/magnific-popup.css')}}" rel="stylesheet">
    <link href="{{asset('asset/static/style/master.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/prism/prism.css')}}">
    <link rel="stylesheet" href="{{asset('asset/dashboard/css/docs.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('asset/css/toast.css')}}" type="text/css">
    @yield('css')
    @include('partials.font')
</head>

<body data-spy="scroll" data-target="#navbar-collapse-toggle" data-offset="98">
    <!-- Header -->
    <header class="header-nav @if(route('home') == url()->current()) header-dark @else header-white @endif">
        <div class="fixed-header-bar">
            <!-- Header Nav -->
            <div class="navbar navbar-main navbar-expand-lg">
                <div class="container">
                    <a class="navbar-brand" href="{{url('/')}}">
                        <img class="nav-img" alt="logo" src="{{asset('asset/'.$logo->dark)}}">
                    </a>
                    <button class="navbar-toggler"   type="button" data-toggle="collapse" data-target="#navbar-main-collapse" aria-controls="navbar-main-collapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon" style="@if(route('home') == url()->current()) color:#fff; background:#fff; @else color:#000; background:#000; @endif"></span>
                    </button>
                    <div class="collapse navbar-collapse navbar-collapse-overlay collaspe" id="navbar-main-collapse">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('pricing')}}">{{__('Pricing')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('developers')}}">{{__('Developers')}}</a>
                            </li>
                            <li class="nav-item mm-in px-dropdown">
                                <a class="nav-link">{{__('Help')}}</a>
                                <i class="fa fa-angle-down px-nav-toggle"></i>
                                <ul class="px-dropdown-menu mm-dorp-in">
                                    <li><a href="{{route('faq')}}">{{__('Knowledge base')}}</a></li>
                                    <li><a href="{{route('contact')}}">{{__('Contact us')}}</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('blog')}}">{{__('News & Articles')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('about')}}">{{__('Why')}} {{$set->site_name}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- End Header Nav -->
        </div>
    </header>
    <!-- Header End -->
    <!-- Main -->
    <main>
        @yield('content')
        <footer class="dark-bg footer effect-section">
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p class="text-white">{{$set->site_desc}}</p>
                                    <ul class="list-unstyled links-white footer-link-1">
                                        @if($set->mobile!=null)
                                        <li><a href="javascript:void;"><i class="fal fa-phone-alt"></i> {{$set->mobile}}</a></li>
                                        @endif
                                        @if($set->email!=null)
                                        <li><a href="javascript:void;"><i class="fal fa-envelope"></i> {{$set->email}}</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-3 m-15px-tb">
                                    <h5 class="footer-title text-white">
                                        {{__('Quick link')}}
                                    </h5>
                                    <ul class="list-unstyled links-white footer-link-1">
                                        <li><a href="{{route('developers')}}">{{__('Developers')}}</a></li>
                                        <li><a href="{{route('pricing')}}">{{__('Pricing')}}</a></li>
                                        <li><a href="{{route('blog')}}">{{__('News & Articles')}}</a></li>
                                        <li><a href="{{route('about')}}">{{__('Why')}} {{$set->site_name}}</a></li>
                                    </ul>
                                </div>                              
                                <div class="col-lg-3 m-15px-tb">
                                    <h5 class="footer-title text-white">
                                        {{__('Help')}}
                                    </h5>
                                    <ul class="list-unstyled links-white footer-link-1">
                                        <li><a href="{{route('contact')}}">{{__('Contact us')}}</a></li>
                                        <li><a href="{{route('faq')}}">{{__('Knowledge base')}}</a></li>
                                        <li><a href="{{route('terms')}}">{{__('Terms of Use')}}</a></li>
                                        <li><a href="{{route('privacy')}}">{{__('Privacy Policy')}}</a></li>
                                    </ul>
                                </div>
                                <div class="col-lg-3 m-15px-tb">
                                    <h5 class="footer-title text-white">
                                        {{__('More')}}
                                    </h5>
                                    <ul class="list-unstyled links-white footer-link-1">
                                        @foreach(getPage() as $vpages)
                                        @if(!empty($vpages))
                                        <li><a href="{{asset('')}}page/{{$vpages->id}}">{{$vpages->title}}</a></li>
                                        @endif
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-lg-3 m-15px-tb">
                                    <h5 class="footer-title text-white">
                                        {{__('Social Media')}}
                                    </h5>
                                    <ul class="list-unstyled links-white footer-link-1">
                                        @foreach(getSocial() as $socials)
                                        @if(!empty($socials->value))
                                        <li><a href="{{$socials->value}}">{{ucwords($socials->type)}}</a></li>
                                        @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom footer-border-dark">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-right m-5px-tb">
                            <ul class="nav justify-content-center justify-content-md-start links-dark font-small footer-link-1">
                            </ul>
                        </div>
                        <div class="col-md-6 text-center text-md-right m-5px-tb">
                            <p class="m-0px font-small text-white">{{$set->site_name}} &copy; {{date('Y')}}. {{__('All Rights Reserved')}}.</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        </div>
        {!!$set->livechat!!}
        {!!$set->analytic_snippet!!}
        <script>
            var urx = "{{asset('/')}}";
        </script>
        <script src="{{asset('asset/static/js/jquery-3.2.1.min.js')}}"></script>
        <script src="{{asset('asset/static/js/jquery-migrate-3.0.0.min.js')}}"></script>
        <script src="{{asset('asset/static/plugin/appear/jquery.appear.js')}}"></script>
        <script src="{{asset('asset/static/plugin/bootstrap/js/popper.min.js')}}"></script>
        <script src="{{asset('asset/static/plugin/bootstrap/js/bootstrap.js')}}"></script>
        <script src="{{asset('asset/static/js/custom.js')}}"></script>
        <script src="{{asset('asset/js/toast.js')}}"></script>
        <script src="{{asset('asset/dashboard/vendor/prism/prism.js')}}"></script>
        @yield('script')
        @if (session('success'))
        <script>
            "use strict";
            toastr.success("{{ session('success') }}");
        </script>
        @endif

        @if (session('alert'))
        <script>
            "use strict";
            toastr.warning("{{ session('alert') }}");
        </script>
        @endif

</body>

</html> --}}





<!DOCTYPE HTML>
<html lang="en-US">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>{{ $title }} - {{$set->site_name}}</title>
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">

    <!-- CSS here -->
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/assets/css/elegant-icons.min.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/assets/css/all.min.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/assets/css/animate.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/assets/css/nice-select.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/assets/css/default.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/assets/css/responsive.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/assets/css/testimoial.css" media="all" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    {{-- <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'> --}}
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.1/assets/owl.carousel.min.css'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.1/assets/owl.theme.default.css'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.css'>


</head>

<body>
    
    <!-- Header -->
    <header class="header @if(route('home') == url()->current()) header-dark @else header-white @endif">
        <div class="header-menu header-menu-2 " id="sticky">
            <nav class="navbar navbar-expand-lg ">
                <div class="container">
                    <a class="navbar-brand" href="{{url('/')}}">
                        <img src="{{asset('asset/'.$logo->dark)}}" srcset="{{asset('asset/'.$logo->dark)}}" width="100" alt="logo">
                    </a>
                    <button class="navbar-toggler collapsed "  type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="menu_toggle" style="@if(route('home') == url()->current()) color:#fff; background:#fff; @else color:#000; background:#000; @endif">
                            <span class="hamburger">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                            <span class="hamburger-cross">
                                <span></span>
                                <span></span>
                            </span>
                        </span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav menu ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('pricing')}}">{{__('Pricing')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('developers')}}">{{__('Developers')}}</a>
                            </li>
                            
                            <li class="nav-item dropdown submenu">
                                <a class="nav-link dropdown-toggle" href="javascript:void()" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{__('Help')}}
                                </a>
                                <i class="arrow_carrot-down_alt2 mobile_dropdown_icon" aria-hidden="false" data-bs-toggle="dropdown"></i>
                                <ul class="dropdown-menu">
                                    <li class="nav-item"><a class="nav-link" href="{{route('faq')}}">{{__('Knowledge base')}}</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{route('contact')}}">{{__('Contact us')}}</a></li>
                                    
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('blog')}}">{{__('News & Articles')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('about')}}">{{__('Why')}} {{$set->site_name}}</a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <!-- Header end-->

    <main>
        @yield('content')



    </main>

    <!-- footer -->
    <footer class="footer footer-3">
        <div class="footer-top" style="padding:4rem  0 2rem;">
            <div class="container">
                <div class="row gx-0  ">

                    <div class="col-12 col-lg-4 col-sm-6 text-center text-sm-start ms-0 ">
                        <div class="footer-widget wow fadeInLeft mb-30">
                            <div class="footer-text mb-20">
                                <p class="text-white">{{$set->site_desc}}</p>
                            </div>
                            <div class="footer-link">
                                <ul>
                                    @if($set->mobile!=null)
                                        <li><a href="javascript:void;"><i class="fa fa-phone-alt"></i> {{$set->mobile}}</a></li>
                                        @endif
                                        @if($set->email!=null)
                                        <li><a href="javascript:void;"><i class="fa fa-envelope"></i> {{$set->email}}</a></li>
                                        @endif
                                </ul>
                            </div>
                        </div>
                    </div>


                    <div class="col-12 col-lg-2 col-sm-6 text-center text-sm-start ms-lg-5 ">
                        <div class="footer-widget mb-30 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="f-widget-title">
                                <h5>{{__('Quick link')}}</h5>
                            </div>
                            <div class="footer-link">
                                <ul>
                                    <li><a href="{{route('developers')}}">{{__('Developers')}}</a></li>
                                    <li><a href="{{route('pricing')}}">{{__('Pricing')}}</a></li>
                                    <li><a href="{{route('blog')}}">{{__('News & Articles')}}</a></li>
                                    <li><a href="{{route('about')}}">{{__('Why')}} {{$set->site_name}}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-2  col-sm-6 text-center text-sm-start ms-lg-5">
                        <div class="footer-widget mb-30 wow fadeInUp" data-wow-delay="0.3s">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="f-widget-title">
                                        <h5>{{__('Help')}}</h5>
                                    </div>
                                    <div class="footer-link">
                                        <ul>
                                            <li><a href="{{route('contact')}}">{{__('Contact us')}}</a></li>
                                            <li><a href="{{route('faq')}}">{{__('Knowledge base')}}</a></li>
                                            <li ><a style="font-size: 13.4px !important;" href="{{route('terms')}}">{{__('Terms of Use')}}</a></li>
                                            <li ><a style="font-size: 13.4px !important;" href="{{route('privacy')}}">{{__('Privacy Policy')}}</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="f-widget-title">
                                        <h5>{{__('More')}}</h5>
                                    </div>
                                    <div class="footer-link">
                                        <ul>
                                            @foreach(getPage() as $vpages)
                                            @if(!empty($vpages))
                                            <li><a href="{{asset('')}}page/{{$vpages->id}}">{{$vpages->title}}</a></li>
                                            @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                    </div>
                    {{-- <div class="col-12 col-lg-1 col-sm-6 text-center text-sm-start me-0 ms-lg-5">
                        <div class="footer-widget mb-30 wow fadeInUp" data-wow-delay="0.5s">
                            <div class="f-widget-title">
                                <h5>{{__('More')}}</h5>
                            </div>
                            <div class="footer-link">
                                <ul>
                                    @foreach(getPage() as $vpages)
                                    @if(!empty($vpages))
                                    <li><a href="{{asset('')}}page/{{$vpages->id}}">{{$vpages->title}}</a></li>
                                    @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-12 col-lg-2 col-sm-6 text-center text-sm-start me-0 ms-lg-5">
                        <div class="footer-widget mb-30 wow fadeInUp" data-wow-delay="0.5s">
                            <div class="f-widget-title">
                                <h5>{{__('Social Media')}}</h5>
                            </div>
                            <div class="footer-link">
                                <ul>
                                    @foreach(getSocial() as $socials)
                                    @if(!empty($socials->value))
                                    <li><a href="{{$socials->value}}">{{ucwords($socials->type)}}</a></li>
                                    @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- copyright area -->
        <div class="copyright pt-25 pb-25">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12 text-center my-3 my-sm-0">
                        <div class="copyright-text">
                            <p> {{$set->site_name}} &copy; {{date('Y')}}. {{__('All Rights Reserved')}}.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer end -->

    <!-- Back to top button -->
    <a id="back-to-top" title="Back to Top"></a>

    <!-- JS here -->
    <script type="text/javascript" src="/assets/js/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="/assets/js/preloader.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js'>
    <script type="text/javascript" src="/assets/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="/assets/js/jquery.smoothscroll.min.js"></script>
    <script type="text/javascript" src="/assets/js/jquery.waypoints.min.js"></script>
    <script type="text/javascript" src="/assets/js/jquery.counterup.min.js"></script>
    <script type="text/javascript" src="/assets/js/jquery.nice-select.min.js"></script>
    <script type="text/javascript" src="/assets/js/parallax.js"></script>
    <script type="text/javascript" src="/assets/js/jquery.parallax-scroll.js"></script>
    <script type="text/javascript" src="/assets/js/wow.min.js"></script>
    @include('partials.script')
    @include('partials.main')
    

    <script type="text/javascript" src="/assets/js/custom.js"></script>

    @if (session('success'))
    <script>
        "use strict";
        toastr.success("{{ session('success') }}");
    </script>
    @endif

    @if (session('alert'))
    <script>
        "use strict";
        toastr.warning("{{ session('alert') }}");
    </script>
    @endif

</body>
</html>    