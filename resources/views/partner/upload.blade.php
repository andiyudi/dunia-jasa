<!-- Blade template -->
@extends('layouts.template')
@section('content')
@php
$title    = $partner->name;
$pretitle = 'Data';
@endphp
<h3>Upload</h3>
<div>
    <form id="vendor-upload" action="{{ route('partner.save', $partner->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row mb-3">
        <!-- Type Selection -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="type_id" class="form-label">Document Type</label>
            </div>
            <div class="col-md-9">
                <select class="form-control @error('type_id') is-invalid @enderror" id="type_id" name="type_id">
                    <option value="">Select Document Type</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('type_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Company Profile Upload Input -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="company_profile" class="form-label">Document (PDF)</label>
            </div>
            <div class="col-md-9">
                <input type="file" class="form-control @error('company_profile') is-invalid @enderror" id="company_profile" name="company_profile" accept=".pdf">
                @error('company_profile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Notes Input -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="notes" class="form-label">Notes</label>
            </div>
            <div class="col-md-9">
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Document notes">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

    <!-- Submit button -->
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="{{ route('partner.index') }}" type="button" class="btn btn-secondary">Back</a>
        <button type="submit" class="btn btn-success">Upload</button>
    </div>
    </form>
</div>
@endsection
