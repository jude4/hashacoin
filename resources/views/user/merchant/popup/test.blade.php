@extends('user.merchant.popup.menu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-12">
        <div class="card rounded">
            <div class="card-header mb-0">
                <div class="row align-items-center">
                    @if($link->logo!=null)
                    @if(UR_exists($link->logo))
                    <div class="col-auto">
                        <div class="avatar avatar-lg">
                            <img alt="Image placeholder" src="{{$link->logo}}">
                        </div>
                    </div>
                    @endif
                    @endif
                    <div class="col @if($link->logo!=null)@if(UR_exists($link->logo))text-right @endif @endif">
                        <h2 class="fw-bold mb-0"><span class="text-sm">{{$link->getCurrency->real->currency}}</span> {{number_format($link->amount,2)}}</h2>
                        <p class="text-sm">{{$link->email}}</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('payment.submit', ['id'=>$link->ref_id])}}" method="post" id="payment-form">
                    @csrf
                    <div class="form-group">
                        <select class="form-control" name="status" required>
                            <option value="">{{__('Select transaction response')}}</option>
                            <option value="1">{{__('Successful')}}</option>
                            <option value="2">{{__('Failed')}}</option>
                        </select>
                        @if ($errors->has('status'))
                        <span>{{$errors->first('status')}}</span>
                        @endif
                    </div>
                    <input type="hidden" value="test" name="type">
                    <input type="hidden" value="2" name="crf">
                    <div class="text-center">
                        <button type="submit" id="ggglogin" class="btn btn-neutral btn-block my-4">
                            {{__('Pay')}}
                            @if($link->user->business()->charges==1)
                            {{$link->getCurrency->real->currency}}<span id="tcr">{{number_format(($link->amount*$link->getCurrency->percent_charge/100)+($link->getCurrency->fiat_charge)+($link->amount),2)}}</span>
                            @else
                            {{$link->getCurrency->real->currency}}<span id="tcr">{{number_format($link->amount,2)}}</span>
                            @endif
                        </button>
                        <span class="badge badge-pill badge-primary"><i class="fal fa-ban"></i> {{__('Test mode')}}</span></br></br>
                        <p class="text-default text-xs"><i class="fal fa-lock"></i> {{__('Secured by')}} <span class="fw-bold">{{$set->site_name}}</span></p>
                    </div>
                </form>
                <div class="row justify-content-center mt-3">
                    <p class="text-xs text-dark font-weight-bold"><i class="fal fa-lock"></i> {{__('Secured by')}} <span class="fw-bold">{{$set->site_name}}</span></p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <a href="javascript:void" onclick="removeIframe()" class="text-white font-weight-bold"><i class="fal fa-times"> Cancel payment</i></a>
        </div>
    </div>
</div>
@stop
@section('script')
<script>
    "use strict";

    function removeIframe() {
        const parent = $('#iframe1', window.parent.document).remove();
    }
</script>
@endsection
@php
Cache::put('popup', 'popup')
@endphp