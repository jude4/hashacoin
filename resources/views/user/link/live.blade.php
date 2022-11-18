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
<div class="container mt--8 pb-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-12">
      <div class="bhoechie-tab-container rounded">
        <div class="row">
          <div class="col-md-12 bhoechie-tab">
            @if($link->getCurrency->card==1 && $link->user->business()->card==1)
            <div class="bhoechie-tab-content rounded @if($type==null) active @else @if($type=='card') active @endif @endif">
              <div class="card-body">
                <h2 class="text-uppercase mb-2 font-weight-bolder text-dark">{{ucwords($link->name)}}</h2>
                <p class="mb-2 font-weight-bolder text-dark">{{$link->description}}</p>
                <form action="{{route('payment.submit', ['id'=>$link->ref_id])}}" method="post" id="payment-form">
                  @csrf
                  <input type="hidden" value="card" name="type">
                  <input type="hidden" value="1" name="crf">
                  <div class="form-group row">
                    @if($link->amount==null)
                    <div class="col-md-12 mb-2">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text text-future">{{$link->getCurrency->real->currency_symbol}}</span>
                        </div>
                        <input class="form-control" max="@if($link->getCurrency->max_amount==null) @else{{$link->getCurrency->max_amount}}@endif" min="{{$link->getCurrency->min_amount}}" type="number" placeholder="0.00" onkeyup="service_charge()" id="transaction_charge" name="amount" autocomplete="off" required>
                      </div>
                      @if ($errors->has('amount'))
                      <span class="text-xs text-uppercase mt-3">{{$errors->first('amount')}}</span>
                      @endif
                    </div>
                    @else
                    <input type="hidden" name="amount" value="{{$link->amount}}">
                    @endif
                  </div>
                  <div class="mb-3">
                    <div id="card-element"></div>
                    <div id="card-errors" role="alert"></div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-12 mb-2">
                      <p class="text-uppercase text-xs mb-2 font-weight-bolder text-dark">Payer information</p>
                      <input type="email" class="form-control" name="email" placeholder="{{__('Email address')}}" autocomplete="off" value="@if(session('email')){{session('email')}}@endif" required />
                      @if ($errors->has('email'))
                      <span>{!!$errors->first('email')!!}</span>
                      @endif
                    </div>
                    <div class="col-6 mb-2">
                      <input type="text" class="form-control" name="first_name" placeholder="{{__('First name')}}" autocomplete="off" value="@if(session('first_name')){{session('first_name')}}@endif" required />
                      @if ($errors->has('first_name'))
                      <span>{{$errors->first('first_name')}}</span>
                      @endif
                    </div>
                    <div class="col-6">
                      <input type="text" class="form-control" name="last_name" placeholder="{{__('Last name')}}" autocomplete="off" value="@if(session('last_name')){{session('last_name')}}@endif" required />
                      @if ($errors->has('last_name'))
                      <span>{{$errors->first('last_name')}}</span>
                      @endif
                    </div>
                  </div>
                  <div class="text-center">
                    <button type="submit" id="payment-submit" class="btn btn-neutral btn-block my-4">{{__('Pay')}}
                      @if($link->user->business()->charges==1)
                      {{$link->getCurrency->real->currency}}<span id="tcr">{{number_format(($link->amount*$link->getCurrency->percent_charge/100)+($link->getCurrency->fiat_charge)+($link->amount),2)}}</span>
                      @else
                      {{$link->getCurrency->real->currency}}<span id="tcr">@if($link->amount==null)0.00 @else{{$link->amount}} @endif</span>
                      @endif
                    </button>
                  </div>
                </form>
              </div>
            </div>
            @endif
            @if($link->getCurrency->bank_account==1 && $link->user->business()->bank_account==1)
            <div class="bhoechie-tab-content rounded @if($type=='bank_account') active @endif">
              <div class="card-body">
                <h2 class="text-uppercase mb-2 font-weight-bolder text-dark">{{ucwords($link->name)}}</h2>
                <p class="mb-2 font-weight-bolder text-dark">{{$link->description}}</p>
                <form action="{{route('payment.submit', ['id'=>$link->ref_id])}}" method="post" id="payment-form1">
                  @csrf
                  <input type="hidden" value="bank" name="type">
                  <input type="hidden" value="1" name="crf">
                  <div class="form-group row">
                    @if($link->amount==null)
                    <div class="col-md-12 mb-2">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text text-future">{{$link->getCurrency->real->currency_symbol}}</span>
                        </div>
                        <input class="form-control" max="@if($link->getCurrency->max_amount==null) @else{{$link->getCurrency->max_amount}}@endif" min="{{$link->getCurrency->min_amount}}" type="number" placeholder="0.00" onkeyup="service_charge_bank()" id="transaction_charge_bank" name="amount" autocomplete="off" required>
                      </div>
                      @if ($errors->has('amount'))
                      <span class="text-xs text-uppercase mt-3">{{$errors->first('amount')}}</span>
                      @endif
                    </div>
                    @else
                    <input type="hidden" name="amount" value="{{$link->amount}}">
                    @endif
                    <div class="col-md-12 mb-2">
                      <p class="text-uppercase text-xs mb-2 font-weight-bolder text-dark">Payer information</p>
                      <input type="email" class="form-control" name="email" placeholder="{{__('Email address')}}" autocomplete="off" value="@if(session('email')){{session('email')}}@endif" required />
                      @if ($errors->has('email'))
                      <span>{!!$errors->first('email')!!}</span>
                      @endif
                    </div>
                    <div class="col-6 mb-2">
                      <input type="text" class="form-control" name="first_name" placeholder="{{__('First name')}}" autocomplete="off" value="@if(session('first_name')){{session('first_name')}}@endif" required />
                      @if ($errors->has('first_name'))
                      <span>{{$errors->first('first_name')}}</span>
                      @endif
                    </div>
                    <div class="col-6">
                      <input type="text" class="form-control" name="last_name" placeholder="{{__('Last name')}}" autocomplete="off" value="@if(session('last_name')){{session('last_name')}}@endif" required />
                      @if ($errors->has('last_name'))
                      <span>{{$errors->first('last_name')}}</span>
                      @endif
                    </div>
                  </div>
                  <div class="text-center">
                    <button type="submit" id="ggglogin1" class="btn btn-neutral btn-block my-4">{{__('Pay')}}
                      @if($link->user->business()->charges==1)
                      {{$link->getCurrency->real->currency}}<span id="tcr_bank">{{number_format(($link->amount*$link->getCurrency->percent_charge/100)+($link->getCurrency->fiat_charge)+($link->amount),2)}}</span>
                      @else
                      {{$link->getCurrency->real->currency}}<span id="tcr_bank">@if($link->amount==null)0.00 @else{{$link->amount}} @endif</span>
                      @endif
                    </button>
                  </div>
                </form>
              </div>
            </div>
            @endif
            @if($link->getCurrency->mobile_money==1 && $link->user->business()->mobile_money==1)
            <div class="bhoechie-tab-content rounded @if($type=='mobile_money') active @endif">
              <div class="">
                <div class="card-body">
                  <h2 class="text-uppercase mb-2 font-weight-bolder text-dark">{{ucwords($link->name)}}</h2>
                  <p class="mb-2 font-weight-bolder text-dark">{{$link->description}}</p>
                  <form action="{{route('payment.submit', ['id'=>$link->ref_id])}}" method="post" id="payment-form2">
                    @csrf
                    <input type="hidden" value="mobile_money" name="type">
                    <input type="hidden" value="1" name="crf">
                    <div class="form-group row">
                      @if($link->amount==null)
                      <div class="col-md-12 mb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text text-future">{{$link->getCurrency->real->currency_symbol}}</span>
                          </div>
                          <input class="form-control" max="@if($link->getCurrency->max_amount==null) @else{{$link->getCurrency->max_amount}}@endif" min="{{$link->getCurrency->min_amount}}" type="number" placeholder="0.00" onkeyup="service_charge_mobile()" id="transaction_charge_mobile" name="amount" autocomplete="off" required>
                        </div>
                        @if ($errors->has('amount'))
                        <span class="text-xs text-uppercase mt-3">{{$errors->first('amount')}}</span>
                        @endif
                      </div>
                      @else
                      <input type="hidden" name="amount" value="{{$link->amount}}">
                      @endif
                      <div class="col-md-12 mb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text text-future">+{{str_replace('+', '', $link->getCurrency->real->phonecode)}}</span>
                          </div>
                          <input class="form-control" type="text" maxlength="12" name="mobile" autocomplete="off" value="@if(session('mobile')){{session('mobile')}}@endif" required>
                        </div>
                        @if ($errors->has('mobile'))
                        <span class="text-xs text-uppercase mt-3">{{$errors->first('mobile')}}</span>
                        @endif
                      </div>
                      <div class="col-md-12 mb-2">
                        <p class="text-uppercase text-xs mb-2 font-weight-bolder text-dark">Payer information</p>
                        <input type="email" class="form-control" name="email" placeholder="{{__('Email address')}}" autocomplete="off" value="@if(session('email')){{session('email')}}@endif" required />
                        @if ($errors->has('email'))
                        <span>{!!$errors->first('email')!!}</span>
                        @endif
                      </div>
                      <div class="col-6 mb-2">
                        <input type="text" class="form-control" name="first_name" placeholder="{{__('First name')}}" autocomplete="off" value="@if(session('first_name')){{session('first_name')}}@endif" required />
                        @if ($errors->has('first_name'))
                        <span>{{$errors->first('first_name')}}</span>
                        @endif
                      </div>
                      <div class="col-6">
                        <input type="text" class="form-control" name="last_name" placeholder="{{__('Last name')}}" autocomplete="off" value="@if(session('last_name')){{session('last_name')}}@endif" required />
                        @if ($errors->has('last_name'))
                        <span>{{$errors->first('last_name')}}</span>
                        @endif
                      </div>
                    </div>
                    <div class="text-center">
                      <button type="submit" id="ggglogin2" class="btn btn-neutral btn-block my-4">{{__('Pay')}}
                        @if($link->user->business()->charges==1)
                        {{$link->getCurrency->real->currency}}<span id="tcr_mobile">{{number_format(($link->amount*$link->getCurrency->percent_charge/100)+($link->getCurrency->fiat_charge)+($link->amount),2)}}</span>
                        @else
                        {{$link->getCurrency->real->currency}}<span id="tcr_mobile">@if($link->amount==null)0.00 @else{{$link->amount}} @endif</span>
                        @endif
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            @endif
          </div>
          <div class="col-md-12 bhoechie-tab-menu mb-3">
            <div class="list-group border-0">
              @if($link->getCurrency->card==1 && $link->user->business()->card==1)
              <a href="#" class="list-group-item text-center border-0 @if($type==null) active @else @if($type=='card') active @endif @endif">
                <h4 class="fad fa-credit-card mb-2"></h4> Pay with Card
              </a>
              @endif
              @if($link->getCurrency->bank_account==1 && $link->user->business()->bank_account==1)
              <a href="#" class="list-group-item text-center border-0 @if($type=='bank_account') active @endif">
                <h4 class="fad fa-university mb-2"></h4> Pay with Bank
              </a>
              @endif
              @if($link->getCurrency->mobile_money==1 && $link->user->business()->mobile_money==1)
              <a href="#" class="list-group-item text-center border-0 @if($type=='mobile_money') active @endif">
                <h4 class="fad fa-mobile mb-2"></h4> Pay with Mobile Money
              </a>
              @endif
            </div>
          </div>
          <div class="col-md-12 text-center">
            <p class="text-xs font-weight-bold mb-3 text-primary"><i class="fal fa-lock"></i> {{__('Secured by')}} <span class="fw-bold">{{$set->site_name}}</span></p>
          </div>
        </div>
      </div>
      <div class="row justify-content-center mt-5">
        <a href="{{route('login')}}" class="text-dark"><i class="fal fa-times"></i> {{__('Cancel payment')}}</a>
      </div>
    </div>
  </div>
