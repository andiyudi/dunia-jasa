@extends('layouts.template')
@section('content')
@php
$title    = 'Tender';
$pretitle = 'Data';
@endphp
    <!-- Filter Form -->
    <div class="row mb-3">
        <div class="col-md-4 mb-3">
        </div>
        <div class="col-md-4 mb-3">
        </div>
        <div class="col-md-4 mb-3">
            <div class="btn-group mb-3 float-end" role="group" aria-label="Basic example">
                <a href="{{ route('tender.create') }}" class="btn btn-primary">Create Tender</a>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <div class="col-md-12 mb-3">
            <table class="table table-bordered table-striped table-hover display responsive no wrap" id="tender-table" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:center">No</th>
                        <th style="text-align:center">Company</th>
                        <th style="text-align:center">Category</th>
                        <th style="text-align:center">Tender</th>
                        <th style="text-align:center">Location</th>
                        <th style="text-align:center">Estimation</th>
                        <th style="text-align:center">Status</th>
                        <th style="text-align:center">Document</th>
                        <th style="text-align:center">Quotation</th>
                        <th style="text-align:center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
