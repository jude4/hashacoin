@extends('user.merchant.popup.menu')

@section('content')
<div class="row justify-content-center mt-6">
  <div class="col-md-4 col-12">
    <div class="card card-profile bg-white border-0 mb-5">
      <div class="card-body">
        <div class="text-center text-dark mb-5">
          <div class="btn-wrapper text-center mb-5 mt-5">
            <a href="javascript:void;" class="mb-3">
              <span class=""><i class="fal fa-check-circle fa-6x text-success"></i></span>
            </a>
          </div>
          <h2 class="font-weight-bolder">{{__('Payment successful')}}</h2>
          <p class="text-dark mb-3">Payment of {{$link->getCurrency->real->currency_symbol.number_format($link->amount, 2)}} to {{$link->business()->name}} is complete</p>
          @if($url!=null)
          <p>Redirecting, please don't close this window</p>
          @endif
        </div>
        <div class="row justify-content-center mt-3">
          <p class="text-xs text-dark font-weight-bold"><i class="fal fa-lock"></i> {{__('Secured by')}} <span class="fw-bold">{{$set->site_name}}</span></p>
        </div>
      </div>
    </div>
    <div class="row justify-content-center">
      <a href="javascript:void" onclick="removeIframe()" class="text-white"><i class="fal fa-times"></i> Close window</a>
    </div>
  </div>
</div>
@stop
@section('script')
@if($url!=null)
<script>
  var timer = setTimeout(function() {
    //const parent = $('#iframe1', window.parent.document).remove();
    window.top.location.href = '{{$url}}';
  }, 10000);
</script>
@endif
<script>
  "use strict";

  function removeIframe() {
    const parent = $('#iframe1', window.parent.document).remove();
  }
</script>
@endsection