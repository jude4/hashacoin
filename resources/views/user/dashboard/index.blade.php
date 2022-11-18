@extends('userlayout')

@section('content')
<div class="container-fluid mt--7">
  <div class="content-wrapper mt-3">
    @if($user->business()->kyc_status==null || $user->business()->kyc_status=="RESUBMIT")
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-9">
            <h2 class="mb-0 font-weight-bolder text-dark">{{__('We need more information about you')}}</h2>
            <p>{{__('Compliance is currently due, please update your account information to have access to receiving payment.')}}</p>
          </div>
          <div class="col-md-3 text-right">
            <a href="{{route('user.compliance')}}" class="btn btn-neutral">{{__('Click here')}}</a>
          </div>
        </div>
      </div>
    </div>
    @endif
    @if($user->business()->kyc_status=="PROCESSING")
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-12">
            <h2 class="mb-0 font-weight-bolder text-dark">{{__('We are processing your request')}}</h2>
            <p>{{__('Compliance is currently being reviewed.')}}</p>
          </div>
        </div>
      </div>
    </div>
    @endif
    <div class="card mb-2">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-6 col-12 mb-2">
            <div class="media align-items-center">
              <div class="media-body">
                @if($user->business()->live==1)
                <h2 class="mb-1 text-dark font-weight-bolder">{{__('Available Balance')}}: {{number_format($user->getBalance($val->id)->amount,2).' '.$val->real->currency}}</h2>
                <p class="text-dark">{{__('Pending Balance')}}: {{number_format($user->getPendingTransactions($val->id),2).' '.$val->real->currency}}</p>
                @else
                <h2 class="mb-0 text-dark font-weight-bolder">{{__('Balance')}}: {{number_format($user->getBalance($val->id)->test,2).' '.$val->real->currency}}</h2>
                @endif
                @if(count($user->getUniqueTransactions($val->id))>0)
                <p class="text-dark mb-3">{{__('Last transaction')}}: {{date("Y/m/d h:i:A", strtotime($user->getLastTransaction($val->id)->created_at))}}</p>
                @else
                <p class="text-dark mb-3">{{__('Last transaction')}}: {{__('No record')}}</p>
                @endif
                {{--<p class="text-dark">{{__('Wallet Id')}}: {{$user->getBalance($val->id)->id}}</p>--}}
                <a class="text-dark mt-3 cursor-pointer" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Change currency -
                  @if(route('user.dashboard')==url()->current())
                  {{$user->getFirstBalance()->getCurrency->real->emoji.' '.$user->getFirstBalance()->getCurrency->real->currency}}
                  @php $val = $user->getFirstBalance()->getCurrency; @endphp
                  @else
                  {{getBalance($currency)->getCurrency->real->emoji.' '.getBalance($currency)->getCurrency->real->currency}}
                  @php $val = getBalance($currency)->getCurrency; @endphp
                  @endif
                </a>
                <div class="dropdown-menu">
                  @foreach(getAcceptedCountry() as $country)
                  <a href="{{route('user.dashboard', ['currency'=>$user->getBalance($country->id)->ref_id])}}" class="dropdown-item">{{$country->real->emoji.' '.$country->real->currency}}</a>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
          @if($user->business()->live==1)
          <div class="col-md-6 col-12 text-md-end">
            <a href="{{route('wallet.transactions', ['country'=>$val->id])}}" class="btn btn-sm btn-neutral text-dark mb-3"><i class="fal fa-sync"></i> {{__('Transactions')}}</a>
            @if($val->funding==1)
            <a href="{{route('fund.account', ['id'=>$user->getBalance($val->id)->ref_id])}}" class="btn btn-sm btn-neutral text-dark mb-3"><i class="fal fa-plus-circle"></i> {{__('Top up balance')}}</a>
            @endif
            @if($user->getBalance($val->id)->amount>0)
            <a href="{{route('wallet.payout', ['country'=>$val->id])}}" class="btn btn-sm btn-neutral text-dark mb-3"><i class="fal fa-share"></i> {{__('Make payouts')}}</a>
            @endif
          </div>
          @endif
          <div class="modal fade" id="payout" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="mb-0 font-weight-bolder">{{__('Request Payout')}}</h3>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
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
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mb-3">
      <div class="col-md-12">
        <div class="nav-wrapper2">
          <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch nav-trans b-b" id="tabs-icons-text" role="tablist">
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if($duration == null || $duration == 'today') active @endif" href="{{route('user.dashboard', ['currency' => $user->getBalance($val->id)->ref_id,'duration' => 'today'])}}" role="tab" aria-controls="tabs-icons-text-" aria-selected="true">Today</a>
            </li>
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if($duration == 'week') active @endif" href="{{route('user.dashboard', ['currency' => $user->getBalance($val->id)->ref_id,'duration' => 'week'])}}" role="tab" aria-controls="tabs-icons-text-" aria-selected="true">This Week</a>
            </li>
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if($duration == 'month') active @endif" href="{{route('user.dashboard', ['currency' => $user->getBalance($val->id)->ref_id,'duration' => 'month'])}}" role="tab" aria-controls="tabs-icons-text-" aria-selected="true">This Month</a>
            </li>
            <li class="nav-item">
              <a class="nav-link mb-sm-3 mb-md-0 @if($duration == 'year') active @endif" href="{{route('user.dashboard', ['currency' => $user->getBalance($val->id)->ref_id,'duration' => 'year'])}}" role="tab" aria-controls="tabs-icons-text-" aria-selected="true">This Year</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="card card-stats rounded">
          <div class="card-body">
            <div class="row mt-2">
              <div class="col">
                <h2 class="font-weight-bolder mb-1">Transaction Value</h2>
                <p class="text-dark mb-1">Total amount made during selected period</p>
                <p class="text-dark h3">{{$val->real->currency.number_format($user->tranStat($duration, $val->id)->sum('amount'), 2)}}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-stats rounded">
          <div class="card-body">
            <div class="row mt-2">
              <div class="col">
                <h2 class="font-weight-bolder mb-1">Transaction Volume</h2>
                <p class="text-dark mb-1">No. of transactions during selected period</p>
                <p class="text-dark h3">{{$user->tranStat($duration, $val->id)->count()}}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-stats rounded">
          <div class="card-body">
            <div class="row mt-2">
              <div class="col">
                <h2 class="font-weight-bolder mb-1">Next Settlement</h2>
                <p class="text-dark mb-1">Due on the next business day</p>
                <p class="text-dark h3">{{$val->real->currency}}@if($user->nextPay($val->id) != null){{number_format($user->nextPay($val->id)->amount, 2)}} @else 0.00 @endif</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="card card-stats rounded">
          <div class="card-body">
            <div class="row mt-2">
              <div class="col">
                <h2 class="font-weight-bolder mb-1">Transaction Success Rate</h2>
                <p class="text-dark mb-1">Percentage of valid transactions during selected period</p>
                <p class="text-dark h3">{{round($user->successStat($duration, $val->id))}} %</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card no-shadow">
          <div class="card-body">
            <h2 class="mb-0 font-weight-bolder text-dark">{{__('Revenue History')}}</h2>
            @if(count($user->getTransactionsExceptPayout($duration, $val->id))>0)
            <div id="myChart"></div>
            @else
            <div class="text-center mt-5 mb-3">
              <div class="btn-wrapper text-center mb-3">
                <a href="javascript:void;" class="mb-3">
                  <span class=""><i class="fal fa-waveform-path fa-4x text-muted"></i></span>
                </a>
              </div>
              <h3 class="text-dark">{{__('No Earning History')}}</h3>
              <p class="text-dark">{{__('We couldn\'t find any earning log to this account')}}</p>
            </div>
            @endif
          </div>
        </div>
      </div>
      @if($user->business()->live==1)
      <div class="col-md-6">
        <div class="card card-stats rounded">
          <div class="card-body">
            <div class="row mt-2">
              <div class="col">
                <h2 class="font-weight-bolder mb-1">Transaction by channel</h2>
                <p class="text-dark mb-2">Payment channels your customers used during the selected period</p>
                <div class="row justify-content-between align-items-center mb-3">
                    <div class="col-12">
                      <p>Card</p>
                    </div>                    
                    <div class="col-9">
                        <div class="progress mb-0">
                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: {{round($user->channel('card', $val->id, $duration))}}%;"></div>
                        </div>
                    </div>
                    <div class="col-3 text-right">
                      <p>{{round($user->channel('card', $val->id, $duration))}}%</p>
                    </div>  
                </div>
                <div class="row justify-content-between align-items-center mb-3">
                    <div class="col-12">
                      <p>Mobile money</p>
                    </div>                    
                    <div class="col-9">
                        <div class="progress mb-0">
                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: {{round($user->channel('mobile', $val->id, $duration))}}%;"></div>
                        </div>
                    </div>
                    <div class="col-3 text-right">
                      <p>{{round($user->channel('mobile', $val->id, $duration))}}%</p>
                    </div>  
                </div>
                <div class="row justify-content-between align-items-center mb-3">
                    <div class="col-12">
                      <p>Bank transfer</p>
                    </div>                    
                    <div class="col-9">
                        <div class="progress mb-0">
                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: {{round($user->channel('bank', $val->id, $duration))}}%;"></div>
                        </div>
                    </div>
                    <div class="col-3 text-right">
                      <p>{{round($user->channel('bank', $val->id, $duration))}}%</p>
                    </div>  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
    @if($user->business()->live==1)
    @if(count(getCountryRates($val->id))>0)
    <div class="card">
      <div class="card-header">
        <h2 class="mb-0 font-weight-bolder text-dark">{{__('Convert')}} {{$val->real->emoji.$val->real->currency}}</h2>
      </div>
      <div class="card-body">
        <form action="{{route('swap.submit', ['id'=>$user->getBalance($val->id)->ref_id])}}" method="post" id="payment-form">
          @csrf
          <input type="hidden" id="min_swap" value="{{$val->swap_min_amount}}">
          <input type="hidden" id="max_swap" value="{{$val->swap_max_amount}}">
          <div class="form-group row">
            <div class="col-lg-5">
              <div class="input-group">
                <input type="number" name="from_amount" step="any" id="from_amount" value="{{$val->swap_min_amount}}" min="{{$val->swap_min_amount}}" max="{{$val->swap_max_amount}}" onkeyup="convert()" class="form-control form-control-lg fw-bold text-lg" autocomplete="off" required>
                <span class="input-group-prepend ">
                  <span class="input-group-text fw-bold text-lg">{{$val->real->emoji.$val->real->currency}}</span>
                </span>
              </div>
              <div class="invalid-feedback" id="invalid-feedback-from">
                You can only swap between {{number_format($val->swap_min_amount,2).$val->real->currency.' - '.number_format($val->swap_max_amount).$val->real->currency}}
              </div>
              @if ($errors->has('from_amount'))
              <span>{{$errors->first('from_amount')}}</span>
              @endif
            </div>
            <div class="col-lg-2 text-center mt-2 mb-2">
              <span class="text-indigo">
                <i class="fal fa-sync-alt fa-3x text-indigo"></i><br><br>
                Rate: <span id="dd"></span><br><br>
                Charge: <span id="cc"></span>
              </span>
            </div>
            <div class="col-lg-5">
              <div class="input-group">
                <input type="number" step="any" class="form-control form-control-lg fw-bold text-lg" autocomplete="off" id="to_amount" onkeyup="convertalt()" name="amount" required>
                <span class="input-group-append">
                  <select class="form-control select form-control-lg fw-bold text-lg" style="padding: 0.35rem 2rem 0.35rem;" id="rate" onclick="convertselect()" name="currency" required>
                    @if(count(getCountryRates($val->id))>0)
                    @foreach(getCountryRates($val->id) as $dal)
                    <option value="{{$dal->id}}*{{$dal->rate}}*{{$dal->to_currency}}*{{$dal->getCurrency->real->currency}}*{{$dal->charge}}">{{$dal->getCurrency->real->emoji.' '.$dal->getCurrency->real->currency}}</option>
                    @endforeach
                    @endif
                  </select>
                </span>
              </div>
              <div class="invalid-feedback" id="invalid-feedback-to">
                You can only swap between {{number_format($val->swap_min_amount,2).$val->real->currency.' - '.number_format($val->swap_max_amount).$val->real->currency}}
              </div>
            </div>
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-neutral" id="ggglogin">{{__('Make Transaction')}}</button>
          </div>
          <div class="accordion" id="accordionExample">
            <div class="card-header border-bottom" style="padding: 1rem 1rem;" id="heading">
              <div data-toggle="collapse" data-target="#collapse" aria-expanded="true" aria-controls="collapse">
                <p class="text-default">Todays Rate</p>
              </div>
            </div>
            <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordionExample">
              <table class="table table-flush border-top-0">
                <thead class="border-top-0">
                  <tr>
                    <th class="text-left">{{__('Currency')}}</th>
                    <th class="text-right">{{__('Rate')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach(getCountryRates($val->id) as $k=>$dal)
                  <tr>
                    <td class="text-left">{{$dal->getCurrency->real->emoji.' '.$dal->getCurrency->real->currency}}</td>
                    <td class="text-right">{{$dal->getCurrency->real->currency_symbol.number_format($dal->rate,2)}}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </form>
      </div>
    </div>
    @endif
    @endif
  </div>
</div>
@stop
@section('script')
<script type="text/javascript">
  $(document).ready(function() {
    var element = document.getElementById('myChart');
    var height = parseInt(200);
    var labelColor = '#a1a5b7';
    var borderColor = '#eff2f5';
    var baseColor = '#00a3ff';
    var lightColor = '#f1faff';

    if (!element) {
      return;
    }
    var options = {
      series: [{
        name: 'Received',
        data: [<?php foreach ($user->getTransactionsExceptPayout($duration, $val->id) as $gval) {
                  echo $gval->amount . ',';
                } ?>]
      }],
      chart: {
        fontFamily: 'inherit',
        type: 'bar',
        height: height,
        width: "100%",
        toolbar: {
          show: !1
        },
        zoom: {
          enabled: true
        },
        sparkline: {
          enabled: !0
        }
      },
      plotOptions: {

      },
      legend: {
        show: true
      },
      dataLabels: {
        enabled: true,
        enabledOnSeries: undefined,
        formatter: function(val, opts) {
          return '@php echo $val->real->currency; @endphp' + val
        },
        textAnchor: 'middle',
        distributed: false,
        offsetX: 0,
        offsetY: 0,
        style: {
          fontSize: '14px',
          fontFamily: 'inherit',
          colors: undefined
        },
        background: {
          enabled: true,
          foreColor: '#000',
          padding: 4,
          borderRadius: 2,
          borderWidth: 1,
          borderColor: '#000',
          opacity: 0.9,
          dropShadow: {
            enabled: false,
            top: 1,
            left: 1,
            blur: 1,
            color: '#000',
            opacity: 0.45
          }
        },
        dropShadow: {
          enabled: false,
          top: 1,
          left: 1,
          blur: 1,
          color: '#000',
          opacity: 0.45
        }
      },
      fill: {
        type: 'solid',
        opacity: 1
      },
      stroke: {
        curve: 'smooth',
        show: true,
        width: 0.5,
        colors: [baseColor]
      },
      xaxis: {
        categories: [<?php foreach ($user->getTransactionsExceptPayout($duration, $val->id) as $gval) {
                        echo "'" . date("M j", strtotime($gval->updated_at)) . "'" . ',';
                      } ?>],
        axisBorder: {
          show: true,
        },
        axisTicks: {
          show: true
        },
        labels: {
          style: {
            colors: labelColor,
            fontSize: '12px'
          }
        },
        crosshairs: {
          position: 'front',
          stroke: {
            color: baseColor,
            width: 1,
            dashArray: 3
          }
        },
        tooltip: {
          enabled: true,
          formatter: undefined,
          offsetY: 0,
          style: {
            fontSize: '12px'
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            colors: labelColor,
            fontSize: '12px'
          }
        }
      },
      states: {
        normal: {
          filter: {
            type: 'none',
            value: 0
          }
        },
        hover: {
          filter: {
            type: 'none',
            value: 0
          }
        },
        active: {
          allowMultipleDataPointsSelection: false,
          filter: {
            type: 'none',
            value: 0
          }
        }
      },
      tooltip: {
        style: {
          fontSize: '12px'
        },
        y: {
          formatter: function(val) {
            return '@php echo $val->real->currency; @endphp' + val
          }
        }
      },
      colors: [lightColor],
      grid: {
        borderColor: borderColor,
        strokeDashArray: 4,
        yaxis: {
          lines: {
            show: true
          }
        }
      },
      markers: {
        strokeColor: baseColor,
        strokeWidth: 3
      }
    };
    var chart = new ApexCharts(element, options);
    chart.render();
  });
</script>
@if(count(getCountryRates($val->id))>0)
<script>
  "use strict";

  function convert() {
    var from_amount = $("#from_amount").val();
    var to_amount = $("#to_amount").val();
    var min = $("#min_swap").val();
    var max = $("#max_swap").val();
    var xx = $("#rate").find(":selected").val();
    var myarr = xx.split("*");
    var gain = parseFloat(from_amount) * parseFloat(myarr[1].split("<"));
    if (parseFloat(from_amount) < parseFloat(min)) {
      //$("#from_amount").val(Math.round(min));
      $("#from_amount").addClass('is-invalid');
      $("#invalid-feedback-from").show();
    } else if (parseFloat(from_amount) > parseFloat(max)) {
      //$("#from_amount").val(Math.round(max));
      $("#from_amount").addClass('is-invalid');
      $("#invalid-feedback-from").show();
    } else if (parseFloat(from_amount) <= parseFloat(max) || parseFloat(from_amount) >= parseFloat(min)) {
      $("#from_amount").removeClass('is-invalid');
      $("#to_amount").removeClass('is-invalid');
      $("#invalid-feedback-from").hide();
      $("#invalid-feedback-to").hide();
      $("#to_amount").val(Math.round(gain));
    }
    $("#dd").text(myarr[1].split("<") + ' ' + myarr[3].split("<"));
    $("#cc").text(myarr[4].split("<") + ' ' + myarr[3].split("<"));
  }
  $("#from_amount").change(convert);
  convert();
</script>
@endif
<script>
  "use strict";

  function convertalt() {
    var from_amount = $("#from_amount").val();
    var to_amount = $("#to_amount").val();
    var xx = $("#rate").find(":selected").val();
    var myarr = xx.split("*");
    var min = parseFloat(myarr[1].split("<")) * parseFloat($("#min_swap").val());
    var max = parseFloat(myarr[1].split("<")) * parseFloat($("#max_swap").val());
    var gain = parseFloat(to_amount) / parseFloat(myarr[1].split("<"));
    if (parseFloat(to_amount) < parseFloat(min)) {
      //$("#to_amount").val(Math.round(min));
      $("#to_amount").addClass('is-invalid');
      $("#invalid-feedback-to").show();
    } else if (parseFloat(to_amount) > parseFloat(max)) {
      //$("#to_amount").val(Math.round(max));
      $("#to_amount").addClass('is-invalid');
      $("#invalid-feedback-to").show();
    } else if (parseFloat(to_amount) <= parseFloat(max) || parseFloat(to_amount) >= parseFloat(min)) {
      $("#to_amount").removeClass('is-invalid');
      $("#from_amount").removeClass('is-invalid');
      $("#invalid-feedback-to").hide();
      $("#invalid-feedback-from").hide();
      $("#from_amount").val(Math.round(gain));
    }
    $("#dd").text(myarr[1].split("<") + ' ' + myarr[3].split("<"));
    $("#cc").text(myarr[4].split("<") + ' ' + myarr[3].split("<"));
  }
  $("#to_amount").change(convertalt);
</script>
<script>
  "use strict";
  function convertselect() {
    var from_amount = $("#from_amount").val();
    var to_amount = $("#to_amount").val();
    var xx = $("#rate").find(":selected").val();
    var myarr = xx.split("*");
    var gain = parseFloat(from_amount) * parseFloat(myarr[1].split("<"));
    $("#to_amount").val(Math.round(gain));
    $("#dd").text(myarr[1].split("<") + ' ' + myarr[3].split("<"));
    $("#cc").text(myarr[4].split("<") + ' ' + myarr[3].split("<"));
  }
  $("#rate").change(convertselect);
</script>
<script>
  "use strict"
  $('#ggglogin').on('click', function() {
    $(this).text('Please wait ...').attr('disabled', 'disabled');
    $('#payment-form').submit();
  });
</script>
@endsection