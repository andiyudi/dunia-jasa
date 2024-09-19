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
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
