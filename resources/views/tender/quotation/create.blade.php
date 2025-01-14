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
                            <th colspan="5" style="text-align: center;">Tender Item Detail</th>
                            <th colspan="4" style="text-align: center;">Quotation Item Details</th>
                            <th rowspan="2">Action</th> <!-- Action akan berada di sebelah kanan tanpa subheader -->
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
                            <th>Total Price</th>
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
            <h5 class="bg-info text-white p-2">Terms of Payment & Upload Quotation Document</h5>
            <div class="p-3 border">
                <form action="{{ route('quotation.store') }}" method="POST" enctype="multipart/form-data" id="uploadQuotationForm">
                    @csrf
                    <div id="tenderItemsInputs"></div>
                    <input type="hidden" name="tender_id" value="{{ $tender->id }}">
                    <div class="mb-3">
                        <label for="terms_of_payment" class="form-label">Terms of Payment:</label>
                        <textarea name="terms_of_payment" id="terms_of_payment" rows="3" class="form-control"></textarea>
                    </div>
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
                    <button type="button" class="btn btn-secondary" onclick="resetQuotationForm()">Reset</button>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let quotationData = {};
    let selectedPartnerId = null;

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('selectPartnerButton').addEventListener('click', selectPartner);
    document.getElementById('price').addEventListener('input', calculateTotalPrice);
    document.getElementById('item_id').addEventListener('change', function() {
        showItemDetails();
        calculateTotalPrice();
    });

    document.getElementById('uploadQuotationForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const partnerIdInput = document.createElement('input');
        partnerIdInput.type = 'hidden';
        partnerIdInput.name = 'partner_id';
        partnerIdInput.value = selectedPartnerId;
        this.appendChild(partnerIdInput);

        for (const [itemId, data] of Object.entries(quotationData)) {
            const priceInput = document.createElement('input');
            priceInput.type = 'hidden';
            priceInput.name = `items[${itemId}][price]`;
            priceInput.value = data.price;
            this.appendChild(priceInput);

            const deliveryTimeInput = document.createElement('input');
            deliveryTimeInput.type = 'hidden';
            deliveryTimeInput.name = `items[${itemId}][delivery_time]`;
            deliveryTimeInput.value = data.delivery_time;
            this.appendChild(deliveryTimeInput);

            const remarkInput = document.createElement('input');
            remarkInput.type = 'hidden';
            remarkInput.name = `items[${itemId}][remark]`;
            remarkInput.value = data.remark;
            this.appendChild(remarkInput);

            const totalPriceInput = document.createElement('input');
            totalPriceInput.type = 'hidden';
            totalPriceInput.name = `items[${itemId}][total_price]`;
            totalPriceInput.value = data.total_price;
            this.appendChild(totalPriceInput);
        }

        this.submit();
    });
});

function selectPartner() {
    var selectedPartner = document.querySelector('input[name="selected_partner"]:checked');
    if (selectedPartner) {
        selectedPartnerId = selectedPartner.value;
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

    const itemSpecification = document.getElementById('item-specification').textContent;
    const itemDelivery = document.getElementById('item-delivery').textContent;
    const itemQuantity = document.getElementById('item-quantity').textContent;
    const itemUnit = document.getElementById('item-unit').textContent;

    const tbody = document.querySelector('#tenderItemsTable tbody');

    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>${selectedItemText}</td>
        <td>${itemSpecification}</td>
        <td>${itemDelivery}</td>
        <td>${itemQuantity}</td>
        <td>${itemUnit}</td>
        <td>${priceInput}</td>
        <td>${deliveryTimeInput}</td>
        <td>${remarkInput}</td>
        <td>${totalPriceDisplay}</td>
        <td><button type="button" class="btn btn-danger" onclick="removeRow(this, '${selectedItemValue}')">Delete</button></td>
    `;

    tbody.appendChild(newRow);

    // Store quotation data
    quotationData[selectedItemValue] = {
        price: priceInput,
        delivery_time: deliveryTimeInput,
        remark: remarkInput,
        total_price: totalPriceDisplay
    };

    // Disable the selected option
    itemSelect.options[itemSelect.selectedIndex].disabled = true;

    // Reset and hide modal
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
    document.getElementById('quotationForm').reset();

    document.getElementById('item-specification').textContent = '';
    document.getElementById('item-delivery').textContent = '';
    document.getElementById('item-quantity').textContent = '';
    document.getElementById('item-unit').textContent = '';
    document.getElementById('total-price-display').textContent = '0';

    document.getElementById('itemDetails').style.display = 'none';
}

document.getElementById('quotationForm').addEventListener('submit', function(event) {
    event.preventDefault();
    resetQuotationForm();
});

function removeRow(button, itemId) {
    const row = button.closest('tr');
    row.remove();

    // Re-enable the removed item in the dropdown
    const itemSelect = document.getElementById('item_id');
    const optionToEnable = Array.from(itemSelect.options).find(option => option.value === itemId);
    if (optionToEnable) {
        optionToEnable.disabled = false;
    }

    // Remove the item from quotation data
    delete quotationData[itemId];
}

</script>

@endsection
