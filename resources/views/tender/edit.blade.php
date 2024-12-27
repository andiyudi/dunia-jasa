@extends('layouts.template')
@section('content')
@php
$title    = 'Tender';
$pretitle = 'Data';
@endphp
<h3>Edit</h3>
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

    <form id="tender-form" action="{{ route('tender.update', encrypt($tender->id)) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <!-- Partner -->
                <div class="form-group">
                    <label for="partner_id" class="form-label">Select Partner</label>
                    <select name="partner_id" id="partner_id" class="form-control" @if($tender->has_quotation) readonly disabled @endif>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" {{ $selectedPartnerId == $partner->id ? 'selected' : '' }}>
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Category -->
                <div class="form-group">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-control" @if($tender->has_quotation) readonly disabled @endif>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $tender->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Tender Name -->
            <div class="form-group">
                <label for="name" class="form-label">Tender Name</label>
                <textarea name="name" id="name" class="form-control" @if($tender->has_quotation) readonly @endif>{{ old('name', $tender->name) }}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <!-- Location -->
                <div class="form-group">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $tender->location) }}" @if($tender->has_quotation) readonly @endif>
                </div>
                <!-- Estimation -->
                <div class="form-group">
                    <label for="estimation" class="form-label">Estimation</label>
                    <input type="text" name="estimation" id="estimation" class="form-control" value="{{ old('estimation', $tender->estimation) }}" @if($tender->has_quotation) readonly @endif>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Payment -->
                <div class="form-group">
                    <label for="payment" class="form-label">Payment</label>
                    <textarea id="payment" name="payment" class="form-control mb-3" placeholder="Input your payment description here" rows="5">{{ old('payment', $tender->payment) }}@if($tender->has_quotation) readonly @endif</textarea>
                </div>
            </div>
        </div>
        <!-- File Upload -->
        <div id="type-upload">
            @foreach ($types as $type)
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="upload_{{ 'type_' . $type->id }}" class="form-label">{{ $type->name }}</label>
                            <input type="file" id="upload_{{ $type->id }}" name="types[{{ $type->id }}]" class="form-control mb-3" placeholder="Upload {{ $type->name }}" accept=".pdf" @if($tender->has_quotation) disabled @endif>
                            <small class="form-text text-muted">PDF Max 2MB</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Note -->
                        <div class="form-group">
                            <label for="note_{{ $type->id }}" class="form-label">Note for {{ $type->name }}</label>
                            <textarea id="note_{{ $type->id }}" name="notes[{{ $type->id }}]" class="form-control mb-3" placeholder="Input your {{ $type->name }} description here" rows="3" @if($tender->has_quotation) readonly @endif>{{ old("notes.{$type->id}", $tender->notes[$type->id] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col form-group">
                <label for="items" class="form-label">Tender Items</label>
                <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#itemsModal" @if($tender->has_quotation) disabled @endif>Add Items</button>
            </div>
        </div>
        <div class="row">
            <!-- Tender Items Table -->
            <div class="col-md-12">
                <table class="table table-bordered" id="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Specification</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Delivery</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tender->items as $item)
                            <tr>
                                <td>
                                    {{ $item->description }}
                                    <input type="hidden" name="items[description][]" value="{{ $item->description }}">
                                </td>
                                <td>
                                    {{ $item->specification }}
                                    <input type="hidden" name="items[specification][]" value="{{ $item->specification }}">
                                </td>
                                <td>
                                    {{ $item->quantity }}
                                    <input type="hidden" name="items[quantity][]" value="{{ $item->quantity }}">
                                </td>
                                <td>
                                    {{ $item->unit }}
                                    <input type="hidden" name="items[unit][]" value="{{ $item->unit }}">
                                </td>
                                <td>
                                    {{ $item->delivery }}
                                    <input type="hidden" name="items[delivery][]" value="{{ $item->delivery }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger remove-item" @if($tender->has_quotation) disabled @endif>Remove</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('tender.index') }}" type="button" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-success" @if($tender->has_quotation) disabled @endif>Update</button>
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
                        <label for="modal-unit" class="form-label">Unit</label>
                        <input type="text" id="modal-unit" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="modal-delivery" class="form-label">Delivery</label>
                        <input type="text" id="modal-delivery" class="form-control">
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
            var unit = $('#modal-unit').val();
            var delivery = $('#modal-delivery').val();

            if (description && specification && quantity && unit && delivery) {
                // Tambahkan row baru ke tabel tender items
                var newRow = `
                    <tr>
                        <td>${description}<input type="hidden" name="items[description][]" value="${description}"></td>
                        <td>${specification}<input type="hidden" name="items[specification][]" value="${specification}"></td>
                        <td>${quantity}<input type="hidden" name="items[quantity][]" value="${quantity}"></td>
                        <td>${unit}<input type="hidden" name="items[unit][]" value="${unit}"></td>
                        <td>${delivery}<input type="hidden" name="items[delivery][]" value="${delivery}"></td>
                        <td><button type="button" class="btn btn-danger remove-item">Remove</button></td>
                    </tr>
                `;
                $('#items-table tbody').append(newRow);

                // Bersihkan input modal
                $('#modal-description').val('');
                $('#modal-specification').val('');
                $('#modal-quantity').val('');
                $('#modal-unit').val('');
                $('#modal-delivery').val('');

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
            var row = $(this).closest('tr');
            Swal.fire({
                title: "Are you sure?",
                text: "This item will be removed.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, remove it!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    row.remove();
                }
            });
        });

    });
</script>
@endsection
