@extends('front.menu')
@section('css')

@stop
@section('content')
<section class="parallax effect-section gray-bg" style="background:url('assets/img/new/blog.jpg'); 
backgrround-size:cover;
background-position:center; background-color:rgba(0, 0, 0, .8); background-blend-mode:overlay; color:white;">
    <div class="container position-relative">
        <div class="row screen-50 align-items-center justify-content-center p-100px-t">
            <div class="col-lg-10 text-center">
                <h6 class="text-white font-w-500">{{$set->title}}</h6>
                <h1 class="text-white ">{{$title}}</h1>
            </div>
        </div>
    </div>
</section>
{{-- <section class="section gray-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 blog-listing p-40px-r lg-p-15px-r">
                <div class="row">
                    @foreach(getBlog() as $vblog)
                    <div class="col-sm-6">
                        <div class="card blog-grid-1 box-shadow-hover">
                            <div class="blog-img">
                                <a href="{{url('/')}}/single/{{$vblog->id}}/{{str_slug($vblog->title)}}">
                                    <img src="{{asset('asset/thumbnails/'.$vblog->image)}}" title="" alt="">
                                </a>
                                <span class="date">{{date("j", strtotime($vblog->created_at))}}<span>{{date("M", strtotime($vblog->created_at))}}</span></span>
                            </div>
                            <div class="card-body blog-info">
                                <h5>
                                    <a href="{{url('/')}}/single/{{$vblog->id}}/{{str_slug($vblog->title)}}">{!! str_limit($vblog->title, 40);!!}..</a>
                                </h5>
                                <p class="m-0px">{!! str_limit($vblog->details, 80);!!}</p>
                                <div class="btn-bar">
                                    <a class="m-link-theme" href="{{url('/')}}/single/{{$vblog->id}}/{{str_slug($vblog->title)}}">Read more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
        </div>
    </div>
</section> --}}



<!-- Blog Posts start-->
<section class="pt-120 pb-120 bg_disable">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="blog-post-widget">
                    <div class="row gy-4 ">
                        @foreach(getBlog() as $vblog)
                        <div class="col-md-4">
                            <div class="blog-widget-2 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="blog-img">
                                    <a href="{{url('/')}}/single/{{$vblog->id}}/{{str_slug($vblog->title)}}">
                                        <img src="{{asset('asset/thumbnails/'.$vblog->image)}}" title="" class="img-fluid" alt="">
                                    </a>
                                    <div class="catagory bg_primary">{{date("j", strtotime($vblog->created_at))}}<span>{{date("M", strtotime($vblog->created_at))}}</div>
                                </div>
                                <div class="blog-content">
                                    <h4><a href="{{url('/')}}/single/{{$vblog->id}}/{{str_slug($vblog->title)}}">{!! str_limit($vblog->title, 40);!!}..</a></h4>
                                    <p>{!! str_limit($vblog->details, 80);!!}</p>
                                    <div class="post-info">
                                        <div class="author">
                                            {{-- <a class="brn" href="{{url('/')}}/single/{{$vblog->id}}/{{str_slug($vblog->title)}}"></a> --}}
                                            <a href="{{url('/')}}/single/{{$vblog->id}}/{{str_slug($vblog->title)}}" class="mt-25">Read more <i class="arrow_right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                    </div>
                    {{-- <div class="row mt-55">
                        <div class="col-12">
                            <div class="pagination-widget">
                                <ul>
                                    <li><a class="active" href="#">1</a></li>
                                    <li><a href="#">2</a></li>
                                    <li><a href="#">3</a></li>
                                    <li><a href="#"> <i class="arrow_carrot-right "></i> </a></li>
                                </ul>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
            {{-- Sidebar --}}
        </div>
    </div>
</section>
<!-- Blog Posts end-->
@stop