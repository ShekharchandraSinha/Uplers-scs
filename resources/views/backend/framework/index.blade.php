@extends('core.backend.app', ['pageTitle' => 'Frameworks'])

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
            url : "{{ route('admin.framework.search-sort-paginate') }}",
            type: "POST",
            data :{ _token: "{{csrf_token()}}"}
        },
        order: [[ 1, "desc" ]],
        columnDefs: [
            { targets: 0, data: 'index', name: 'index', searchable:false, orderable: false},
            { 
                targets: 1, 
                data: 'icon', 
                name: 'icon', 
                searchable:false,
                orderable: false,
                render: function (data, type, row, meta){
                    let imagePath = "{{ asset('img/framework') }}"
                    return `<img class="img img-responsive img-size-64" src="${imagePath}/${row.icon}" alt="${row.title}" style="max-height:100px"/>`
                }
            },
            { targets: 2, data: 'title', name: 'title'},
            { 
                targets: 3, 
                data: 'active', 
                name: 'active',
                render: function (data, type, row, meta){
                    if(row.active){
                        return `<span class="px-3 py-2 badge badge-success">Active</span>`
                    } else {
                        return `<span class="px-3 py-2 badge badge-danger">De-activated</span>`
                    }
                }
            },
            { 
                targets: 4, 
                data: 'action', 
                name: 'action', 
                searchable:false, 
                orderable: false,
                render: function (data, type, row, meta){
                    let itemEditUrl = "{{ route('admin.framework.edit', '::itemId::') }}".replace('::itemId::', row.id)
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
                        Frameworks List
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="data-table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Icon</th>
                                <th>Title</th>
                                <th>Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Icon</th>
                                <th>Title</th>
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