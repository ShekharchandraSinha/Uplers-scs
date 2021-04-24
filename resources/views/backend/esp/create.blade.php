@extends('core.backend.app', ['pageTitle' => 'ESPs'])

@push('styles')
@endpush

@push('scripts')
<script src="{{ asset('backend/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
    bsCustomFileInput.init();
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
                        ESP Create
                    </h3>
                </div>
                <form role="form" class="form-horizontal" method="POST" action="{{ route('admin.esp.store') }}" enctype="multipart/form-data">
                    <!-- /.card-header -->
                    <div class="card-body">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" id="title" placeholder="Enter title" value="{{ old('title') }}" autofocus required>
                                @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="icon">Icon (image will be resized to 200 x 94 pixels)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="icon" class="custom-file-input @error('icon') is-invalid @enderror" id="icon" required>
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                </div>
                                @error('icon')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="active" class="custom-control-input" id="account_active" value="1" checked>
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
                        <button type="submit" class="btn btn-info mx-1">Create</button>
                        <a href="{{ route('admin.esp.index') }}" class="btn mx-1">Cancel</a>
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