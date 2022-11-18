@extends('user.merchant.popup.menu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-4 col-12">
        <div class="card">
            <div class="card-header mb-0">
                <h3 class="font-weight-bold">{{__('Enter your card pin')}}</h3>
            </div>
            <div class="card-body">
                <form action="{{route('payment.pin.submit', ['id'=>$link->ref_id])}}" method="post" id="payment-form">
                    @csrf
                    <div class="form-group">
                        <input class="form-control" placeholder="{{__('Pin')}}" pattern="[0-9]*" maxlength="4" type="text" name="pin" autocomplete="off" required>
                        @if ($errors->has('pin'))
                        <span>{!!$errors->first('pin')!!}</span>
                        @endif
                    </div>
                    <div class="text-center">
                        <button type="submit" id="ggglogin" class="btn btn-neutral btn-block my-4">{{__('Proceed')}}</button>
                    </div>
                </form>
                <div class="row justify-content-center mt-3">
                    <p class="text-xs text-dark font-weight-bold"><i class="fal fa-lock"></i> {{__('Secured by')}} <span class="fw-bold">{{$set->site_name}}</span></p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <a href="javascript:void" onclick="removeIframe()" class="text-white font-weight-bold"><i class="fal fa-times"> Close window</i></a>
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