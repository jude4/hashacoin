@extends('master')

@section('content')
<!-- Page content -->
<div class="container-fluid mt--6">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <form class="navbar-search navbar-search-light" action="{{route('admin.payment.search')}}" method="post" id="navbar-search-main">
                            @csrf
                            <div class="form-group mb-0">
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fal fa-search"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="Name, amount" name="search" type="text">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-responsive py-4">
                <table class="table table-flush" id="">
                    <thead>
                        <tr>
                            <th>{{__('S / N')}}</th>
                            <th>{{__('User')}}</th>
                            <th>@sortablelink('name', 'Name')</th>
                            <th>@sortablelink('business_id', 'Business')</th>
                            <th>@sortablelink('amount', 'Amount')</th>
                            <th>@sortablelink('active', 'Status')</th>
                            <th>@sortablelink('status', 'Suspended')</th>
                            <th>@sortablelink('created_at', 'Date')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($links as $k=>$val)
                        <tr>
                            <td>{{++$k}}.</td>
                            <td>@if($val->user->first_name==null) [Deleted] @else <a href="{{route('user.manage', ['id'=>$val->user_id])}}">{{$val->user->first_name.' '.$val->user->last_name}}</a> @endif</td>
                            <td>@if(strlen($val->name)>10){{substr($val->name,0, 10)}}..@else {{$val->name}} @endif</td>
                            <td>@if($val->business()==null) No business attached @else {{$val->business()->name}} @endif</td>
                            <td>@if($val->amount==null) Not fixed [{{$val->getCurrency->real->currency}}]@else {{$val->getCurrency->real->currency_symbol.number_format($val->amount, 2)}} @endif</td>
                            <td>
                                @if($val->active==1)
                                <span class="badge badge-pill badge-success">{{__('Active')}}</span>
                                @else
                                <span class="badge badge-pill badge-danger">{{__('Disabled')}}</span>
                                @endif
                            </td>
                            <td>
                                @if($val->status==1)
                                <span class="badge badge-pill badge-success">{{__('Yes')}}</span>
                                @else
                                <span class="badge badge-pill badge-danger">{{__('No')}}</span>
                                @endif
                            </td>
                            <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                            <td class="text-center">
                                <div class="">
                                    <div class="dropdown">
                                        <a class="text-dark" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fal fa-chevron-circle-down"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item" target="_blank" href="{{route('payment.link', ['id' => $val->ref_id])}}"><i class="fal fa-eye"></i>{{__('Preview')}}</a>
                                            @if($val->status==1)
                                            <a class='dropdown-item' href="{{route('links.unpublish', ['id' => $val->id])}}"><i class="fal fa-check"></i>{{__('Unsuspend')}}</a>
                                            @else
                                            <a class='dropdown-item' href="{{route('links.publish', ['id' => $val->id])}}"><i class="fal fa-ban"></i>{{__('Suspend')}}</a>
                                            @endif
                                            <a class="dropdown-item" href="{{route('admin.linkstrans', ['id' => $val->id])}}"><i class="fal fa-sync"></i>{{__('Transactions')}}</a>
                                            <a data-toggle="modal" data-target="#delete{{$val->id}}" href="" class="dropdown-item"><i class="fal fa-trash"></i>{{__('Delete')}}</a>
                                            <a data-toggle="modal" data-target="#description{{$val->id}}" href="" class="dropdown-item"><i class="fal fa-info-circle"></i>{{__('Description')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Showing 1 to {{$links->count()}} of {{ $links->total() }} entries</p>
                        </div>
                        <div class="col-md-12 text-right">
                            {{ $links->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @foreach($links as $k=>$val)
        <div class="modal fade" id="delete{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <div class="card bg-white border-0 mb-0">
                            <div class="card-header">
                                <h3 class="mb-0">{{__('Are you sure you want to delete this?')}}</h3>
                            </div>
                            <div class="card-body px-lg-5 py-lg-5 text-right">
                                <button type="button" class="btn btn-neutral btn-sm" data-dismiss="modal">{{__('Close')}}</button>
                                <a href="{{route('delete.link', ['id' => $val->id])}}" class="btn btn-danger btn-sm">{{__('Proceed')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="description{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal- modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <p class="mb-0 text-sm">{{$val->description}}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-neutral btn-sm" data-dismiss="modal">{{__('Close')}}</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        @stop