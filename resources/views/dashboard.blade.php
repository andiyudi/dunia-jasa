@extends('layouts.template')
@section('content')
@php
$title    = 'Dashboard';
$pretitle = 'Dunia Jasa';
@endphp
<div class="row mb-2">
    <div class="col-12 col-md-4">
        <div class="card card-statistic">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x text-primary mb-2"></i> <!-- Tambahkan ikon -->
                <h5 class="card-title mb-3 text-uppercase">Managed Partners</h5>
                <div class="d-flex justify-content-center align-items-center">
                    <div class="display-4 text-dark fw-bold">
                        {{ $partnerCount }}
                    </div>
                </div>
                <p class="text-muted mt-2">Total partners under your management</p>
            </div>
        </div>
    </div>
    <!-- Card untuk Tender Count -->
    <div class="col-12 col-md-4 mb-4">
        <div class="card card-statistic">
            <div class="card-body text-center">
                <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                <h5 class="card-title mb-3 text-uppercase">Total Tenders</h5>
                <div class="d-flex justify-content-center align-items-center">
                    <div class="display-4 text-dark fw-bold">
                        {{ $tenderCount }}
                    </div>
                </div>
                <p class="text-muted mt-2">Total tenders created by your partners</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-4">
        <div class="card card-statistic">
            <div class="card-body text-center">
                <i class="fas fa-paperclip fa-2x text-primary mb-2"></i>
                <h5 class="card-title mb-3 text-uppercase">Total Quotation Submissions</h5>
                <div class="d-flex justify-content-center align-items-center">
                    <div class="display-4 text-dark fw-bold">
                        {{ $quotationSubmitCount }}
                    </div>
                </div>
                <p class="text-muted mt-2">Total quotations submitted for the same tenders by your partners</p>
            </div>
        </div>
    </div>
</div>
@endsection
