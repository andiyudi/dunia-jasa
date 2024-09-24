@extends('layouts.template')
@section('content')
@php
$title    = 'Vendor';
$pretitle = 'Data';
@endphp
    <h3>List</h3>
    <div class="table-responsive">
        <!-- Filter Form -->
        <div class="row mb-3">
            <div class="col-md-4 mb-3">
                <select id="category-filter" class="form-select">
                    <option value="">All Categories</option>
                    <!-- Iterate over categories -->
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <input type="text" id="brand-filter" class="form-control" placeholder="Search by Brand">
            </div>
            <div class="col-md-4 mb-3">
                <div class="btn-group mb-3" role="group" aria-label="Basic example">
                    <button class="btn btn-light" id="filter-btn">Search</button>
                    <button class="btn btn-dark" id="reset-btn">Reset</button>
                    <a href="{{ route('partner.create') }}" class="btn btn-primary">Create Vendor</a>
                </div>
            </div>
        </div>
        <table class="table .table-responsive{-sm|-md|-lg|-xl} table-bordered table-striped table-hover" id="vendor-table" width="100%">
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
                ajax: {
                    url: '{{ route('partner.index') }}',
                    data: function (d) {
                        d.category = $('#category-filter').val();
                        d.brand = $('#brand-filter').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'categories', name: 'categories', orderable: false, searchable: false },
                    { data: 'description', name: 'description', orderable: false },
                    { data: 'brand', name: 'brand', orderable: false, searchable: false },
                    { data: 'npwp', name: 'npwp', orderable: false, searchable: false },
                    { data: 'pic', name: 'pic', orderable: false, searchable: false },
                    { data: 'email', name: 'email', orderable: false, searchable: false },
                    { data: 'contact', name: 'contact', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
            $('#filter-btn').click(function() {
                table.draw();
            });

            $('#reset-btn').click(function() {
                $('#category-filter').val('');
                $('#brand-filter').val('');
                table.draw();
            });
        });
    </script>
@endsection
