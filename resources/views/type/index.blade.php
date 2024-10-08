@extends('layouts.template')
@section('content')
@php
$title    = 'Type';
$pretitle = 'Master';
@endphp
    <div class="row mb-3">
        <div class="table-responsive">
            <div class="col-md-12 mb-3">
                <button class="btn btn-primary float-end mb-3" id="create-new-type">Create Type</button>
            </div>
        </div>
        <table class="table table-responsive table-bordered table-striped table-hover" id="type-table" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="type-modal" tabindex="-1" aria-labelledby="typeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="typeModalLabel">Create Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="type-form">
                        @csrf
                        <input type="hidden" id="type_id" name="type_id">
                        <div class="mb-3">
                            <label for="name" class="form-label">Type Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success" id="save-btn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            let table = $('#type-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('type.data') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Open modal for creating new type
            $('#create-new-type').click(function() {
                $('#type-modal').modal('show');
                $('#typeModalLabel').text('Create Type');
                $('#type-form')[0].reset();
                $('#type_id').val('');
                $('#save-btn').text('Submit');
            });

            // Save or Update type
            $('#type-form').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                let type_id = $('#type_id').val();
                let url = type_id ? "{{ route('type.index') }}" + '/' + type_id : "{{ route('type.store') }}";
                let method = type_id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        $('#type-modal').modal('hide');
                        table.ajax.reload();

                        // Success alert using SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.success
                        });
                    },
                    error: function(xhr) {
                        // Error alert using SweetAlert2
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving type!'
                        });
                    }
                });
            });

            // Edit type
            $('body').on('click', '.edit-type', function() {
                let type_id = $(this).data('id');
                $.get("{{ route('type.index') }}" + '/' + type_id + '/edit', function (data) {
                    $('#typeModalLabel').text('Edit Type');
                    $('#save-btn').text('Update');
                    $('#type-modal').modal('show');
                    $('#type_id').val(data.id);
                    $('#name').val(data.name);
                });
            });

            // Delete type
            $('body').on('click', '.delete-type', function() {
                let type_id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            data: {
                                "_token": "{{ csrf_token() }}", // Add CSRF token
                            },
                            url: "{{ route('type.index') }}" + '/' + type_id,
                            type: 'DELETE',
                            success: function(response) {
                                table.ajax.reload();

                                // Success alert using SweetAlert2
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Type deleted successfully!'
                                });
                            },
                            error: function(xhr) {
                                // Error alert using SweetAlert2
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error deleting type!'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>

@endsection
