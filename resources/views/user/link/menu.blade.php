<!doctype html>
<html class="no-js" lang="en">

<head>
  <title>{{ $title }} | {{$set->site_name}}</title>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1" />
  <meta name="robots" content="index, follow">
  <meta name="apple-mobile-web-app-title" content="{{$set->site_name}}" />
  <meta name="application-name" content="{{$set->site_name}}" />
  <meta name="msapplication-TileColor" content="#ffffff" />
  <meta name="description" content="{{$set->site_desc}}" />
  <link rel="shortcut icon" href="{{asset('asset/'.$logo->image_link2)}}" />
  <link rel="stylesheet" href="{{asset('asset/css/toast.css')}}" type="text/css">
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/@fortawesome/fontawesome-free/css/all.min.css')}}" type="text/css">
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('asset/dashboard/vendor/datatables.net-select-bs4/css/select.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('asset/dashboard/css/argon.css?v=1.1.0')}}" type="text/css">
  <link href="{{asset('asset/fonts/fontawesome/css/all.css')}}" rel="stylesheet" type="text/css">
  <link href="{{asset('asset/fonts/fontawesome/styles.min.css')}}" rel="stylesheet" type="text/css">
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

<body class="bg-kind-steel" style="min-height: 100vh;">
  @if($set->preloader==1)
  <div class="preloader"></div>
  @endif
  <nav id="navbar-main" class="navbar navbar-horizontal navbar-transparent navbar-main navbar-expand-lg navbar-dark">
    <div class="container">
      <div class="navbar-collapse navbar-custom-collapse collapse" id="navbar-collapse">
        <div class="navbar-collapse-header">
        </div>
      </div>
    </div>
  </nav>
  <!-- header end -->

  @yield('content')
  {!!$set->livechat!!}
  {!!$set->analytic_snippet!!}
  <!-- Argon Scripts -->
  <!-- Core -->
  <script src="{{asset('asset/dashboard/vendor/jquery/dist/jquery.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
  <script src="https://js.stripe.com/v3/"></script>
  <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
  <script src="{{asset('asset/dashboard/vendor/js-cookie/js.cookie.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/jquery.scrollbar/jquery.scrollbar.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/jvectormap-next/jquery-jvectormap.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/js/vendor/jvectormap/jquery-jvectormap-world-mill.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-buttons/js/buttons.flash.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/datatables.net-select/js/dataTables.select.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/clipboard/dist/clipboard.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/select2/dist/js/select2.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/nouislider/distribute/nouislider.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/quill/dist/quill.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/vendor/dropzone/dist/min/dropzone.min.js')}}"></script>
  <script src="{{asset('asset/dashboard/js/argon.js?v=1.1.0')}}"></script>
  <script src="{{asset('asset/tinymce/tinymce.min.js')}}"></script>
  <script src="{{asset('asset/tinymce/init-tinymce.js')}}"></script>
  <script src="{{asset('asset/js/toast.js')}}"></script>
</body>

</html>
@yield('script')
<script>
  // Create a Stripe client.
  var stripe = Stripe("{{$set->public_key}}");

  // Create an instance of Elements.
  var elements = stripe.elements({
    fonts: [{
      cssSrc: 'https://fonts.googleapis.com/css2?family={{$set->default_font}}:wght@300;400;600;700;800;900&display=swap',
    }]
  });

  var style = {
    base: {
      color: '#222221',
      fontFamily: '{{$set->default_font}}',
      fontSmoothing: 'antialiased',
      fontSize: '14px',
      '::placeholder': {
        color: '#222221'
      }
    },
    invalid: {
      color: '#fa755a',
      iconColor: '#fa755a'
    }
  };

  // Create an instance of the card Element.
  var card = elements.create('card', {
    style: style
  });

  // Add an instance of the card Element into the `card-element` <div>.
  card.mount('#card-element');

  // Handle real-time validation errors from the card Element.
  card.addEventListener('change', function(event) {
    var displayError = document.getElementById('card-errors');
    if (event.error) {
      displayError.textContent = event.error.message;
    } else {
      displayError.textContent = '';
    }
  });

  // Handle form submission.
  var form = document.getElementById('payment-form');
  form.addEventListener('submit', function(event) {
    event.preventDefault();

    stripe.createSource(card).then(function(result) {
      if (result.error) {
        // Inform the user if there was an error.
        var errorElement = document.getElementById('card-errors');
        errorElement.textContent = result.error.message;
      } else {
        // Send the token to your server.
        stripeSourceHandler(result.source);
      }
    });
  });

  // Submit the form with the source ID.
  function stripeSourceHandler(source) {
    // Insert the source ID into the form so it gets submitted to the server
    var form = document.getElementById('payment-form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeSource');
    hiddenInput.setAttribute('value', source.id);
    form.appendChild(hiddenInput);
    // Submit the form
    form.submit();
    $('#payment-submit').html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', 'disabled');
  }
</script>
@if (session('success'))
<script>
  "use strict";
  toastr.success("{{ session('success') }}");
</script>
@endif

@if (session('alert'))
<script>
  "use strict";
  toastr.warning("{{ session('alert') }}");
</script>
@endif
<script type="text/javascript">
  $('.preloader').fadeOut(1000);
</script>