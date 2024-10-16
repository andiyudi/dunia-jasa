@extends('layouts.template')
@section('content')
@php
$title    = $partner->name;
$pretitle = 'Data';
@endphp
<h3>Edit Partner</h3>
<div>
    <form id="vendor-form" action="{{ route('partner.update', encrypt($partner->id)) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <!-- Name input field -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $partner->name) }}" placeholder="Input Name">
                <div id="name-error" class="invalid-feedback"></div>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- NPWP field -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="npwp" class="form-label">NPWP</label>
                <input type="text" class="form-control @error('npwp') is-invalid @enderror" id="npwp" name="npwp" value="{{ old('npwp', $partner->npwp) }}" placeholder="Input NPWP">
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
                            <input type="checkbox" name="category[]" id="check-{{ $category->id }}" value="{{ $category->id }}" class="form-check-input"
                                {{ in_array($category->id, old('category', $partner->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
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

    <!-- Description field -->
    <div class="row">
        <div class="col mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="Input Description">{{ old('description', $partner->description) }}</textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Brand field with dynamic add/remove functionality -->
    <div class="row">
        <div class="col mb-3">
            <label for="brand" class="form-label">Brand</label>
            <button type="button" class="btn btn-primary add-brand float-end mb-3">+</button>
            <div id="brand-group" class="mt-2">
                @foreach(old('brand', $partner->brands) as $brand)
                <div class="input-group mb-2">
                    <input type="text" class="form-control @error('brand.*') is-invalid @enderror" name="brand[]" value="{{ $brand->name }}" placeholder="Input Brand">
                    <button type="button" class="btn btn-danger remove-brand">-</button>
                </div>
                @endforeach
            </div>
            @error('brand.*')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Display existing files -->
    <div class="row">
        <div class="col mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label">Existing Files</label>
                <a href="{{ route('partner.upload', encrypt($partner->id)) }}" class="btn btn-primary">Upload New File</a>
            </div>
            @if($partner->files->isEmpty())
                <p>No files uploaded yet.</p>
            @else
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>File Name</th>
                            <th>File Type</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partner->files as $index => $file)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $file->name }}</td>
                                <td>{{ $file->type->name ?? 'N/A' }}</td>
                                <td>{{ $file->note }}</td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm delete-file" data-file-id="{{ $file->id }}">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Submit button -->
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="{{ route('partner.index') }}" type="button" class="btn btn-secondary">Back</a>
        <button type="submit" class="btn btn-success" id="update-button">Update</button>
    </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Name check functionality
    const nameInput = document.getElementById('name');
    const nameError = document.getElementById('name-error');
    const updateButton = document.getElementById('update-button');
    const form = document.getElementById('vendor-form');
    const originalName = '{{ $partner->name }}';
    let isNameValid = true;

    nameInput.addEventListener('blur', function() {
        if (this.value !== originalName) {
            checkName(this.value);
        } else {
            nameError.textContent = '';
            nameInput.classList.remove('is-invalid');
            updateButton.disabled = false;
            isNameValid = true;
        }
    });

    function checkName(name) {
        fetch('{{ route("partner.check") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                nameError.textContent = 'This name already exists. Please choose a different name.';
                nameInput.classList.add('is-invalid');
                updateButton.disabled = true;
                isNameValid = false;
            } else {
                nameError.textContent = '';
                nameInput.classList.remove('is-invalid');
                updateButton.disabled = false;
                isNameValid = true;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            isNameValid = false;
        });
    }

    form.addEventListener('submit', function(e) {
        if (nameInput.value !== originalName) {
            e.preventDefault();
            checkName(nameInput.value);
            setTimeout(() => {
                if (isNameValid) {
                    form.submit();
                }
            }, 500);
        }
    });

    const brandGroup = document.getElementById('brand-group');
    const addBrandButton = document.querySelector('.add-brand');

    // Add new brand input
    const addBrandInput = () => {
        const newBrandInput = document.createElement('div');
        newBrandInput.classList.add('input-group', 'mb-2');
        newBrandInput.innerHTML = `
            <input type="text" class="form-control" name="brand[]" placeholder="Input Brand">
            <button type="button" class="btn btn-danger remove-brand">-</button>
        `;
        brandGroup.appendChild(newBrandInput);
        updateBrandListeners();
    };

    // Remove brand input
    const removeBrandInput = (button) => {
        const inputGroup = button.closest('.input-group');
        inputGroup.remove();
    };

    // Update listeners for brand add/remove buttons
    const updateBrandListeners = () => {
        brandGroup.querySelectorAll('.remove-brand').forEach(button => {
            button.removeEventListener('click', () => removeBrandInput(button));
            button.addEventListener('click', () => removeBrandInput(button));
        });
    };

    // Event listener for the Add Brand button
    addBrandButton.addEventListener('click', addBrandInput);

    // Initialize listeners for existing remove buttons
    updateBrandListeners();

    // Handle file deletion with SweetAlert2
    const deleteFileButtons = document.querySelectorAll('.delete-file');
    deleteFileButtons.forEach(button => {
        button.addEventListener('click', function() {
            const fileId = this.getAttribute('data-file-id');
            const row = this.closest('tr');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ route('partner.file-delete', '') }}/${fileId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            );
                            row.remove();
                            if (document.querySelectorAll('.delete-file').length === 0) {
                                document.querySelector('.table').remove();
                                document.querySelector('.col.mb-3').innerHTML += '<p>No files uploaded yet.</p>';
                            }
                        } else {
                            Swal.fire(
                                'Error!',
                                'Failed to delete the file. Please try again.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire(
                            'Error!',
                            'An error occurred while deleting the file.',
                            'error'
                        );
                    });
                }
            });
        });
    });
});
</script>

@endsection
