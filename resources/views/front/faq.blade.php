@extends('front.menu')
@section('css')

@stop
@section('content')
<section class="parallax effect-section gray-bg" style="background:url('/assets/img/new/basic.jpg'); 
backgrround-size:cover;
background-position:top; background-color:rgba(0, 0, 0, .8); background-blend-mode:overlay; color:white;">
    <div class="container position-relative">
        <div class="row screen-40 align-items-center justify-content-center pb-5">
            <div class="col-lg-10 text-center">
                <h6 class="text-white font-w-500">{{__('How can we help?')}}</h6>
                <h1 class="text-white mb-4">{{$title}}</h1>
                <form method="post" action="{{route('faq-submit')}}">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12 text-center">
                            <div class="form-group">
                                <input type="text" name="search" placeholder="Search the knowledge base..." class="form-control py-3">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
{{-- <section class="section gray-bg">
    <div class="container">
        <div class="row">
            @foreach(getFaqCategory() as $val)
            <div class="col-4">
                
                <ol class="list-group ">
                    <li class="list-group-item active" aria-current="true">{{$val->name}}</li>
                    @foreach(getFaq($val->id) as $vals)
                    <li class="list-group-item">
                        <a href="{{route('answer', ['id'=>$vals->id, 'slug'=>$vals->slug])}}">
                            <span class="d-block dark-color">{{$vals->question}}</span>
                        </a>
                       
                    </li>
                    @endforeach
                </ol>
               
                @if(count(getFaq($val->id))>4)
                    <a href="{{route('faq.all', ['id'=>$val->id, 'slug'=>$val->slug])}}">
                        <span class="d-block dark-color fw-bold">{{__('See all')}}</span>
                    </a>
                @endif
            </div>
            @endforeach
           
        </div>
    </div>
</section> --}}


<section class="how-it-work pt-125 pb-140">
    <div class="container">

        <div class="row pt-60 gy-lg-0 gy-4">

            @foreach(getFaqCategory() as $val)
            <div class="col-lg-4 ps-lg-0">
                <div class="single-widget apply-online wow fadeInUp" data-wow-delay="0.3s">
                    <div class="widget-header">
                        <div class="widget-img">
                            <img src="/assets/img/how-works/icon-2.png" alt="icon">
                        </div>
                        <div class="widget-title">
                            <h4>{{$val->name}}</h4>
                        </div>
                    </div>

                    <ul class="widget-content">
                        @foreach(getFaq($val->id) as $vals)
                        <li class="active"> 
                            <a href="{{route('answer', ['id'=>$vals->id, 'slug'=>$vals->slug])}}">
                                <span class="number"><i class="fa fa-check"></i></span>
                                <span class="text">{{$vals->question}}</span>
                            </a>
                            
                        </li>
                        @endforeach
                    </ul>
                    @if(count(getFaq($val->id))>4)
                    <a class="text-center" href="{{route('faq.all', ['id'=>$val->id, 'slug'=>$val->slug])}}">
                        <span class="d-block py-4 text-primary dark-color fw-bold">{{__('See all')}}</span>
                    </a>
                   @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@stop