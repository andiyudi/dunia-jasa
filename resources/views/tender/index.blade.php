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
                    <th>Action</th>
                    <th>No</th>
                    <th>Status</th>
                    <th>Company</th>
                    <th>Category</th>
                    <th>Tender</th>
                    <th>Estimation</th>
                    <th>Document</th>
                    <th>Location</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="documentsModal" tabindex="-1" aria-labelledby="documentsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentsModalLabel">Tender Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Data dokumen dengan type_id akan dimuat di sini oleh JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
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
                {data: 'action', name: 'action', orderable: false, searchable: false},
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'partner', name: 'partner'},
                {data: 'category', name: 'category'},
                {data: 'name', name: 'name'},
                {data: 'estimation', name: 'estimation'},
                {data: 'document', name: 'document'},
                {data: 'location', name: 'location'},
            ]
        });
    });
    $(document).on('click', '.view-documents', function() {
        var url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                $('#documentsModal .modal-body').html(data);
                $('#documentsModal').modal('show');
            }
        });
    });
</script>
@endsection
