@extends('core.backend.app', ['pageTitle' => 'Portfolio'])

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
            url : "{{ route('admin.portfolio.search-sort-paginate') }}",
            type: "POST",
            data :{ _token: "{{csrf_token()}}"}
        },
        order: [[ 1, "desc" ]],
        columnDefs: [
            { targets: 0, data: 'index', name: 'index', searchable:false, orderable: false},
            { targets: 1, data: 'name', name: 'name'},
            { 
                targets: 2, 
                data: 'encoded_id', 
                name: 'encoded_id',
                searchable: false, 
                orderable: false,
                render: function (data, type, row, meta){
                    let portfolioUrl = "{{ route('public.portfolio', ['portfolioSlug'=> '::SLUG::']) }}".replace('::SLUG::', row.slug);
                    
                    if(row.confirmed && row.active){
                        return `<a href="${portfolioUrl}" class="btn btn-primary" target="_blank"><i class="fas fa-eye"></i> View Portfolio</a>`;
                    } else {
                        if(!row.confirmed){
                            return `<button type="button" class="btn btn-primary" title="Please confirm profile" disabled><i class="fas fa-eye"></i> View Portfolio</button>`;
                        } else if(!row.active){
                        return `<button type="button" class="btn btn-primary" title="Please activate profile" disabled><i class="fas fa-eye"></i> View Portfolio</button>`;
                        }
                    }
                }
            },
            { targets: 3, data: 'designation', name: 'designation'},
            { targets: 4, data: 'skill_level', name: 'skill_level'},
            { 
                targets: 5, 
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
                targets: 6, 
                data: 'action', 
                name: 'action', 
                searchable:false, 
                orderable: false,
                render: function (data, type, row, meta){
                    let itemEditUrl = "{{ route('admin.portfolio.edit', '::itemId::') }}".replace('::itemId::', row.id)
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
                        Portfolio List
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="data-table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Url</th>
                                <th>Designation</th>
                                <th>Skill Level</th>
                                <th>Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Url</th>
                                <th>Designation</th>
                                <th>Skill Level</th>
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