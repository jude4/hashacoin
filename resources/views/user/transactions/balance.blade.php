@extends('userlayout')

@section('content')
<div class="container-fluid mt--7">
    <div class="content-wrapper mt-3">
        <div class="row">
                                                  
            @foreach($user->getAllBalance() as $val)
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6 col-12 mb-2">
                                <div class="media align-items-center">
                                    <div class="media-body">
                                        @if($user->business()->live==1)
                                        <h2 class="mb-1 text-dark font-weight-bolder">{{__('Available Balance')}}: {{number_format($val->amount,2).' '.$val->getCurrency->real->currency.$val->getCurrency->real->emoji}}</h2>
                                        <p class="text-dark">{{__('Pending Balance')}}: {{number_format($user->getPendingTransactions($val->country_id),2).' '.$val->getCurrency->real->currency}}</p>
                                        @else
                                        <h2 class="mb-0 text-dark font-weight-bolder">{{__('Balance')}}: {{number_format($val->test,2).' '.$val->getCurrency->real->currency}}</h2>
                                        @endif
                                        @if(count($user->getUniqueTransactions($val->country_id))>0)
                                        <p class="text-dark">{{__('Last transaction')}}: {{date("Y/m/d h:i:A", strtotime($user->getLastTransaction($val->country_id)->created_at))}}</p>
                                        @else
                                        <p class="text-dark">{{__('Last transaction')}}: {{__('No record')}}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($user->business()->live==1)
                            <div class="col-md-6 col-12 text-md-end">
                                <a href="{{route('wallet.transactions', ['country'=>$val->country_id])}}" class="btn btn-sm btn-neutral text-dark"><i class="fal fa-sync"></i> {{__('Transactions')}}</a>
                                @if($val->getCurrency->funding==1)
                                <a href="{{route('fund.account', ['id'=>$user->getBalance($val->country_id)->ref_id])}}" class="btn btn-sm btn-neutral text-dark"><i class="fal fa-plus-circle"></i> {{__('Top up')}}</a>
                                @endif
                                @if($user->getBalance($val->country_id)->amount>0)
                                <a href="{{route('wallet.payout', ['country'=>$val->country_id])}}" class="btn btn-sm btn-neutral text-dark"><i class="fal fa-share"></i> {{__('Make payouts')}}</a>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@stop
@section('script')
<script>
    "use strict"
    $('#ggglogin').on('click', function() {
        $(this).text('Please wait ...').attr('disabled', 'disabled');
        $('#payment-form').submit();
    });
</script>
<script>
    'use strict';
    var clipboard = new ClipboardJS('.castro-copy');

    clipboard.on('success', function(e) {
        navigator.clipboard.writeText(e.text);
        $(e.trigger)
            .attr('title', 'Copied!')
            .text('Copied!')
            .tooltip('_fixTitle')
            .tooltip('show')
            .attr('title', 'Copy to clipboard')
            .tooltip('_fixTitle')

        e.clearSelection()
    });

    clipboard.on('error', function(e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
    });
</script>
@endsection