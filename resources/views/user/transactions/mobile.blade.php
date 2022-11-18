@extends('errors.menu')

@section('content')
<div class="main-content">
    <div class="header py-7 py-lg-8 pt-lg-1">
        <div class="container">
            <div class="header-body text-center mb-3">
                <div class="row justify-content-center">
                    <div class="col-xl-5 col-lg-6 col-md-8 px-5">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt--8 pb-5 mb-0">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card card-profile bg-white border-0 mb-5">
                    <div class="card-body pt-7 px-5">
                        <div class="text-center text-dark mb-5">
                            <div class="btn-wrapper text-center mb-3">
                                <a href="javascript:void;" class="mb-3">
                                    <span class=""><i class="fal fa-mobile fa-4x text-info"></i></span>
                                </a>
                            </div>
                            <p>{{__('Authorize this transaction via your app, a push notification has been sent to your phone')}}</p>
                            <a href="{{route('verify.mobile', ['id' => $ref_id])}}" class="btn btn-info btn-block mt-3">{{__('I have entered pin')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center mt-3">
        <p>{{__('Contact')}} <a href="mailto:{{$set->email}}" class="text-info">{{$set->email}}</a> {{__('for any information')}}</p>
    </div>
    @stop