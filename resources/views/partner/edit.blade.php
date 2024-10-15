@extends('layouts.template')
@section('content')
@php
$title    = $partner->name;
$pretitle = 'Data';
@endphp
<h3>Edit Partner</h3>
<div>
    <form id="vendor-form" action="{{ route('partner.update', $partner->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <!-- Name input field -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $partner->name) }}" placeholder="Input Name">
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
            <button type="button" class="btn btn-success add-brand float-end mb-3">+</button>
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
            <label class="form-label">Existing Files</label>
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
                                {{-- <td>{{ $index + 1 }}</td> --}}
                                <td>{{ $file->id }}</td>
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
    </div>

    <!-- Submit button -->
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="{{ route('partner.index') }}" type="button" class="btn btn-secondary">Back</a>
        <button type="submit" class="btn btn-success">Update</button>
    </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
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

    // Handle file deletion
    const deleteFileButtons = document.querySelectorAll('.delete-file');
    deleteFileButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this file?')) {
                const fileId = this.getAttribute('data-file-id');
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
                        this.closest('tr').remove();
                        if (document.querySelectorAll('.delete-file').length === 0) {
                            document.querySelector('.table').remove();
                            document.querySelector('.col.mb-3').innerHTML += '<p>No files uploaded yet.</p>';
                        }
                    } else {
                        alert('Failed to delete the file. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the file.');
                });
            }
        });
    });
});
</script>

@endsection
