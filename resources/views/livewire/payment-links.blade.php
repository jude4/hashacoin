<div>
    <div class="row">
        @forelse($links as $k=>$val)
        <div class="col-md-4">
            <div class="card bg-white">
                <!-- Card body -->
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-8">
                            <h5 class="h3 font-weight-bolder">@if(strlen($val->name)>20){{substr($val->name,0, 20)}}..@else {{$val->name}} @endif</h5>
                        </div>
                        <div class="col-4 text-right">
                            <a class="mr-0 text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="fadse">
                                <i class="fal fa-chevron-circle-down"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-left">
                                <a class="dropdown-item" target="_blank" href="{{route('payment.link', ['id' => $val->ref_id])}}"><i class="fal fa-eye"></i>{{__('Preview')}}</a>
                                @if($val->active==1)
                                <a class='dropdown-item' href="{{route('payment.disable', ['id' => $val->ref_id])}}"><i class="fal fa-ban"></i>{{ __('Disable')}}</a>
                                @else
                                <a class='dropdown-item' href="{{route('payment.enable', ['id' => $val->ref_id])}}"><i class="fal fa-check"></i>{{ __('Activate')}}</a>
                                @endif
                                <a class="dropdown-item" href="{{route('payment.transactions', ['id' => $val->ref_id])}}"><i class="fal fa-sync"></i>{{__('Transactions')}}</a>
                                <a class="dropdown-item" data-toggle="modal" data-target="#edit{{$val->id}}" href="#"><i class="fal fa-pencil"></i>{{__('Edit')}}</a>
                                <a class="dropdown-item" data-toggle="modal" data-target="#share{{$val->id}}" href="#"><i class="fal fa-share"></i>{{__('Share')}}</a>
                                <a class="dropdown-item" data-toggle="modal" data-target="#delete{{$val->id}}" href=""><i class="fal fa-trash"></i>{{__('Delete')}}</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <p>{{__('Amount')}}: @if($val->amount==null) Not fixed [{{$val->getCurrency->real->currency}}]@else {{$val->getCurrency->real->currency_symbol.number_format($val->amount, 2)}} @endif</p>
                            <p class="mb-2">{{__('Created')}}: {{date("j, M Y", strtotime($val->created_at))}}</p>
                            @if($val->active==1)
                            <span class="badge badge-pill badge-success"><i class="fal fa-check"></i> {{__('Active')}}</span>
                            @else
                            <span class="badge badge-pill badge-danger"><i class="fal fa-ban"></i> {{__('Disabled')}}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="edit{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="mb-0 font-weight-bolder">{{__('Edit Payment')}}</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{route('update.payment', ['id' => $val->ref_id])}}" method="post">
                        <div class="modal-body">
                            @csrf
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="text" name="name" class="form-control" value="{{$val->name}}" placeholder="{{__('Name of your Page')}}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control" id="amount" autocomplete="off" name="amount" value="{{$val->amount}}" placeholder="How much?">
                                        <span class="input-group-append">
                                            <select class="form-control select" style="padding: 0.35rem 2rem 0.35rem;" id="currency" name="currency" required>
                                                @if(count(getAcceptedCountry())>0)
                                                @foreach(getAcceptedCountry() as $dval)
                                                <option value="{{$dval->id}}*{{$dval->min_amount}}*@if($dval->max_amount==null)empty @else{{$dval->max_amount}}@endif" @if($val->currency==$dval->id)selected @endif>{{$dval->real->emoji.' '.$dval->real->currency}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </span>
                                    </div>
                                    <span class="form-text">{{__('Leave empty to allow customers enter desired amount')}}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <textarea type="text" name="description" rows="5" class="form-control" placeholder="{{__('Tell your customer why you are requesting this payment')}}" required>{{$val->description}}</textarea>
                                </div>
                            </div>
                            <input type="hidden" name="id" value="{{$val->id}}">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-neutral btn-block">{{__('Update Payment')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="delete{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="mb-0 font-weight-bolder">{{__('Delete Payment')}}</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{{__('Are you sure you want to delete this?, all transaction related to this payment link will also be deleted')}}</p>
                    </div>
                    <div class="modal-footer">
                        <a href="{{route('delete.payment', ['id' => $val->ref_id])}}" class="btn btn-danger btn-block">{{__('Proceed')}}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="share{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="mb-0 font-weight-bolder">{{__('Share')}}</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="text-center">
                                {!! QrCode::eye('circle')->style('round')->size(250)->generate(route('payment.link', ['id' => $val->ref_id])); !!}
                            </div>
                            <div class="text-center mb-3 mt-3">
                                <p>{{__('Scan QR code or Share using:')}}</p>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{route('payment.link', ['id' => $val->ref_id])}}">
                                    <div class="input-group-append">
                                        <span class="input-group-text castro-copy" data-clipboard-text="{{route('payment.link', ['id' => $val->ref_id])}}" title="Copy to clipboard"><i class="fal fa-copy"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <a href="https://wa.me/?text={{route('payment.link', ['id' => $val->ref_id])}}" target="_blank" class="btn btn-neutral btn-icon-only">
                                    <span class="btn-inner--icon"><i class="fab fa-whatsapp"></i></span>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{route('payment.link', ['id' => $val->ref_id])}}" target="_blank" class="btn btn-neutral btn-icon-only">
                                    <span class="btn-inner--icon"><i class="fab fa-facebook"></i></span>
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{route('payment.link', ['id' => $val->ref_id])}}" target="_blank" class="btn btn-neutral btn-icon-only">
                                    <span class="btn-inner--icon"><i class="fab fa-twitter"></i></span>
                                </a>
                                <a href="mailto:?body={{route('payment.link', ['id' => $val->ref_id])}}" class="btn btn-neutral btn-icon-only">
                                    <span class="btn-inner--icon"><i class="fal fa-envelope"></i></span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-md-12 mb-5">
            <div class="text-center mt-8">
                <div class="btn-wrapper text-center mb-3">
                    <a href="javascript:void;" class="mb-3">
                        <span class=""><i class="fal fa-link fa-4x text-info"></i></span>
                    </a>
                </div>
                <h3 class="text-dark">{{__('No Payment Link Found')}}</h3>
                <p class="text-dark">{{__('We couldn\'t find any payment to this account')}}</p>
            </div>
        </div>
        @endforelse
    </div>
    <div class="row align-items-center justify-content-lg-between mt-5">
        <div class="col-md-12">
            <p>{{__('Showing 1 to')}} {{$links->count()}} of {{ $links->total() }} {{__('entries')}}</p>
            {{ $links->onEachSide(2)->links('pagination::bootstrap-4')}}
        </div>
    </div>
</div>