@extends('core.backend.app', ['pageTitle' => 'Portfolios'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".select2").select2({
                theme: 'bootstrap4'
            });
        })

        $(document).on("keypress", "#experience-years-years, #clientele-agency, #clientele-companies", function(event) {
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
                            Portfolio Create
                        </h3>
                    </div>
                    <form role="form" class="form-horizontal" method="POST" action="{{ route('admin.portfolio.store') }}">
                        <!-- /.card-header -->
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="first-name">First Name</label>
                                <input type="text" name="first-name" class="form-control @error('first-name') is-invalid @enderror" id="first-name" placeholder="Enter first name" value="{{ old('first-name') }}" autofocus required>
                                @error('first-name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name</label>
                                <input type="text" name="last-name" class="form-control @error('last-name') is-invalid @enderror" id="last-name" placeholder="Enter last name" value="{{ old('last-name') }}" required>
                                @error('last-name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input type="email" name="email" pattern="^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="mobile">Mobile</label>
                                <input type="tel" name="mobile" class="form-control @error('mobile') is-invalid @enderror" id="mobile" placeholder="Enter mobile" value="{{ old('mobile') }}" maxlength="12" minlength="10" required>
                                @error('mobile')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info mx-1">Create</button>
                            <a href="{{ route('admin.portfolio.index') }}" class="btn mx-1">Cancel</a>
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
