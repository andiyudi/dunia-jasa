@extends('layouts.template')
@section('content')
@php
$title    = 'Vendor';
$pretitle = 'Data';
@endphp
    <h3>List</h3>
    <div class="table-responsive">
        <div class="mb-3">
            <a href="{{ route('partner.create') }}" class="btn btn-primary float-end mb-3">Create Vendor</a>
        </div>
        <table class="table table-responsive table-bordered table-striped table-hover" id="vendor-table" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Brand</th>
                    <th>NPWP</th>
                    <th>PIC</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <script>
        $(function() {
            let table = $('#vendor-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('partner.index') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'categories', name: 'categories' },
                    { data: 'description', name: 'description' },
                    { data: 'brand', name: 'brand' },
                    { data: 'npwp', name: 'npwp' },
                    { data: 'pic', name: 'pic' },
                    { data: 'email', name: 'email' },
                    { data: 'contact', name: 'contact' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

        });
    </script>
@endsection
