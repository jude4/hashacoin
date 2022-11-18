@extends('userlayout')

@section('content')
<!-- Page content -->
<div class="container-fluid mt--7">
    <div class="content-wrapper mt-3">
        <div class="card">
            <div class="table-responsive py-4">
                <table class="table table-flush" id="example">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th class="text-center">{{__('Status')}}</th>
                            <th class="text-center">{{__('Amount')}}</th>
                            <th class="text-center">{{__('Customer')}}</th>
                            <th class="text-center">{{__('Type')}}</th>
                            <th class="text-center">{{__('Payment')}}</th>
                            <th class="text-center">{{__('Reference')}}</th>
                            <th class="text-center">{{__('Date')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->getNoPayout() as $k=>$val)
                        @if($user->business()->live==1)
                        @if($val->payment_type=="card")
                        @php $type="analytics" @endphp
                        @else
                        @php $type="webhook" @endphp
                        @endif
                        @else
                        @php $type="webhook" @endphp
                        @endif
                        <tr class="cursor-pointer">
                            <td data-href="{{route('view.transactions', ['id' => $val->ref_id,'type' => $type])}}">
                                {{$loop->iteration}}.
                            </td>
                            <td data-href="{{route('view.transactions', ['id' => $val->ref_id,'type' => $type])}}" class="text-center">
                                @if($val->status==0) <span class="badge badge-pill badge-primary"><i class="fal fa-sync"></i> Pending</span>
                                @elseif($val->status==1) <span class="badge badge-pill badge-success"><i class="fal fa-check"></i> Success</span>
                                @elseif($val->status==2) <span class="badge badge-pill badge-danger"><i class="fal fa-ban"></i> Failed/cancelled</span>
                                @elseif($val->status==3) <span class="badge badge-pill badge-info"><i class="fal fa-arrow-alt-circle-left"></i> Refunded</span>
                                @elseif($val->status==4) <span class="badge badge-pill badge-info"><i class="fal fa-arrow-alt-circle-left"></i> Reversed (Chargeback)</span>
                                @endif
                            </td>
                            <td data-href="{{route('view.transactions', ['id' => $val->ref_id,'type' => $type])}}" class="text-center">
                                {{$val->getCurrency->real->currency.' '.number_format($val->amount, 2)}}
                            </td>
                            <td data-href="{{route('view.transactions', ['id' => $val->ref_id,'type' => $type])}}" class="text-center">
                                @if($val->email!=null){{$val->email}}@else {{$user->email}} @endif
                            </td>
                            <td data-href="{{route('view.transactions', ['id' => $val->ref_id,'type' => $type])}}" class="text-center">
                                @if($val->type==1) Payment link
                                @elseif($val->type==2) API
                                @elseif($val->type==3) Payout
                                @elseif($val->type==4) Funding
                                @elseif($val->type==5) Swapping
                                @endif
                            </td>
                            <td>{{$val->payment_type}}</td>
                            <td class="text-center castro-copy" data-clipboard-text="{{$val->ref_id}}">{{$val->ref_id}} <i class="fal fa-copy"></i></td>
                            <td data-href="{{route('view.transactions', ['id' => $val->ref_id,'type' => $type])}}" class="text-center">{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @stop
        @section('script')
        <script src="{{asset('asset/dashboard/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('asset/dashboard/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
        <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.print.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#example').DataTable({
                    "language": {
                        "lengthMenu": "Show _MENU_",
                    },
                    "dom": "<'row'" +
                        "<'col-md-6 col-12 d-flex align-items-center justify-conten-start mb-2'B>" +
                        "<'col-md-6 col-12 d-flex align-items-center justify-content-end'f>" +
                        ">" +

                        "<'table-responsive'tr>" +

                        "<'row'" +
                        "<'col-sm-12 col-md-4 d-flex align-items-center justify-content-center justify-content-md-start'l>" +
                        "<'col-sm-12 col-md-8 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                        ">",
                    buttons: [
                        'copy', 'excel', 'pdf', 'print'
                    ]
                });
            });
        </script>
        <script>
            $('td[data-href]').on("click", function() {
                window.location.href = $(this).data('href');
            });
        </script>
        <script>
            'use strict';
            var clipboard = new ClipboardJS('.castro-copy');

            clipboard.on('success', function(e) {
                navigator.clipboard.writeText(e.text);
                $(e.trigger)
                    .attr('title', 'Copied!')
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