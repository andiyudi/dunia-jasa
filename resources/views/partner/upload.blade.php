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
        <div class="col-md-3">
            <label for="type_id" class="form-label">Document Type</label>
        </div>
        <div class="col-md-9">
            <select class="form-control" id="type_id" name="type_id" required>
                <option value="">Select Document Type</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Company Profile Upload Input -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="company_profile" class="form-label">Document (PDF)</label>
        </div>
        <div class="col-md-9">
            <input type="file" class="form-control" id="company_profile" name="company_profile" accept=".pdf" required>
        </div>
    </div>

    <!-- Notes Input -->
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="notes" class="form-label">Notes</label>
        </div>
        <div class="col-md-9">
            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes"></textarea>
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
