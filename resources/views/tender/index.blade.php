@extends('layouts.template')
@section('content')
@php
$title    = 'Tender';
$pretitle = 'Data';
@endphp
<!-- Filter Form -->
<div class="row mb-3">
    <div class="col-md-4 mb-3">
    </div>
    <div class="col-md-4 mb-3">
    </div>
    <div class="col-md-4 mb-3">
        <div class="btn-group mb-3 float-end" role="group" aria-label="Basic example">
            @auth
                @if(Auth::user()->is_admin || Auth::user()->partners->contains('is_verified', true))
                    <a href="{{ route('tender.create') }}" class="btn btn-primary">Create Tender</a>
                @else
                    <p class="text-muted">You must have at least one verified vendor to create a tender.</p>
                @endif
            @endauth
        </div>
    </div>
</div>
<div class="table-responsive">
    <div class="col-md-12 mb-3">
        <table class="table table-bordered table-striped table-hover display responsive no-wrap" id="tender-table" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Company</th>
                    <th>Category</th>
                    <th>Tender</th>
                    <th>Location</th>
                    <th>Estimation</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $(function () {
        var table = $('#tender-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            scrollX: true,
            ajax: "{{ route('tender.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'partner', name: 'partner'},
                {data: 'category', name: 'category'},
                {data: 'name', name: 'name'},
                {data: 'location', name: 'location'},
                {data: 'estimation', name: 'estimation'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    });
</script>
@endsection
