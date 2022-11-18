@extends('user.merchant.popup.menu')

@section('content')
<div class="row justify-content-center mt-6">
    <div class="col-md-3 col-12">
        <div class="card card-profile bg-white border-0 mb-5">
            <div class="card-body">
                <div class="text-center text-dark mb-5">
                    <div class="btn-wrapper text-center mb-3">
                        <a href="javascript:void;" class="mb-3">
                            <span class=""><i class="fal fa-lock fa-4x text-dark"></i></span>
                        </a>
                    </div>
                    <p>{{__('Authorize this transaction via your app, a push notification has been sent to your phone')}}</p>
                    <a href="{{route('verify.mobile', ['id' => $ref])}}" class="btn btn-neutral btn-block mt-3">{{__('I have entered pin')}}</a>
                </div>
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