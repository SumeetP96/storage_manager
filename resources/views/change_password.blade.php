@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-12 pt-5">
            <div class="card shadow-sm mt-5">

                <div class="card-body">
                    <div class="h3 mb-4 pb-3">Update Password</div>

                    <form method="POST" action="{{ route('update.password') }}">
                        @csrf
                        <div class="form-group">
                            <label for="password">Old Password</label>

                            <input id="password" type="password" autofocus
                                class="form-control @error('old_password') is-invalid @enderror" name="old_password">

                            @error('old_password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">New Password</label>

                            <input id="password" type="password"
                                class="form-control @error('new_password') is-invalid @enderror" name="new_password">

                            @error('new_password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Confirm Password</label>

                            <input id="password" type="password"
                                class="form-control @error('confirm_password') is-invalid @enderror"
                                name="confirm_password">

                            @error('confirm_password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-content-center mt-4">
                            <button type="submit" class="btn btn-success pb-2">Update password</button>
                            <a href="/" class="btn btn-secondary pb-2">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
