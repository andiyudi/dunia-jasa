@extends('layouts.template')
@section('content')
@php
$title    = 'Account';
$pretitle = 'Information';
@endphp
<div class="row mb-2">
    <div class="col-12">
        @include('profile.partials.update-profile-information-form')
        @include('profile.partials.update-password-form')
        <div class="card mt-3">
            <div class="card-body">
                <div class="mb-3">
                    <label for="passwordLama" class="form-label">Hapus Akun</label>
                </div>
                <button type="button" class="btn btn-danger">Ya, Hapus!</button>
            </div>
        </div>
    </div>
</div>
@endsection
