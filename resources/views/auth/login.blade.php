@extends('layouts.auth')

@section('content')
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
@endsection
