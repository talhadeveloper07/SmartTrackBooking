@extends('organization.layouts.app')
@section('organization_content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Business Accounts</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Business Accounts</a></li>
                </ol>
            </div>
        </div>
        @if(session('success'))
            <script>
                toastr.success("{{ session('success') }}");
            </script>
        @endif

        @if(session('error'))
            <script>
                toastr.error("{{ session('error') }}");
            </script>
        @endif

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">All Business Accounts</h4>
            </div>
            <div class="card-body">
                <table class="table" id="businessTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Business Type</th>
                            <th>Status</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

    <script>
        $(function () {

            $('#businessTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('org.business.data') }}",

                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'logo', name: 'logo', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'city', name: 'city' },
                    { data: 'business_type', name: 'business_type' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

        });
    </script>

@endsection