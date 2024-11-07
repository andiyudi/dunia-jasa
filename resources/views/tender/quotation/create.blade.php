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

        <!-- Tender Items Section with Submit Button -->
        <div class="col-md-12 mb-4">
            <h5 class="bg-secondary text-white p-2">Tender Items</h5>
        </div>
        <div class="col-md-12 mb-4">
            <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#submitQuotationModal" disabled id="submitQuotationItemButton">
                Submit Quotation Item
            </button>
        </div>

        <!-- Tender Items Table -->
        <div class="col-md-12 mb-4">
            <div class="table-responsive">
                <table class="table table-bordered" id="tenderItemsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Tender Items</th>
                            <th>Price</th>
                            <th>Delivery Time</th>
                            <th>Remark</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be added dynamically here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- File Upload Section -->
        <div class="col-md-12 mt-4">
            <h5 class="bg-warning text-white p-2">Upload Quotation Document</h5>
            <div class="p-3 border">
                <form action="#" method="POST" enctype="multipart/form-data" id="uploadQuotationForm">
                    @csrf
                    <div id="tenderItemsInputs"></div>
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

<!-- Submit Quotation Modal -->
<div class="modal fade" id="submitQuotationModal" tabindex="-1" aria-labelledby="submitQuotationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submitQuotationModalLabel">Submit Quotation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quotationForm" action="#" method="POST" onsubmit="addItemToTable(event)">
                @csrf
                <input type="hidden" name="partner_id" id="selected-partner-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="item_id" class="form-label">Tender Item</label>
                        <select class="form-select" id="item_id" name="item_id" required>
                            <option value="" selected>Select an item</option>
                            @foreach($tender->items as $item)
                                <option
                                    value="{{ $item->id }}"
                                    data-quantity="{{ $item->quantity }}"
                                    data-unit="{{ $item->unit }}"
                                    data-delivery="{{ $item->delivery }}"
                                    data-specification="{{ $item->specification }}"
                                >
                                    {{ $item->description }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="itemDetails" class="mb-3" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Specification :</strong> <span id="item-specification"></span></p>
                                <p><strong>Delivery :</strong> <span id="item-delivery"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Quantity :</strong> <span id="item-quantity"></span></p>
                                <p><strong>Unit :</strong> <span id="item-unit"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="delivery_time" class="form-label">Delivery Time</label>
                        <input type="text" class="form-control" id="delivery_time" name="delivery_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="remark" class="form-label">Remark</label>
                        <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Total Price</label>
                        <p id="total-price-display">0</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('selectPartnerButton').addEventListener('click', selectPartner);
        document.getElementById('price').addEventListener('input', calculateTotalPrice);
        document.getElementById('item_id').addEventListener('change', function() {
            showItemDetails();
            calculateTotalPrice();
        });
    });

    function selectPartner() {
        var selectedPartner = document.querySelector('input[name="selected_partner"]:checked');
        if (selectedPartner) {
            document.getElementById('selected-partner-id').value = selectedPartner.value;
            document.getElementById('selected-partner-name').textContent = selectedPartner.nextElementSibling.textContent;
            document.getElementById('submitQuotationItemButton').disabled = false;
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

    function addItemToTable(event) {
        event.preventDefault();

        const itemSelect = document.getElementById('item_id');
        const priceInput = document.getElementById('price').value;
        const deliveryTimeInput = document.getElementById('delivery_time').value;
        const remarkInput = document.getElementById('remark').value;
        const totalPriceDisplay = document.getElementById('total-price-display').textContent;

        const selectedItemText = itemSelect.options[itemSelect.selectedIndex].text;
        const selectedItemValue = itemSelect.value;

        const tbody = document.querySelector('#tenderItemsTable tbody');

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${selectedItemText}</td>
            <td>${priceInput}</td>
            <td>${deliveryTimeInput}</td>
            <td>${remarkInput}</td>
            <td>${totalPriceDisplay}</td>
            <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Delete</button></td>
        `;

        tbody.appendChild(newRow);

        const inputsContainer = document.getElementById('tenderItemsInputs');
        inputsContainer.innerHTML += `
            <input type="hidden" name="items[${selectedItemValue}][price]" value="${priceInput}">
            <input type="hidden" name="items[${selectedItemValue}][delivery_time]" value="${deliveryTimeInput}">
            <input type="hidden" name="items[${selectedItemValue}][remark]" value="${remarkInput}">
            <input type="hidden" name="items[${selectedItemValue}][total_price]" value="${totalPriceDisplay}">
        `;

        document.getElementById('quotationForm').reset();
        $('#submitQuotationModal').modal('hide');
    }

    function calculateTotalPrice() {
        const itemSelect = document.getElementById('item_id');
        const quantity = itemSelect.options[itemSelect.selectedIndex].dataset.quantity || 0;
        const price = parseFloat(document.getElementById('price').value) || 0;
        document.getElementById('total-price-display').textContent = (quantity * price).toLocaleString();
    }

    function showItemDetails() {
        const itemSelect = document.getElementById('item_id');
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];

        if(selectedOption.value) {
            document.getElementById('item-quantity').textContent = selectedOption.getAttribute('data-quantity') || 'N/A';
            document.getElementById('item-unit').textContent = selectedOption.getAttribute('data-unit') || 'N/A';
            document.getElementById('item-delivery').textContent = selectedOption.getAttribute('data-delivery') || 'N/A';
            document.getElementById('item-specification').textContent = selectedOption.getAttribute('data-specification') || 'N/A';
            document.getElementById('itemDetails').style.display = 'block';
        } else {
            document.getElementById('itemDetails').style.display = 'none';
        }
    }
    function resetQuotationForm() {
        // Reset seluruh form
        document.getElementById('quotationForm').reset();

        // Reset elemen p yang menampilkan detail item
        document.getElementById('item-specification').textContent = '';
        document.getElementById('item-delivery').textContent = '';
        document.getElementById('item-quantity').textContent = '';
        document.getElementById('item-unit').textContent = '';
        document.getElementById('total-price-display').textContent = '0';

        // Sembunyikan itemDetails jika ditampilkan sebelumnya
        document.getElementById('itemDetails').style.display = 'none';

        // Tutup modal
        $('#submitQuotationModal').modal('hide');
    }

    document.getElementById('quotationForm').addEventListener('submit', function(event) {
        event.preventDefault();

        // Reset form dan elemen p setelah item ditambahkan
        resetQuotationForm();
    });
    
    function removeRow(button) {
        const row = button.closest('tr');
        row.remove();
    }
</script>

@endsection
