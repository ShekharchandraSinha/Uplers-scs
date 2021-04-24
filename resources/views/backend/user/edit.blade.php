@extends('core.backend.app', ['pageTitle' => 'User'])

@push('styles')
@endpush

@push('scripts')
    <script>
        $("#mobile").on("keypress", function(event) {
            evt = (event) ? event : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 32 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        });

    </script>
@endpush

@section('page-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            User Update
                        </h3>
                    </div>
                    <form role="form" class="form-horizontal" method="POST" action="{{ route('admin.user.update', $item->id) }}">
                        <!-- /.card-header -->
                        <div class="card-body">
                            @csrf
                            @method('PATCH')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="first-name">First Name</label>
                                    <input type="text" name="first-name" class="form-control @error('first-name') is-invalid @enderror" id="first-name" placeholder="Enter first name" value="{{ old('first-name', $item->first_name) }}" autofocus required>
                                    @error('first-name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="last-name">Last Name</label>
                                    <input type="text" name="last-name" class="form-control @error('last-name') is-invalid @enderror" id="last-name" placeholder="Enter last name" value="{{ old('last-name', $item->last_name) }}" required>
                                    @error('last-name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input type="email" name="email" pattern="^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter email" value="{{ old('email', $item->email) }}" required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="mobile">Mobile</label>
                                    <input type="tel" name="mobile" class="form-control @error('mobile') is-invalid @enderror" id="mobile" placeholder="Enter mobile" value="{{ old('mobile', $item->mobile) }}" maxlength="13" minlength="10" required>
                                    @error('mobile')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password">Password (Leave blank for no change)</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Password" minlength="6">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="confirm-password">Confirm Password (Leave blank for no change)</label>
                                    <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="confirm-password" placeholder="Confirm Password" minlength="6">
                                    @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="active" class="custom-control-input" id="account_active" @if ($item->active) checked @endif>
                                        <label class="custom-control-label" for="account_active">Active?</label>
                                    </div>
                                    @error('active')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info mx-1">Update</button>
                            <a href="{{ route('admin.user.index') }}" class="btn mx-1">Cancel</a>
                        </div>
                        <!-- /.card-footer -->
                    </form>
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
@endsection
