@extends('layouts.template')
@section('content')
@php
$title    = 'Tender';
$pretitle = 'Data';
@endphp
<div class="table-responsive">
    <div class="col-md-12 mb-3">
        <table class="table table-bordered table-striped table-hover display responsive no-wrap" id="tender-table" width="100%">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>No</th>
                    <th>Company</th>
                    <th>Category</th>
                    <th>Tender</th>
                    <th>Location</th>
                    <th>Estimation</th>
                    <th>Document</th>
                    <th>Status</th>
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
<!-- Partner Selection Modal -->
<div class="modal fade" id="partnerSelectionModal" tabindex="-1" aria-labelledby="partnerSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="partnerSelectionModalLabel">Select a Partner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="partnerSelectionForm" action="{{ route('quotation.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tender_id" id="modalTenderId" value="">
                    <div class="form-group">
                        <label for="partner">Choose a Partner</label>
                        <select name="partner_id" id="partner" class="form-control" required>
                            <!-- Partner options will be dynamically loaded here -->
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Join Tender</button>
                </form>
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
            ajax: "{{ route('quotation.index') }}",
            columns: [
                {data: 'action', name: 'action', orderable: false, searchable: false},
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'partner', name: 'partner'},
                {data: 'category', name: 'category'},
                {data: 'name', name: 'name'},
                {data: 'location', name: 'location'},
                {data: 'estimation', name: 'estimation'},
                {data: 'document', name: 'document'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
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
