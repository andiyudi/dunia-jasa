@extends('layouts.template')

@section('content')
@php
    $title = 'Tender';
    $pretitle = 'Quotation';
@endphp

<h3>Join - <span id="selected-partner-name"></span></h3> <!-- Display selected partner's name -->
<div>

    <!-- Display errors -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Tender Information -->
        <div class="col-md-12 mb-4">
            <h5 class="bg-primary text-white p-2">Name : {{ $tender->name }}</h5>
            <div class="p-3 border">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Owner :</strong> {{ $tender->partner->first()->name }}</p>
                        <p><strong>Category :</strong> {{ $tender->category->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Location :</strong> {{ $tender->location }}</p>
                        <p><strong>Estimation :</strong> {{ $tender->estimation }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tender Documents -->
        <div class="col-md-12 mb-4">
            <h5 class="bg-info text-white p-2">Tender Documents</h5>
            <div class="p-3 border">
                @if($tender->documents->isEmpty())
                    <p>No documents available.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Document Type</th>
                                    <th>Document Name</th>
                                    <th>Note</th>
                                    <th>Download Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tender->documents as $document)
                                    <tr>
                                        <td>{{ $document->type->name }}</td> <!-- Display document type name -->
                                        <td>{{ $document->name }}</td> <!-- Document name -->
                                        <td>{{ $document->note ?? 'No notes available' }}</td> <!-- Document note -->
                                        <td>
                                            <a href="{{ $document->path }}" target="_blank">Download</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>


        <!-- Tender Items Table -->
        <div class="col-md-12">
            <h5 class="bg-secondary text-white p-2">Tender Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th>Specification</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Delivery</th>
                            <th>Quotation</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tender->items as $item)
                            <tr>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->specification }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit }}</td>
                                <td>{{ $item->delivery }}</td>
                                <td>
                                    <!-- Button to trigger modal -->
                                    <button type="button" class="btn btn-primary btn-sm submit-quotation-btn" data-bs-toggle="modal" data-bs-target="#quotationModal" data-item-id="{{ $item->id }}" disabled>
                                        Submit Quotation
                                    </button>
                                </td>
                                <td id="total-price-{{ $item->id }}">0</td> <!-- Display calculated total price -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- File Upload Section -->
        <div class="col-md-12 mt-4">
            <h5 class="bg-warning text-white p-2">Upload Quotation Document</h5>
            <div class="p-3 border">
                {{-- <form action="{{ route('tender.uploadDocument', $tender->id) }}" method="POST" enctype="multipart/form-data"> --}}
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Document Type</label>
                        <select class="form-select" name="type_id" id="document_type" required>
                            <option value="">Select Document Type</option>
                            {{-- @foreach($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>

                    {{-- <div class="mb-3">
                        <label for="document_name" class="form-label">Document Name</label>
                        <input type="text" class="form-control" id="document_name" name="name" required>
                    </div> --}}

                    <div class="mb-3">
                        <label for="file" class="form-label">File</label>
                        <input type="file" class="form-control" id="file" name="file" required>
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Upload Document</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Partner Selection Modal -->
<div class="modal fade" id="partnerSelectionModal" tabindex="-1" aria-labelledby="partnerSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="partnerSelectionModalLabel">Select Partner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="partnerSelectionForm">
                    @foreach($userPartners as $partner)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="selected_partner" id="partner{{ $partner->id }}" value="{{ $partner->id }}">
                            <label class="form-check-label" for="partner{{ $partner->id }}">
                                {{ $partner->name }}
                            </label>
                        </div>
                    @endforeach
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="selectPartnerButton">Select</button>
            </div>
        </div>
    </div>
</div>

<!-- Quotation Modal -->
<div class="modal fade" id="quotationModal" tabindex="-1" aria-labelledby="quotationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quotationModalLabel">Submit Quotation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quotationForm" action="{{ route('quotation.store') }}" method="POST">
                @csrf
                <input type="hidden" name="partner_id" id="selected-partner-id">
                <input type="hidden" name="item_id" id="item-id">
                <div class="modal-body">
                    <!-- Price input -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" name="price" required>
                    </div>

                    <!-- Delivery Time input -->
                    <div class="mb-3">
                        <label for="delivery_time" class="form-label">Delivery Time</label>
                        <input type="text" class="form-control" id="delivery_time" name="delivery_time" required>
                    </div>

                    <!-- Remark input -->
                    <div class="mb-3">
                        <label for="remark" class="form-label">Remark</label>
                        <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var partnerSelectionModal = new bootstrap.Modal(document.getElementById('partnerSelectionModal'));
        partnerSelectionModal.show();

        // Handle partner selection
        document.getElementById('selectPartnerButton').addEventListener('click', function() {
            var selectedPartner = document.querySelector('input[name="selected_partner"]:checked');
            if (selectedPartner) {
                document.getElementById('selected-partner-id').value = selectedPartner.value;
                document.getElementById('selected-partner-name').textContent = selectedPartner.nextElementSibling.textContent; // Update partner name display
                partnerSelectionModal.hide();

                // Enable all "Submit Quotation" buttons
                document.querySelectorAll('.submit-quotation-btn').forEach(function(button) {
                    button.disabled = false;
                });
            } else {
                // Show SweetAlert warning if no partner is selected
                Swal.fire({
                    icon: 'warning',
                    title: 'No Partner Selected',
                    text: 'Please select a partner before proceeding.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    var quotationModal = document.getElementById('quotationModal');
    quotationModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var itemId = button.getAttribute('data-item-id');
        document.getElementById('item-id').value = itemId;

        document.getElementById('price').addEventListener('input', function() {
            const quantity = @json($tender->items->pluck('quantity', 'id'));
            const price = this.value;
            const totalPrice = price * quantity[itemId];
            document.getElementById(`total-price-${itemId}`).textContent = totalPrice;
        });
    });
</script>
@endsection
