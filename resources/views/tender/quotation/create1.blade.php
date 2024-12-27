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
            <h5 class="bg-info text-white p-2">Name : {{ $tender->name }}</h5>
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
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Payment :</strong> {{ $tender->payment }}</p>
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

        <!-- Tender Items Section with Submit Button -->
        <div class="col-md-12 mb-4">
            <h5 class="bg-info text-white p-2">Tender Items</h5>
        </div>

        <!-- Tender Items Table -->
        <div class="col-md-12 mb-4">
            <div class="table-responsive">
                <table class="table table-bordered" id="tenderItemsTable">
                    <thead class="table-light">
                        <tr>
                            <th colspan="5" style="text-align: center;">Tender Item Detail</th>
                            <th colspan="5" style="text-align: center;">Quotation Item Details</th>
                        </tr>
                        <tr>
                            <!-- Tender Details -->
                            <th>Description</th>
                            <th>Specification</th>
                            <th>Delivery</th>
                            <th>Quantity</th>
                            <th>Unit</th>

                            <!-- Quotation Details -->
                            <th>Price</th>
                            <th>Delivery Time</th>
                            <th>Remark</th>
                            <th>Terms of Payment</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (json_decode($tender->items, true) as $item)
                            <tr>
                                <td>{{ $item['description'] }}</td>
                                <td>{{ $item['specification'] }}</td>
                                <td>{{ $item['delivery'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>{{ $item['unit'] }}</td>
                                <td><input type="text" class="form-control" name="price[]"></td>
                                <td><input type="text" class="form-control" name="delivery_time[]"></td>
                                <td><input type="text" class="form-control" name="remark[]"></td>
                                <td><input type="text" class="form-control" name="terms_of_payment[]"></td>
                                <td><span id="total-price-display">0</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- File Upload Section -->
        <div class="col-md-12 mt-4">
            <h5 class="bg-info text-white p-2">Upload Quotation Document</h5>
            <div class="p-3 border">
                <form action="{{ route('quotation.store') }}" method="POST" enctype="multipart/form-data" id="uploadQuotationForm">
                    @csrf
                    <div id="tenderItemsInputs"></div>
                    <div class="mb-3">
                        <label for="file" class="form-label">File</label>
                        <input type="file" class="form-control" id="file" name="file">
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                    </div>

                    <!-- Tambahkan d-flex dan justify-content-end pada wrapper tombol -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Partner Selection Modal -->
<div class="modal fade show d-block" id="partnerSelectionModal" tabindex="-1" aria-labelledby="partnerSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="partnerSelectionModalLabel">Select Partner</h5>
                <button type="button" class="btn-close" onclick="hideModal()" aria-label="Close"></button>
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
                <button type="button" class="btn btn-secondary" onclick="hideModal()">Close</button>
                <button type="button" class="btn btn-primary" id="selectPartnerButton" onclick="selectPartner()">Select</button>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedPartnerId = null;

    document.getElementById('uploadQuotationForm').addEventListener('submit', function(event) {
        const tenderItemsInputs = document.getElementById('tenderItemsInputs');
        tenderItemsInputs.innerHTML = ''; // Bersihkan input sebelumnya

        const rows = document.querySelectorAll('#tenderItemsTable tbody tr');
        rows.forEach(row => {
            const itemData = {
                description: row.children[0].textContent.trim(),
                specification: row.children[1].textContent.trim(),
                delivery: row.children[2].textContent.trim(),
                quantity: row.children[3].textContent.trim(),
                unit: row.children[4].textContent.trim(),
                price: row.children[5].querySelector('input').value.trim(),
                delivery_time: row.children[6].querySelector('input').value.trim(),
                remark: row.children[7].querySelector('input').value.trim(),
                terms_price: row.children[8].querySelector('input').value.trim(),
            };

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'tender_items[]';
            hiddenInput.value = JSON.stringify(itemData);
            tenderItemsInputs.appendChild(hiddenInput);
        });
    });


    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('selectPartnerButton').addEventListener('click', selectPartner);
    });

function selectPartner() {
    var selectedPartner = document.querySelector('input[name="selected_partner"]:checked');
    if (selectedPartner) {
        selectedPartnerId = selectedPartner.value;
        document.getElementById('selected-partner-name').textContent = selectedPartner.nextElementSibling.textContent;
        hideModal();
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'No Partner Selected',
            text: 'Please select a partner before proceeding.',
            confirmButtonText: 'OK'
        });
    }
}

function hideModal() {
    document.getElementById('partnerSelectionModal').classList.remove('show', 'd-block');
}
</script>

@endsection
