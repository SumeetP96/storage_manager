@extends('layouts.auth')

@section('content')

@php
    $user = \App\User::find(1);
    $trialExpired = Hash::check(date('Ymd'), $user->dm_token) ? TRUE : FALSE;
    $trialPeriod = (!$trialExpired && !Hash::check('full-version', $user->dm_token)) ? TRUE : FALSE;
    $fullVersion = (Hash::check('full-version', $user->dm_token)) ? TRUE : FALSE;
@endphp

@if ($trialPeriod)

<div class="card shadow-sm w-100">
  <div class="card-body">
    <div class="mb-4 pb-3">
      <span class="display-4">Login</span>
      <span class="h5 text-secondary ml-3">( Trial version )</span>
    </div>

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div class="form-group">
        <label for="username">Username</label>
        <input id="text" type="username" class="form-control @error('username') is-invalid @enderror" name="username"
          value="{{ old('username') ? old('username') : 'admin' }}" autocomplete="username">

        @error('username')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>

      <div class="form-group">
        <label for="password">Password</label>

        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
          name="password" autocomplete="current-password" autofocus>

        @error('password')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>

      <button type="submit" class="btn btn-success w-100 mt-4">Login</button>

    </form>
  </div>
</div>

@elseif ($trialExpired)

<div class="card shadow-sm w-100" style="margin-top: 20%">
  <div class="card-body">
    <div class="h4">Thank you</div>

    <p class="mt-3">We hope you liked this application.</p>

    <p class="mt-4">
      Your trial period has expired.
      You can contact the developer and purchase the full version if you want to continue using this application.
    </p>

    <p class="mt-4">
      <b>Sumeet Prajapati</b> - 9662865252
    </p>
  </div>
</div>

@elseif ($fullVersion)

<div class="card shadow-sm w-100">
  <div class="card-body">
    <div class="display-4 mb-4 pb-3">Login</div>

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div class="form-group">
        <label for="username">Username</label>
        <input id="text" type="username" class="form-control @error('username') is-invalid @enderror" name="username"
          value="{{ old('username') ? old('username') : 'admin' }}" autocomplete="username">

        @error('username')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>

      <div class="form-group">
        <label for="password">Password</label>

        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
          name="password" autocomplete="current-password" autofocus>

        @error('password')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
        @enderror
      </div>

      <button type="submit" class="btn btn-success w-100 mt-4">Login</button>

    </form>
  </div>
</div>

@endif
@endsection
