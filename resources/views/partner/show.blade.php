@extends('layouts.template')
@section('content')
@php
$title    = 'Show';
$pretitle = 'Data';
// Jika user adalah admin
if (auth()->user()->is_admin) {
        if ($partner->is_verified == false) {
            $title .= ' And Verify'; // Tambahkan "Verify" jika belum diverifikasi
        } else {
            $title .= ' And Cancel Verify'; // Tambahkan "Cancel" jika sudah diverifikasi
        }
    }
@endphp

    <!-- Partner Information Section -->
    <div class="row">
        <div class="col-md-12">
            <h3 class="mb-4">{{ $partner->name }}</h3>
        </div>
    </div>

    <!-- Vendor Information (NPWP & Description) and Users Information (Table) -->
    <div class="row mb-4">
        <!-- Vendor Information (NPWP & Description) -->
        <div class="col-md-3">
            <h5>Information</h5>
            <p><strong>NPWP:</strong> {{ $partner->npwp }}</p>
            <p><strong>Description:</strong> {{ $partner->description }}</p>
        </div>

        <!-- Users Information in Table -->
        <div class="col-md-9">
            <h5>PIC</h5>
            @if($partner->users->isEmpty())
                <p>No users selected.</p>
            @else
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partner->users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Categories and Brands Section (Side by Side) -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h5>Categories</h5>
            @if($partner->categories->isEmpty())
                <p>No categories selected.</p>
            @else
                <ul>
                    @foreach($partner->categories as $category)
                        <li><i class="bi bi-tags"></i> {{ $category->name }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="col-md-6">
            <h5>Brands</h5>
            @if($partner->brands->isEmpty())
                <p>No brands available.</p>
            @else
                <ul>
                    @foreach($partner->brands as $brand)
                        <li><i class="bi bi-box"></i> {{ $brand->name }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- Uploaded Files Section -->
    <div class="row mb-4">
        <div class="col">
            <h5>Uploaded Files</h5>
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
                                    <a href="{{ $file->path }}" target="_blank" class="btn btn-primary btn-sm">
                                        Download
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="{{ route('partner.index') }}" class="btn btn-secondary">
            Back
        </a>

        @if(auth()->user()->is_admin)  <!-- Cek apakah pengguna adalah admin -->
            <button type="button" class="btn @if($partner->is_verified == false) btn-success @else btn-danger @endif" id="verifyButton">
                @if($partner->is_verified == false)
                    Verify
                @else
                    Cancel
                @endif
            </button>
            <form id="verifyForm" action="{{ route('partner.verify', encrypt($partner->id)) }}" method="POST" style="display:none;">
                @csrf
                @method('PATCH')
            </form>
        @endif
    </div>

<!-- SweetAlert Script for Verification Confirmation -->
<script>
    document.getElementById('verifyButton').addEventListener('click', function () {
        let isVerified = {{ $partner->is_verified ? 'true' : 'false' }};
        let confirmText = isVerified ? 'Cancel verification?' : 'Verify this partner?';
        let buttonText = isVerified ? 'Yes, cancel it!' : 'Yes, verify it!';
        let successText = isVerified ? 'Verification cancelled!' : 'Partner verified!';
        let btnClass = isVerified ? 'btn-danger' : 'btn-success';

        Swal.fire({
            title: 'Are you sure?',
            text: confirmText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: buttonText,
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('verifyForm').submit();
            }
        });
    });
</script>
@endsection
