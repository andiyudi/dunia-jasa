@extends('layouts.template')
@section('content')
@php
$title    = 'Tender';
$pretitle = 'Data';
@endphp
<h3>Create</h3>
<div>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="tender-form" action="{{ route('tender.store') }}" method="POST">
    @csrf
        <div class="row">
            <div class="col-md-6">
                <!-- Partner (only for verified partners or admins) -->
                @auth
                    @if(Auth::user()->is_admin)
                        <!-- Admin can select any partner -->
                        <div class="form-group">
                            <label for="partner_id" class="form-label">Select Partner</label>
                            <select name="partner_id" id="partner_id" class="form-control" required>
                                @foreach($partners as $partner)
                                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($partners->isNotEmpty())
                        <!-- Verified partner can select from their list -->
                        <div class="form-group">
                            <label for="partner_id" class="form-label">Select Partner</label>
                            <select name="partner_id" id="partner_id" class="form-control" required>
                                @foreach($partners as $partner)
                                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <p class="text-muted">You do not have any verified partners. You cannot create a tender.</p>
                    @endif
                @endauth
            </div>
            <div class="col-md-6">
                <!-- Category -->
                <div class="form-group">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Tender Name -->
            <div class="form-group">
                <label for="name" class="form-label">Tender Name</label>
                <textarea type="text" name="name" id="name" class="form-control" required></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <!-- Location -->
                <div class="form-group">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" name="location" id="location" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Estimation -->
                <div class="form-group">
                    <label for="estimation" class="form-label">Estimation</label>
                    <input type="text" name="estimation" id="estimation" class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col form-group">
                <label for="items" class="form-label">Tender Items</label>
                <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#itemsModal">Add Items</button> <!-- Trigger Modal -->
            </div>
        </div>
        <div class="row">
            <!-- Tempat menampilkan data tender items -->
            <div class="col-md-12">
                <table class="table table-bordered" id="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Specification</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Satuan</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dinamis: data tender items akan muncul di sini -->
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Submit button -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('tender.index') }}" type="button" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-success">Save</button>
        </div>
    </form>
</div>

<!-- Modal untuk input tender items -->
<div class="modal fade" id="itemsModal" tabindex="-1" aria-labelledby="itemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemsModalLabel">Add Tender Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="modal-description" class="form-label">Description</label>
                        <textarea name="modal-description" id="modal-description" class="form-control"></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="modal-specification" class="form-label">Specification</label>
                        <textarea name="modal-specification" id="modal-specification" class="form-control"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="modal-quantity" class="form-label">Quantity</label>
                        <input type="number" id="modal-quantity" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="modal-satuan" class="form-label">Satuan</label>
                        <input type="text" id="modal-satuan" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="modal-unit" class="form-label">Unit</label>
                        <input type="text" id="modal-unit" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-item">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#save-item').on('click', function () {
            var description = $('#modal-description').val();
            var specification = $('#modal-specification').val();
            var quantity = $('#modal-quantity').val();
            var satuan = $('#modal-satuan').val();
            var unit = $('#modal-unit').val();

            if (description && specification && quantity && satuan && unit) {
                // Tambahkan row baru ke tabel tender items
                var newRow = `
                    <tr>
                        <td>${description}<input type="hidden" name="items[description][]" value="${description}"></td>
                        <td>${specification}<input type="hidden" name="items[specification][]" value="${specification}"></td>
                        <td>${quantity}<input type="hidden" name="items[quantity][]" value="${quantity}"></td>
                        <td>${unit}<input type="hidden" name="items[unit][]" value="${unit}"></td>
                        <td>${satuan}<input type="hidden" name="items[satuan][]" value="${satuan}"></td>
                        <td><button type="button" class="btn btn-danger remove-item">Remove</button></td>
                    </tr>
                `;
                $('#items-table tbody').append(newRow);

                // Bersihkan input modal
                $('#modal-description').val('');
                $('#modal-specification').val('');
                $('#modal-quantity').val('');
                $('#modal-satuan').val('');
                $('#modal-unit').val('');

                // Tutup modal
                $('#itemsModal').modal('hide');
            } else {
                Swal.fire({
                title: "Error",
                text: "Please fill all fields",
                icon: "error",
                button: "OK",
                });
            }
        });

        // Remove item dari tabel
        $('#items-table').on('click', '.remove-item', function () {
            $(this).closest('tr').remove();
        });
    });
</script>
@endsection