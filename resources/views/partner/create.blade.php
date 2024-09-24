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
            <!-- Left side: Name and NPWP -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Input Name">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="npwp" class="form-label">NPWP</label>
                    <input type="text" class="form-control @error('npwp') is-invalid @enderror" id="npwp" name="npwp" value="{{ old('npwp') }}" placeholder="Input NPWP">
                    @error('npwp')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Right side: Category -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <div class="row">
                        @foreach ($categories as $category)
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input type="checkbox"
                                    name="category[]"
                                    id="check-{{ $category->id }}"
                                    value="{{ $category->id }}"
                                    class="form-check-input"
                                    {{ in_array($category->id, old('category', [])) ? 'checked' : '' }}>
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
        </div>
        <div class="row">
            <div class="col mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="Input Description">{{ old('description') }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <!-- Brand, Email, PIC, Contact with append functionality -->
        <div class="row">
            <div class="col mb-3">
                <label for="brand" class="form-label">Brand</label>
                <div id="brand-group">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control @error('brand.*') is-invalid @enderror" name="brand[]" placeholder="Input Brand">
                        <button class="btn btn-outline-secondary add-input" data-name="brand" type="button">Add</button>
                    </div>
                </div>
                @error('brand.*')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col mb-3">
                <label for="email" class="form-label">Email</label>
                <div id="email-group">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control @error('email.*') is-invalid @enderror" name="email[]" placeholder="Input Email">
                        <button class="btn btn-outline-secondary add-input" data-name="email" type="button">Add</button>
                    </div>
                </div>
                @error('email.*')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="col mb-3">
                <label for="pic" class="form-label">PIC</label>
                <div id="pic-group">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control @error('pic.*') is-invalid @enderror" name="pic[]" placeholder="Input PIC">
                        <button class="btn btn-outline-secondary add-input" data-name="pic" type="button">Add</button>
                    </div>
                </div>
                @error('pic.*')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col mb-3">
                <label for="contact" class="form-label">Contact</label>
                <div id="contact-group">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control @error('contact.*') is-invalid @enderror" name="contact[]" placeholder="Input Contact">
                        <button class="btn btn-outline-secondary add-input" data-name="contact" type="button">Add</button>
                    </div>
                </div>
                @error('contact.*')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('partner.index') }}" type="button" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-success">Save</button>
        </div>
        </form>
    </div>

    <!-- Script to add and remove input fields dynamically -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Add input functionality
            document.querySelectorAll('.add-input').forEach(button => {
                button.addEventListener('click', function () {
                    const targetGroup = document.getElementById(button.getAttribute('data-name') + '-group');
                    const inputGroup = document.createElement('div');
                    inputGroup.classList.add('input-group', 'mb-2');

                    // Create new input field with the correct name attribute
                    inputGroup.innerHTML = `
                        <input type="text" class="form-control" name="${button.getAttribute('data-name')}[]" placeholder="Input ${button.getAttribute('data-name').charAt(0).toUpperCase() + button.getAttribute('data-name').slice(1)}">
                        <button class="btn btn-outline-danger remove-input" type="button">Remove</button>
                    `;
                    targetGroup.appendChild(inputGroup);

                    // Attach remove functionality to the newly added button
                    inputGroup.querySelector('.remove-input').addEventListener('click', function () {
                        inputGroup.remove();
                    });
                });
            });

            // Form submission validation
            document.getElementById('vendor-form').addEventListener('submit', function (event) {
                let hasEmptyFields = false;
                const categories = ['brand', 'email', 'pic', 'contact'];

                categories.forEach(category => {
                    const inputs = document.querySelectorAll(`input[name="${category}[]"]`);
                    inputs.forEach(input => {
                        if (input.value.trim() === '') {
                            hasEmptyFields = true;
                        }
                    });
                });

                if (hasEmptyFields) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please fill in all required fields before submitting.',
                    });
                }
            });
        });
    </script>
@endsection
