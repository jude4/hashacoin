<!doctype html>
<html class="no-js" lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <base href="{{url('/')}}" />
  <title>{{$set->site_name}} {{__('Dashboard')}}</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1" />
  <meta name="robots" content="index, follow">
  <meta name="apple-mobile-web-app-title" content="{{$set->site_name}}" />
  <meta name="application-name" content="{{$set->site_name}}" />
  <meta name="msapplication-TileColor" content="#ffffff" />
  <meta name="description" content="{{$set->site_desc}}" />
  <link rel="shortcut icon" href="{{asset('asset/'.$logo->image_link2)}}" />
  <link rel="stylesheet" href="{{asset('asset/css/toast.css')}}" type="text/css">
  <link rel="stylesheet" href="{{asset('asset/dashboard/css/argon.css?v=1.1.0')}}" type="text/css">
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/select2/dist/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/prism/prism.css')}}">
  <link rel="stylesheet" href="{{asset('asset/dashboard/css/docs.css')}}" type="text/css">
  <link href="{{asset('asset/fonts/fontawesome/css/all.css')}}" rel="stylesheet" type="text/css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/datatables.net-select-bs4/css/select.bootstrap4.min.css')}}">
  <style type="text/css">
    .preloader {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 9999;
      background-image: url("{{asset('asset/'.$logo->preloader)}}");
      background-repeat: no-repeat;
      background-color: #FFF;
      background-position: center;
    }
  </style>
  @yield('css')
  @include('partials.font')
</head>
<!-- header begin-->
@if($set->preloader==1)
<div class="preloader"></div>
@endif

