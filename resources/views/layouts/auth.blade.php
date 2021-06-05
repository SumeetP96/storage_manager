<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Storage Manager Login</title>

  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
  <style>
    body,
    html {
      height: 100%;
    }

    .login-wrapper {
      height: 100%;
      width: 100%;
      background-image: url({{ url('images/login_page/1.jpg')}});
      background-position: center;
      background-size: cover;
      background-repeat: no-repeat;
    }
    .left-side {
      color: white;
      padding-left: 10%;
      margin-top: 22%;
    }
    .right-side {
      width: 35%;
      padding-right: 10%;
      margin-top: 13%;
    }
  </style>
</head>

<body>
  <div class="login-wrapper d-flex flex-column justify-content-between">

    <div class="d-flex justify-content-between">

      <div class="left-side">
        <div class="display-4">Welcome to Storage Manager</div>
        <h5 class="mt-2 pl-1">A simple stock management system for your business</h5>
      </div>

      <div class="right-side">
        @yield('content')
      </div>

    </div>

    <div class="text-center mb-3 text-light">
      <div>Designed and developed by Sumeet Prajapati</div>
      @php $currentYear = date('Y') @endphp
      <small>
        Copyright &#169;
        @if($currentYear == '2021')
          {{ $currentYear }}
        @else
          2021-{{ $currentYear }}
        @endif
        Rameshwar Trading Company. All rights reserved.
        <a class="text-light" href="/terms_of_use.html">Terms of use.</a>
      </small>
    </div>
  </div>
</body>

</html>
