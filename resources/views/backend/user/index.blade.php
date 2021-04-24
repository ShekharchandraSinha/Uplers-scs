@extends('core.backend.app', ['pageTitle' => 'Users'])

@push('styles')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}" />
<link rel="stylesheet" href="{{ asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}" />
@endpush
@push('scripts')
<script src="{{ asset('backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script>
    $("#data-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url : "{{ route('admin.user.search-sort-paginate') }}",
            type: "POST",
            data :{ _token: "{{csrf_token()}}"}
        },
        order: [[ 1, "asc" ]],
        columnDefs: [
            { targets: 0, data: 'index', name: 'index', searchable:false, orderable: false},
            { targets: 1, data: 'name', name: 'name'},
            { targets: 2, data: 'email', name: 'email'},
            { targets: 3, data: 'mobile', name: 'mobile'},
            { 
                targets: 4, 
                data: 'active', 
                name: 'active',
                render: function (data, type, row, meta){
                    if(row.active){
                        return `<span class="badge badge-success py-2 px-3">Active</span>`
                    } else {
                        return `<span class="badge badge-danger py-2 px-3">De-activated</span>`
                    }
                }
            },
            { 
                targets: 5, 
                data: 'action', 
                name: 'action', 
                searchable:false, 
                orderable: false,
                render: function (data, type, row, meta){
                    let itemEditUrl = "{{ route('admin.user.edit', '::itemId::') }}".replace('::itemId::', row.id)
                    return `<a href="${itemEditUrl}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>`;
                }
            },
        ],
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
                        Users List
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="data-table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Active</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
@endsection