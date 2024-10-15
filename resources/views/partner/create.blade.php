<!-- Blade template -->
@extends('layouts.template')
@section('content')
@php
$title    = 'Vendor';
$pretitle = 'Data';
@endphp
<h3>Create</h3>
<div>
    <form id="vendor-form" action="{{ route('partner.store') }}" method="POST">
    @csrf
    <div class="row">
        <!-- Name input field -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Input Name">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Other fields, initially readonly -->
    <div class="row">
        <!-- NPWP field -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="npwp" class="form-label">NPWP</label>
                <input type="text" class="form-control @error('npwp') is-invalid @enderror" id="npwp" name="npwp" value="{{ old('npwp') }}" placeholder="Input NPWP" readonly>
                @error('npwp')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Category field -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <div class="row">
                    @foreach ($categories as $category)
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input type="checkbox" name="category[]" id="check-{{ $category->id }}" value="{{ $category->id }}" class="form-check-input" disabled>
                            <label for="check-{{ $category->id }}" class="form-check-label">{{ $category->name }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if ($errors->has('category'))
                    <div class="invalid-feedback d-block">{{ $errors->first('category') }}</div>
                @endif
            </div>
        </div>

    <!-- Description field -->
    <div class="row">
        <div class="col mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="Input Description" readonly>{{ old('description') }}</textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Brand field with dynamic add/remove functionality -->
    <div class="row">
        <div class="col mb-3">
            <label for="brand" class="form-label">Brand</label>
            <button type="button" class="btn btn-success add-brand float-end mb-3">+</button> <!-- Separate Add button -->
            <div id="brand-group" class="mt-2"> <!-- Input groups for brands will go here -->
                <div class="input-group mb-2">
                    <input type="text" class="form-control @error('brand.*') is-invalid @enderror" name="brand[]" placeholder="Input Brand" readonly>
                    <button type="button" class="btn btn-danger remove-brand" disabled>-</button>
                </div>
            </div>
            @error('brand.*')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- PIC, Email, and Contact fields (always readonly) -->
    <div class="row">
        <div class="col mb-3">
            <label for="pic" class="form-label">PIC</label>
            <input type="text" class="form-control" name="pic" value="{{ auth()->user()->name }}" readonly>
        </div>

        <div class="col mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control" name="email" value="{{ auth()->user()->email }}" readonly>
        </div>

        <div class="col mb-3">
            <label for="contact" class="form-label">Contact</label>
            <input type="text" class="form-control" name="contact" value="{{ auth()->user()->phone }}" readonly>
        </div>
        <!-- Submit button -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('partner.index') }}" type="button" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-success">Save</button>
        </div>
    </div>
    </form>
</div>

<!-- JavaScript for name check and adding/removing brand inputs -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nameInput = document.getElementById('name');
        const npwpField = document.getElementById('npwp');
        const descriptionField = document.getElementById('description');
        const brandGroup = document.getElementById('brand-group');
        const addBrandButton = document.querySelector('.add-brand');
        const categoryCheckboxes = document.querySelectorAll('[name="category[]"]');
        const form = document.getElementById('vendor-form');

        // Disable the Add Brand button initially
        addBrandButton.disabled = true;

        // Enable/disable fields
        const toggleReadOnlyFields = (readonly) => {
            npwpField.readOnly = readonly;
            descriptionField.readOnly = readonly;
            brandGroup.querySelectorAll('input').forEach(input => input.readOnly = readonly);
            categoryCheckboxes.forEach(checkbox => checkbox.disabled = readonly);
            brandGroup.querySelectorAll('.remove-brand').forEach(button => button.disabled = readonly);
        };

        // Function to enable/disable Add Brand button based on Name input
        const toggleAddBrandButton = () => {
            addBrandButton.disabled = !nameInput.value.trim(); // Disable if name is empty
        };

        // Add new brand input
        const addBrandInput = () => {
            const newBrandInput = document.createElement('div');
            newBrandInput.classList.add('input-group', 'mb-2');
            newBrandInput.innerHTML = `
                <input type="text" class="form-control" name="brand[]" placeholder="Input Brand">
                <button type="button" class="btn btn-danger remove-brand">-</button>
            `;
            brandGroup.appendChild(newBrandInput);
            updateBrandListeners(); // Update listeners for new inputs
        };

        // Remove brand input
        const removeBrandInput = (button) => {
            const inputGroup = button.closest('.input-group');
            inputGroup.remove();
        };

        // Update listeners for brand add/remove buttons
        const updateBrandListeners = () => {
            // Add functionality for "Remove" buttons
            brandGroup.querySelectorAll('.remove-brand').forEach(button => {
                button.removeEventListener('click', () => removeBrandInput(button));
                button.addEventListener('click', () => removeBrandInput(button));
            });
        };

        // Check name availability when the user stops typing
        nameInput.addEventListener('blur', function () {
            const name = nameInput.value.trim();

            if (name) {
                // Disable the name input immediately
                nameInput.readOnly = true;

                // Show loading indicator
                const loadingIndicator = document.createElement('div');
                loadingIndicator.id = 'loading-indicator';
                loadingIndicator.textContent = 'Checking name...';
                nameInput.parentNode.appendChild(loadingIndicator);

                // Make an AJAX request to check if the name exists
                fetch('{{ route('partner.check') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ name: name })
                })
                .then(response => response.json())
                .then(data => {
                    // Remove loading indicator
                    document.getElementById('loading-indicator').remove();

                    if (data.exists) {
                        // Name exists, populate fields and keep name input readonly
                        npwpField.value = data.npwp || '';
                        descriptionField.value = data.description || '';
                        toggleReadOnlyFields(false);

                        if (data.categories && data.categories.length > 0) {
                            data.categories.forEach(categoryId => {
                                const checkbox = document.querySelector(`#check-${categoryId}`);
                                if (checkbox) checkbox.checked = true;
                            });
                        }

                        if (data.brands && data.brands.length > 0) {
                            brandGroup.innerHTML = '';
                            data.brands.forEach(brand => {
                                const newBrandInput = document.createElement('div');
                                newBrandInput.classList.add('input-group', 'mb-2');
                                newBrandInput.innerHTML = `
                                    <input type="text" class="form-control" name="brand[]" value="${brand}" placeholder="Input Brand">
                                    <button type="button" class="btn btn-danger remove-brand">-</button>
                                `;
                                brandGroup.appendChild(newBrandInput);
                            });
                            updateBrandListeners();
                        }
                    } else {
                        // Name doesn't exist, allow editing other fields
                        toggleReadOnlyFields(false);
                    }

                    // Keep name input readonly in both cases
                    nameInput.readOnly = true;

                    // Update Add Brand button state
                    toggleAddBrandButton();
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Remove loading indicator
                    document.getElementById('loading-indicator').remove();
                    // Re-enable name input in case of error
                    nameInput.readOnly = false;
                    alert('An error occurred while checking the name. Please try again.');
                });
            }
        });

        // Prevent form submission if name is empty
        form.addEventListener('submit', function(event) {
            if (!nameInput.value.trim()) {
                event.preventDefault();
                alert('Please enter a name before submitting the form.');
            }
        });

        // Monitor the name input for changes to enable/disable Add Brand button
        nameInput.addEventListener('input', toggleAddBrandButton);

        // Event listener for the Add Brand button
        addBrandButton.addEventListener('click', addBrandInput);
    });
</script>

@endsection

