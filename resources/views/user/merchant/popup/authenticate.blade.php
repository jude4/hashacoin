@extends('user.merchant.popup.menu')

@section('content')
<div class="row justify-content-center mt-6">
    <div class="col-md-4 col-12">
        <div class="card card-profile bg-white border-0 mb-5">
            <div class="card-body">
                <div class="text-center text-dark mb-5">
                    @if($title == "Authenticate")
                    <div class="btn-wrapper text-center mb-5 mt-5">
                        <a href="javascript:void;" class="mb-3">
                            <span class=""><i class="fal fa-lock fa-6x text-muted"></i></span>
                        </a>
                    </div>
                    <p class="text-dark mb-3">{{__('Please authenticate payment the button below to authenticate with your bank, don\'t close this popup')}}</p>
                    <a target="_blank" href="{{$url}}" id="event" class="btn btn-neutral btn-block mt-3">{{__('Authenticate payment')}}</a>
                    <a href="javascript:void;" data-href="{{url()->previous()}}" id="previous" class="btn btn-success btn-block mt-3">{{__('Change payment method')}}</a>
                    @else
                    <div class="btn-wrapper text-center mb-5 mt-5">
                        <a href="javascript:void;" class="mb-3">
                            <span class=""><i class="fal fa-university fa-6x text-muted"></i></span>
                        </a>
                    </div>
                    <p>{{__('Please click the button below to redirect to your bank')}}</p>
                    <a target="_blank" href="{{Cache::get('popup_url')}}" id="event" class="btn btn-neutral btn-block mt-3">{{__('Click here')}}</a>
                    <a href="javascript:void;" data-href="{{Cache::get('popup_previous_url')}}" id="previous" class="btn btn-success btn-block mt-3">{{__('Change payment method')}}</a>
                    @endif
                </div>
                <div class="row justify-content-center mt-3">
                    <p class="text-xs text-dark font-weight-bold"><i class="fal fa-lock"></i> {{__('Secured by')}} <span class="fw-bold">{{$set->site_name}}</span></p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <a href="javascript:void" onclick="removeIframe()" class="text-white"><i class="fal fa-times"></i> Cancel payment</a>
        </div>
    </div>
</div>
@stop
@section('script')
<script>
    "use strict";
    $('#event').on("click", function() {
        if (!$(this).prop('disabled')) {
            $(this).attr("disabled", "disabled").html('<span class="spinner-border spinner-border-sm"></span> &nbsp; Waiting for response ...').prop('disabled', 'disabled');
        } else {
            $(this).attr("href", "javascript:void;")
        }
    });

    $(document).ready(function() {
        var channel = new BroadcastChannel('payment');
        channel.onmessage = function(e) {
            window.location.href = e.data;
        }
    })

    "use strict";
    $('#previous[data-href]').on("click", function() {
        $(this).html('<span class="spinner-border spinner-border-sm"></span> &nbsp; Please wait ...').prop('disabled', 'disabled');
        window.location.href = $(this).data('href');
    });

    "use strict";

    function removeIframe() {
        const parent = $('#iframe1', window.parent.document).remove();
    }
</script>
@endsection