<body>
  <!-- Sidenav -->
  <nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-kind-steel" id="sidenav-main">
    <div class="scrollbar-inner">
      <!-- Brand -->
      <div class="sidenav-header d-flex align-items-center">
        <a class="navbar-brand" href="{{route('home')}}">
          <img src="{{asset('asset/'.$logo->dark)}}" class="navbar-brand-img" alt="...">
        </a>
        <div class="ml-auto">
          <!-- Sidenav toggler -->
          <div class="sidenav-toggler d-none d-xl-block" data-action="sidenav-unpin" data-target="#sidenav-main">
            <div class="sidenav-toggler-inner">
              <i class="sidenav-toggler-line"></i>
              <i class="sidenav-toggler-line"></i>
              <i class="sidenav-toggler-line"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="sidenav-header mb-5">
        <div class="navbar-brand">
          <div class="card rounded">
            <div class="media align-items-center mx-2 py-3">
              <span class="rounded-circle">
                <img style="height:auto; max-width:40px;" src="{{asset('asset/profile/person.png')}}">
              </span>
              <div class="media-body ml-2">
                <h3 class="mb-0 h4 text-dark font-weight-bolder">{{ucwords(strtolower($user->first_name))}} {{ucwords(strtolower($user->last_name))}}, 👋🏼</h3>
                <p class="mb-1 text-dark">{{__('Hi')}}, {{__('What would you like to do?')}}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="navbar-inner">
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
          <h6 class="navbar-heading p-0">{{__('Business')}}</h6>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link @if(route('user.dashboard')==url()->current()) active border-left-2 border-info @endif" href="{{route('user.dashboard')}}">
                <i class="fal fa-house-user"></i>
                <span class="nav-link-text">{{__('Dashboard')}}</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link @if(route('user.payment')==url()->current()) active border-left-2 border-info @endif" href="{{route('user.payment')}}">
                <i class="fal fa-link"></i>
                <span class="nav-link-text">{{__('Payment Links')}}</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link @if(route('user.transactions')==url()->current()) active border-left-2 border-info @endif" href="{{route('user.transactions')}}">
                <i class="fal fa-sync"></i>
                <span class="nav-link-text">{{__('Transactions')}}</span>
              </a>
            </li>             
            <li class="nav-item">
              <a class="nav-link @if(route('user.balance')==url()->current()) active border-left-2 border-info @endif" href="{{route('user.balance')}}">
                <i class="fal fa-wallet"></i>
                <span class="nav-link-text">{{__('Wallets')}}</span>
              </a>
            </li>            
            <li class="nav-item">
              <a class="nav-link @if(route('user.payouts')==url()->current()) active border-left-2 border-info @endif" href="{{route('user.payouts')}}">
                <i class="fal fa-arrow-down"></i>
                <span class="nav-link-text">{{__('Settlements')}}</span>
              </a>
            </li>
            @if(count(getAcceptedCountryVirtual())>0)
            <li class="nav-item">
              <a class="nav-link @if(route('user.card')==url()->current()) active border-left-2 border-info @endif" href="{{route('user.card')}}">
                <i class="fal fa-credit-card-front"></i>
                <span class="nav-link-text">{{__('Cards')}}</span>
              </a>
            </li>
            @endif
            <li class="nav-item">
              <a class="nav-link @if(route('user.chargeback')==url()->current()) active border-left-2 border-info @endif" href="{{route('user.chargeback')}}">
                <i class="fal fa-ban"></i>
                <span class="nav-link-text">{{__('Disputes')}}</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
  <div class="main-content" id="panel">
    <!-- Topnav -->
    <nav class="navbar navbar-top navbar-expand navbar-dark">
      <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- Navbar links -->
          <ul class="navbar-nav align-items-center ml-md-auto">
            <li class="nav-item d-xl-none">
              <!-- Sidenav toggler -->
              <div class="pr-3 sidenav-toggler sidenav-toggler-light" data-action="sidenav-pin" data-target="#sidenav-main">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </div>
            </li>
          </ul>
          <ul class="navbar-nav align-items-center">
            <span class="ml-0 text-md text-dark"> You're in @if($user->business()->live==1) {{__('Live')}} @else {{__('Test')}} @endif mode</span>
            <li class="nav-item ml-2 mt-2">
              <label class="switch">
                <input type="checkbox" onclick="changeMode()" @if($user->business()->live==1) checked @endif>
                <span class="slider round"></span>
              </label>
            </li>
          </ul>
          <ul class="navbar-nav align-items-center ml-auto ml-md-0">
            <li class="nav-item dropdown">
              <a class="nav-link pr-0" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="media align-items-center">
                  <div class="icon icon-shape bg-new-york text-dark rounded-circle shadow">
                    {{strtoupper(substr($user->business()->name, 0, 2))}}
                  </div>
                  <div class="media-body ml-2 d-none d-md-block">
                    <p class="mb-0 text-sm font-weight-bold text-dark">{{$user->business()->name}}</p>
                    <span class="mb-0 text-sm text-dark">ID: {{$user->business()->reference}}</span>
                  </div>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-right">
                @foreach(getBusiness($user->business_id, $user->id) as $val)
                <a href="{{route('default.business', ['id'=>$val->reference])}}" class="dropdown-item">
                  <i class="fal fa-briefcase"></i> <span>{{ucwords($val->name)}}</span>
                </a>
                @endforeach
                @if(count(getBusiness($user->business_id, $user->id) )>0)
                <div class="dropdown-divider"></div>
                @endif
                <a href="{{route('new.business')}}" class="dropdown-item">
                  <i class="fal fa-plus"></i>
                  <span>Add a new business</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{route('user.profile')}}" class="dropdown-item">
                  <i class="fal fa-cog"></i> <span>Settings</span>
                </a>
                <a href="{{route('user.documentation')}}" class="dropdown-item">
                  <i class="fal fa-file-alt"></i> <span>Documentation</span>
                </a>                
                <a href="{{route('user.ticket')}}" class="dropdown-item">
                  <i class="fal fa-user"></i> <span>Support</span>
                </a>
                <a href="{{route('user.logout')}}" class="dropdown-item">
                  <i class="fal fa-sign-out"></i> <span>Log out</span>
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="header pb-6">
      <div class="container-fluid">
        <div class="header-body">
        </div>
      </div>
    </div>
    <!-- header end -->

    @yield('content')


    <!-- footer begin -->
  </div>
  </div>
  {!!$set->livechat!!}
  {!!$set->analytic_snippet!!}
  <!-- Argon Scripts -->
  <!-- Core -->
  <script src="{{asset('asset/dashboard/vendor/jquery/dist/jquery.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/js-cookie/js.cookie.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/jquery.scrollbar/jquery.scrollbar.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js')}}"></script>
  <!-- Optional JS -->
  <script src="{{asset('asset/dashboard/vendor/prism/prism.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/jvectormap-next/jquery-jvectormap.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/js/vendor/jvectormap/jquery-jvectormap-world-mill.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.print.min.js"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-select/js/dataTables.select.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/clipboard/dist/clipboard.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/select2/dist/js/select2.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/nouislider/distribute/nouislider.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/dropzone/dist/min/dropzone.min.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <!-- Argon JS -->
  <script src="{{asset('asset/dashboard/js/argon.js?v=1.1.0')}}"></script>
  <script src="{{asset('asset/js/toast.js')}}"></script>
  <script src="{{asset('asset/tinymce/tinymce.min.js')}}"></script>
  <script src="{{asset('asset/tinymce/init-tinymce.js')}}"></script>
  <script src="{{asset('asset/js/countries.js')}}"></script>
</body>

</html>
@yield('script')
@if (session('success'))
<script>
  "use strict";
  toastr.success("{!! session('success') !!}");
</script>
@endif

@if (session('alert'))
<script>
  "use strict";
  toastr.warning("{!! session('alert') !!}");
</script>
@endif
@if($user->business()->live==0)
<script>
  "use strict";

  function changeMode() {
    window.location.href = "{{route('user.account.mode', ['id'=>1])}}"
  }
</script>
@else
<script>
  "use strict";

  function changeMode() {
    window.location.href = "{{route('user.account.mode', ['id'=>0])}}"
  }
</script>
@endif
<script type="text/javascript">
  $('.preloader').fadeOut(1000);
</script>