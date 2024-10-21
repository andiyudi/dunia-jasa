<!-- Blade template -->
@extends('layouts.template')
@section('content')
@php
$title    = 'Tender';
$pretitle = 'Data';
@endphp
<h3>Create</h3>
<div>
    <form id="tender-form" action="{{ route('tender.store') }}" method="POST">
    @csrf
        <div class="row">
            <div class="col-md-6">
                <!-- Partner (only for verified partners or admins) -->
                @auth
                    @if(Auth::user()->is_admin)
                        <!-- Admin can select any partner -->
                        <div class="form-group">
                            <label for="partner_id">Select Partner</label>
                            <select name="partner_id" id="partner_id" class="form-control" required>
                                @foreach($partners as $partner)
                                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($partners->isNotEmpty())
                        <!-- Verified partner can select from their list -->
                        <div class="form-group">
                            <label for="partner_id">Select Partner</label>
                            <select name="partner_id" id="partner_id" class="form-control" required>
                                @foreach($partners as $partner)
                                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <p class="text-muted">You do not have any verified partners. You cannot create a tender.</p>
                    @endif
                @endauth
            </div>
            <div class="col-md-6">
                <!-- Category -->
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Tender Name -->
            <div class="form-group">
                <label for="name">Tender Name</label>
                <textarea type="text" name="name" id="name" class="form-control" required></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <!-- Location -->
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" name="location" id="location" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Estimation -->
                <div class="form-group">
                    <label for="estimation">Estimation</label>
                    <input type="text" name="estimation" id="estimation" class="form-control" required>
                </div>
            </div>
            <!-- Submit button -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="{{ route('tender.index') }}" type="button" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </div>
    </form>
</div>
@endsection

