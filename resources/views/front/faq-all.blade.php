@extends('front.menu')
@section('css')

@stop
@section('content')
<section class="parallax effect-section gray-bg">
    <div class="container position-relative">
        <div class="row screen-40 align-items-center justify-content-center p-150px-t">
            <div class="col-lg-10 text-center">
                <h6 class="white-color-black font-w-500">{{__('How can we help?')}}</h6>
                <h1 class="display-4 black-color m-20px-b">{{$title}}</h1>
                <form method="post" action="{{route('faq-submit')}}">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12 text-center">
                            <div class="input-group">
                                <input type="text" name="search" placeholder="Search the knowledge base..." 
                                  class="form-control py-3">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<section class="section gray-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 mb-5">
                <ul class="list-group list-group-numbered">
                    @foreach($faq as $val)
                    <li class="list-group-item py-4">
                        <a href="{{route('answer', ['id'=>$val->id, 'slug'=>$val->slug])}}">
                            <span class="">{{$val->question}}</span>
                        </a>
                    </li>
                    @endforeach
                  </ul>

            </div>
        </div>
    </div>
</section>



@stop