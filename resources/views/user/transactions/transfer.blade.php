@extends('userlayout')

@section('content')
<!-- Page content -->
<div class="container-fluid mt--7">
    <div class="content-wrapper mt-3">
    <a href="{{url()->previous()}}" class="btn btn-neutral mb-3"><i class="fal fa-caret-left"></i> {{__('Go back')}}</a>
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0 font-weight-bolder">{{__('Request Payout')}}</h3>
            </div>
            <form action="{{route('withdraw.submit', ['id'=>$user->getBalance($val->id)->ref_id])}}" method="post">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text text-future">{{$val->real->currency_symbol}}</span>
                            </div>
                            <input type="number" class="form-control" min="1" max="{{$user->getBalance($val->id)->amount}}" autocomplete="off" id="amount" name="amount" placeholder="{{__('How much?')}}" required>
                        </div>
                    </div>
                    <input type="hidden" id="withdraw_percent_charge" value="{{$val->withdraw_percent_charge}}">
                    <input type="hidden" id="withdraw_fiat_charge" value="{{$val->withdraw_fiat_charge}}">
                    <div class="form-group">
                        <select class="form-control" id="payout_type" name="payout_type" required>
                            <option value="2*{{$val->bank_format}}">Beneficiary</option>
                            <option value="1*{{$val->bank_format}}">New Beneficiary</option>
                        </select>
                    </div>
                    <div class="form-group" id="old_beneficiary" style="display:none;">
                        <select class="form-control" id="beneficiary" name="beneficiary">
                            <option value="">{{__('Select beneficiary')}}</option>
                            @foreach($user->getBeneficiary($val->id) as $ben)
                            <option value="{{$ben->id}}">{{$ben->name}} -
                                @if($val->bank_format=="us")
                                {{$ben->routing_no}}
                                @elseif($val->bank_format=="eur")
                                {{$ben->iban}}
                                @elseif($val->bank_format=="uk")
                                {{$ben->acct_no}} - {{$ben->sort_code}}
                                @elseif($val->bank_format=="normal")
                                {{getBankFirst($ben->bank_name)->name}} - {{$ben->acct_no}}
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="bank" style="display:none;">
                        <div class="form-group">
                            <input type="text" name="name" id="name" maxlength="255" class="form-control" placeholder="{{__('Name of account holder')}}">
                        </div>
                        @if($val->bank_format=="us")
                        <div class="form-group">
                            <input type="text" name="routing_no" id="routing_no" pattern="\d*" maxlength="9" minlength="9" class="form-control" placeholder="{{__('Routing number')}}">
                        </div>
                        @elseif($val->bank_format=="eur")
                        <div class="form-group">
                            <input type="text" name="iban" id="iban" pattern="\d*" maxlength="16" minlength="16" class="form-control" placeholder="{{__('Iban')}}">
                        </div>
                        @elseif($val->bank_format=="uk")
                        <div class="form-group">
                            <input type="text" name="acct_no" id="acct_no" pattern="\d*" maxlength="8" minlength="8" class="form-control" placeholder="{{__('Account number')}}">
                        </div>
                        <div class="form-group">
                            <input type="text" name="sort_code" id="sort_code" pattern="\d*" maxlength="6" minlength="6" class="form-control" placeholder="{{__('Sort code')}}">
                        </div>
                        @elseif($val->bank_format=="normal")
                        <div class="form-group">
                            <select class="form-control" name="bank_name" id="bank_name">
                                <option value="">Select bank</option>
                                @foreach(getBank($val->id) as $bank)
                                <option value="{{$bank->id}}">{{$bank->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="acct_no" id="acct_no" pattern="\d*" maxlength="10" minlength="10" class="form-control" placeholder="{{__('Account number')}}">
                        </div>
                        <div class="form-group">
                            <input type="text" name="acct_name" id="acct_name" class="form-control" placeholder="{{__('Account name')}}">
                        </div>
                        @endif
                    </div>
                    <div class="row mt-3 mb-3" id="new_beneficiary" style="display:none;">
                        <div class="col-6">
                            <div class="custom-control custom-control-alternative custom-checkbox">
                                <input class="custom-control-input" id="custombeneficiary" type="checkbox" name="new_beneficiary">
                                <label class="custom-control-label" for="custombeneficiary">
                                    <span class="text-dark">{{__('Save as Beneficiary')}}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <p>{{__('You will receive')}}: <span id="receive">0.00</span>{{$val->real->currency}}</p>
                                    <p>{{__('Transaction charge')}}: <span id="charge">0.00</span>{{$val->real->currency}}</p>
                                    <p>{{__('Next settlement')}}: {{date("M j, Y", strtotime(nextPayoutDate($val->duration)))}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="payment" class="btn btn-neutral btn-block">{{__('Submit Request')}}</button>
                </div>
            </form>
        </div>
        @stop
        @section('script')
        <script>
            function ben() {
                var payout_type = $("#payout_type").find(":selected").val();
                var myarr = payout_type.split("*");
                if (myarr[0].split("<") == 1) {
                    $("#bank").show();
                    $("#new_beneficiary").show();
                    $("#old_beneficiary").hide();
                    $("#name").attr('required', '');
                    if (myarr[1].split("<") == "us") {
                        $("#routing_no").attr('required', '');
                    } else if (myarr[1].split("<") == "eur") {
                        $("#iban").attr('required', '');
                    } else if (myarr[1].split("<") == "uk") {
                        $("#acct_no").attr('required', '');
                        $("#sort_code").attr('required', '');
                    } else if (myarr[1].split("<") == "normal") {
                        $("#bank_name").attr('required', '');
                        $("#acct_no").attr('required', '');
                        $("#acct_name").attr('required', '');
                    }
                    $("#beneficiary").removeAttr('required', '');
                } else if (myarr[0].split("<") == 2) {
                    $("#bank").hide();
                    $("#old_beneficiary").show();
                    $("#new_beneficiary").hide();
                    $("#name").removeAttr('required', '');
                    if (myarr[1].split("<") == "us") {
                        $("#routing_no").removeAttr('required', '');
                    } else if (myarr[1].split("<") == "eur") {
                        $("#iban").removeAttr('required', '');
                    } else if (myarr[1].split("<") == "uk") {
                        $("#acct_no").removeAttr('required', '');
                        $("#sort_code").removeAttr('required', '');
                    } else if (myarr[1].split("<") == "normal") {
                        $("#bank_name").removeAttr('required', '');
                        $("#acct_no").removeAttr('required', '');
                        $("#acct_name").removeAttr('required', '');
                    }
                    $("#beneficiary").attr('required', '');
                }
            }
            $("#payout_type").change(ben);
            ben();
        </script>
        <script>
            function withdraw() {
                var amount = $("#amount").val();
                var percent = $("#withdraw_percent_charge").val();
                var fiat = $("#withdraw_fiat_charge").val();
                var receive = parseFloat(amount) - parseFloat(fiat) - (parseFloat(amount) * isNaN(parseFloat(percent)) / 100);
                var charge = parseFloat(fiat) + (parseFloat(amount) * isNaN(parseFloat(percent)) / 100);
                if (isNaN(receive) || receive < 0) {
                    receive = 0;
                }
                $("#receive").text(receive.toFixed(2));
                $("#charge").text(charge.toFixed(2));
                if (receive < charge) {
                    $("#payment").attr('disabled', 'disabled');
                } else {
                    $("#payment").removeAttr('disabled', '');
                }
            }
            $("#amount").keyup(withdraw);
        </script>
        @endsection