@extends('layouts.template')
@section('content')
@php
$title    = 'Dashboard';
$pretitle = 'Dunia Jasa Ads';
@endphp
<div class="row mb-2">
    <div class="col-12 col-md-3">
        <div class="card card-statistic">
            <div class="card-body p-0">
                <div class="d-flex flex-column">
                    <div class='px-3 py-3 d-flex justify-content-between'>
                        <h3 class='card-title'>Place for Ads</h3>
                        <div class="card-right d-flex align-items-center">
                            <p></p>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="canvas1" style="height:100px !important"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card card-statistic">
            <div class="card-body p-0">
                <div class="d-flex flex-column">
                    <div class='px-3 py-3 d-flex justify-content-between'>
                        <h3 class='card-title'>Place for Ads</h3>
                        <div class="card-right d-flex align-items-center">
                            <p></p>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="canvas2" style="height:100px !important"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card card-statistic">
            <div class="card-body p-0">
                <div class="d-flex flex-column">
                    <div class='px-3 py-3 d-flex justify-content-between'>
                        <h3 class='card-title'>Place for Ads</h3>
                        <div class="card-right d-flex align-items-center">
                            <p></p>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="canvas3" style="height:100px !important"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card card-statistic">
            <div class="card-body p-0">
                <div class="d-flex flex-column">
                    <div class='px-3 py-3 d-flex justify-content-between'>
                        <h3 class='card-title'>Place for Ads</h3>
                        <div class="card-right d-flex align-items-center">
                            <p></p>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="canvas4" style="height:100px !important"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
