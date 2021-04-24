@extends('core.backend.app', ['pageTitle' => 'PMS'])

@push('styles')
@endpush

@push('scripts')
<script>
    $("#mobile").on("keypress", function (event) {
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
                        PMS Update
                    </h3>
                </div>
                <form role="form" class="form-horizontal" method="POST" action="{{ route('admin.pms.update', $item->id) }}" enctype="multipart/form-data">
                    <!-- /.card-header -->
                    <div class="card-body">
                        @csrf
                        @method('PATCH')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" id="title" placeholder="Enter title" value="{{ old('title', $item->title) }}" autofocus required>
                                @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Old icon</h3>
                                    </div>
                                    <div class="card-body">
                                        <a href="{{ asset('img/pms').'/'.$item->icon }}">
                                            <img src="{{ asset('img/pms').'/'.$item->icon }}" alt="" class="img img-responsive">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="icon">New Icon (image will be resized to 200 x 94 pixels)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="icon" class="custom-file-input @error('icon') is-invalid @enderror" id="icon">
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
                                    <input type="checkbox" name="active" class="custom-control-input" id="account_active" @if($item->active) checked @endif>
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
                        <a href="{{ route('admin.pms.index') }}" class="btn mx-1">Cancel</a>
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