</div>
@stop
@section('script')
@if($link->user->business()->charges==1)
<script>
  "use strict";
  function service_charge() {
    var amount = $("#transaction_charge").val();
    var percent_charge = '{{$link->getCurrency->percent_charge}}';
    var fiat_charge = '{{$link->getCurrency->fiat_charge}}';
    var charge = (parseFloat(amount) * parseFloat(percent_charge) / 100) + parseFloat(fiat_charge) + parseFloat(amount);
    if (isNaN(charge) || charge < 0) {
      charge = 0;
    }
    $("#tcr").text(charge.toFixed(2));
  }
  "use strict";
  function service_charge_bank() {
    var amount = $("#transaction_charge_bank").val();
    var percent_charge = '{{$link->getCurrency->percent_charge}}';
    var fiat_charge = '{{$link->getCurrency->fiat_charge}}';
    var charge = (parseFloat(amount) * parseFloat(percent_charge) / 100) + parseFloat(fiat_charge) + parseFloat(amount);
    if (isNaN(charge) || charge < 0) {
      charge = 0;
    }
    $("#tcr_bank").text(charge.toFixed(2));
  }
  "use strict";
  function service_charge_mobile() {
    var amount = $("#transaction_charge_mobile").val();
    var percent_charge = '{{$link->getCurrency->percent_charge}}';
    var fiat_charge = '{{$link->getCurrency->fiat_charge}}';
    var charge = (parseFloat(amount) * parseFloat(percent_charge) / 100) + parseFloat(fiat_charge) + parseFloat(amount);
    if (isNaN(charge) || charge < 0) {
      charge = 0;
    }
    $("#tcr_mobile").text(charge.toFixed(2));
  }
  $("#transaction_charge").change(service_charge);
  $("#transaction_charge_bank").change(service_charge_bank);
  $("#transaction_charge_mobile").change(service_charge_mobile);
