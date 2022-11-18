@extends('user.merchant.popup.menu')

@section('content')
<div class="row justify-content-center mt-6">
    <div class="col-md-4 col-12">
        <div class="card">
            <div class="card-body">
                <div id="institution">
                    <h5 class="h4 font-weight-bolder text-dark mb-3">{{__('Select an Institution')}}</h5>
                    <ul class="list-group list-group-flush list mb-5" id="myDIV" style="display:block;height:200px;overflow-y:auto;">
                        @foreach($institution as $val)
                        @if(in_array("CREATE_DOMESTIC_SINGLE_PAYMENT",$val->features))
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <a id="event{{$val->id}}" target="_blank" href="{{route('authorize.payment', ['auth_token'=>$authtoken,'bank_id'=>$val->id,'trans_type'=>$type,'reference'=>$reference])}}">
                                        <h5>{{$val->name}}</h5>
                                    </a>
                                </div>                                
                                <div class="col">
                                    <a id="event{{$val->id}}" target="_blank" href="{{route('authorize.payment', ['auth_token'=>$authtoken,'bank_id'=>$val->id,'trans_type'=>$type,'reference'=>$reference])}}">
                                        <h5>{{$val->name}}</h5>
                                    </a>
                                </div>
                            </div>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                    <div class="form-group mb-0">
                        <input type="text" id="myInput" class="form-control" placeholder="{{__('Search institution ...')}}">
                    </div>
                </div>
                <div id="response" style="display:none;" class="text-center">
                    <div class="btn-wrapper text-center mb-5 mt-5">
                        <a href="javascript:void;" class="mb-3">
                            <span class=""><i class="fal fa-university fa-6x text-success"></i></span>
                        </a>
                    </div>
                    <p class="text-dark mb-3"><span class="spinner-border spinner-border-sm"></span> &nbsp; Waiting for response from institution</p>
                </div>
                <div class="row justify-content-center">
                    <a href="javascript:void;" data-href="{{url()->previous()}}" id="previous" class="btn btn-neutral btn-block mt-3">{{__('Change payment method')}}</a>
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
@foreach($institution as $val)
@if(in_array("CREATE_DOMESTIC_SINGLE_PAYMENT",$val->features))
<script>
    "use strict";
    $('#event{{$val->id}}').on("click", function() {
        if (!$(this).prop('disabled')) {
            $(this).attr("disabled", "disabled");
            $('#institution').hide();
            $('#response').show();
        } else {
            $(this).attr("href", "javascript:void;")
        }
    });
</script>
@endif
@endforeach
<script>
    function removeIframe() {
        const parent = $('#iframe1', window.parent.document).remove();
    }
    $('#previous[data-href]').on("click", function() {
        $(this).html('<span class="spinner-border spinner-border-sm"></span> Please wait ...').prop('disabled', 'disabled')
        window.location.href = $(this).data('href')
    });

    $(document).ready(function() {
        var channel = new BroadcastChannel('bank');
        channel.onmessage = function(e) {
            window.location.href = e.data;
        }
    })

    $(document).ready(function() {
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myDIV li").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endsection