@extends('layouts.template')
@section('content')
@php
$title    = 'Tender';
$pretitle = 'Data';
@endphp
<h3>Show</h3>
<div>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


</div>
@endsection