</script>
@else
<script>
  "use strict";
  function service_charge() {
    var amount = $("#transaction_charge").val();
    if (isNaN(amount) || amount < 0) {
      amount = 0;
    }
    $("#tcr").text(parseFloat(amount).toFixed(2));
  }
  "use strict";

  function service_charge_bank() {
    var amount = $("#transaction_charge_bank").val();
    if (isNaN(amount) || amount < 0) {
      amount = 0;
    }
    $("#tcr_bank").text(parseFloat(amount).toFixed(2));
  }
  "use strict";

  function service_charge_mobile() {
    var amount = $("#transaction_charge_mobile").val();
    if (isNaN(amount) || amount < 0) {
      amount = 0;
    }
    $("#tcr_mobile").text(parseFloat(amount).toFixed(2));
  }
  $("#transaction_charge").change(service_charge);
  $("#transaction_charge_bank").change(service_charge_bank);
  $("#transaction_charge_mobile").change(service_charge_mobile);
</script>
@endif
<script>
  $(document).ready(function() {
    $("div.bhoechie-tab-menu>div.list-group>a").click(function(e) {
      e.preventDefault();
      $(this).siblings('a.active').removeClass("active");
      $(this).addClass("active");
      var index = $(this).index();
      $("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
      $("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
    });
  });
</script>
@endsection
@php
if(session('trace_id')==null){
Session::put('trace_id', Str::random(32));
}else{
if($link->ref_id!=session('payment_link')){
Session::put('trace_id', Str::random(32));
}
}
Session::forget('card_number');
Session::forget('expiry');
Session::forget('expiry_month');
Session::forget('expiry_year');
Session::forget('cvv');
Session::forget('first_name');
Session::forget('last_name');
Session::forget('tx_ref');
Session::forget('email');
@endphp