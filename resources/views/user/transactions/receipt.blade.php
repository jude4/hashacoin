@extends('user.link.menu')

@section('content')
<div class="main-content">
  <div class="header py-5 pt-7">
    <div class="container">
      <div class="header-body text-center mb-7">
        <div class="row justify-content-center">
        </div>
      </div>
    </div>
  </div>
</div>
<div class="container mt--8 pb-5 mb-0">
  <div class="row justify-content-center">
    <div class="col-md-5 col-12">
      <div class="card card-profile bg-white border-0 mb-5">
        <div class="card-body">
          <div class="text-center text-dark mb-5">
            <div class="btn-wrapper text-center mb-5 mt-5">
              <a href="javascript:void;" class="mb-3">
                <span class=""><i class="fal fa-check-circle fa-6x text-success"></i></span>
              </a>
            </div>
            <h2 class="font-weight-bolder">{{__('Payment successful')}}</h2>
            <p class="text-dark">Payment of {{$link->getCurrency->real->currency_symbol.number_format($link->amount, 2)}} to {{$link->business()->name}} is complete</p>
            @if($link->type == 2)
            @if($url!=null)
            <h2 class="font-weight-bolder">Redirecting, please don't close this page</h2>
            @endif
            @endif
          </div>
        </div>
      </div>
      <div class="row justify-content-center mt-3">
        <p class="text-dark font-weight-bold"><i class="fal fa-lock"></i> {{__('Secured by')}} <span class="fw-bold">{{$set->site_name}}</span></p>
      </div>
    </div>
  </div>
</div>
@stop
@section('script')
@if($link->type == 2)
@if($url!=null)
<script>
  var timer = setTimeout(function() {
    window.location.href = '{{$url}}';
  }, 10000);
</script>
@endif
@endif
@endsection