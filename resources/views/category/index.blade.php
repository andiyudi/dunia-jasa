@extends('layouts.template')
@section('content')
@php
$title    = 'Category';
$pretitle = 'Master';
@endphp
    <h3>List</h3>
    <div class="mb-3">
        <button class="btn btn-primary float-end mb-3" id="create-new-category">Create Category</button>
    </div>
    <table class="table table-bordered" id="category-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="category-modal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Create Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="category-form">
                        @csrf
                        <input type="hidden" id="category_id" name="category_id">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    $(function() {
        let table = $('#category-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('category.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Open modal for creating new category
        $('#create-new-category').click(function() {
            $('#category-modal').modal('show');
            $('#categoryModalLabel').text('Create Category');
            $('#category-form')[0].reset();
            $('#category_id').val('');
            $('#save-btn').text('Create');
        });

        // Save or Update Category
        $('#category-form').submit(function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            let category_id = $('#category_id').val();
            let url = category_id ? "{{ route('category.index') }}" + '/' + category_id : "{{ route('category.store') }}";
            let method = category_id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(response) {
                    $('#category-modal').modal('hide');
                    table.ajax.reload();
                    alert(response.success); // Tampilkan pesan sukses
                },
                error: function(xhr) {
                    alert('Error saving category!');
                }
            });
        });

        // Edit Category
        $('body').on('click', '.edit-category', function() {
            let category_id = $(this).data('id');
            $.get("{{ route('category.index') }}" + '/' + category_id + '/edit', function (data) {
                $('#categoryModalLabel').text('Edit Category');
                $('#save-btn').text('Update');
                $('#category-modal').modal('show');
                $('#category_id').val(data.id);
                $('#name').val(data.name);
            });
        });

        // Delete Category
        $('body').on('click', '.delete-category', function() {
            let category_id = $(this).data('id');
            if (confirm('Are you sure want to delete this category?')) {
                $.ajax({
                    data: {
                        "_token": "{{ csrf_token() }}", // Tambahkan token CSRF
                    },
                    url: "{{ route('category.index') }}" + '/' + category_id,
                    type: 'DELETE',
                    success: function(response) {
                        table.ajax.reload();
                        alert('Category deleted successfully!');
                    },
                    error: function(xhr) {
                        alert('Error deleting category!');
                    }
                });
            }
        });
    });
</script>
@endsection
