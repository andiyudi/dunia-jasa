@extends('layouts.template')
@section('content')
@php
$title    = 'Tender : ' . $tender->name;
$pretitle = 'Table Comparations';
@endphp
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
    <div class="table-responsive">
        <table class="table table-bordered" width="100%">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th colspan="5" class="text-center">Item</th>
                    @php
                        // Ambil semua partner unik dari quotations melalui partnerUser
                        $partners = collect();
                        foreach ($tender->items as $item) {
                            $partners = $partners->merge(
                                $item->quotations->map(function ($quotation) {
                                    return $quotation->partnerUser->partner->name ?? null;
                                })
                            );
                        }
                        $partners = $partners->filter()->unique();
                    @endphp
                    @foreach ($partners as $partner)
                        <th colspan="4" class="text-center">{{ $partner }}</th>
                    @endforeach
                </tr>
                <tr>
                    <th>Description</th>
                    <th>Specification</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Delivery</th>
                    @foreach ($partners as $partner)
                        <th class="text-center">Remark</th>
                        <th class="text-center">Delivery Time</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Total Price</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($tender->items as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->specification ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>{{ $item->delivery ?? '-' }}</td>
                        @foreach ($partners as $partner)
                            @php
                                $quotation = $item->quotations->first(function ($q) use ($partner) {
                                    return $q->partnerUser->partner->name === $partner;
                                });
                            @endphp
                            <td>{{ $quotation->remark ?? '-' }}</td>
                            <td>{{ $quotation->delivery_time ?? '-' }}</td>
                            <td>{{ $quotation ? number_format($quotation->price) : '-' }}</td>
                            <td>{{ $quotation ? number_format($quotation->total_price) : '-' }}</td>
                        @endforeach
                    </tr>
                @endforeach

                <!-- Baris Total -->
                <tr>
                    <td colspan="6"><strong>Total</strong></td>
                    @foreach ($partners as $partner)
                        @php
                            $totalPrice = 0;
                            foreach ($tender->items as $item) {
                                $quotation = $item->quotations->first(function ($q) use ($partner) {
                                    return $q->partnerUser->partner->name === $partner;
                                });
                                $totalPrice += $quotation->total_price ?? 0;
                            }
                        @endphp
                        <td colspan="3"></td>
                        <td><strong>{{ number_format($totalPrice) }}</strong></td>
                    @endforeach
                </tr>

                <!-- Baris Files -->
                <tr>
                    <td colspan="6"><strong>Files</strong></td>
                    @foreach ($partners as $partner)
                        @php
                            $partnerFiles = $tender->files->filter(function ($file) use ($partner) {
                                return $file->partner->name === $partner;
                            });
                        @endphp
                        <td colspan="4">
                            @if ($partnerFiles->count() > 0)
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#filesModal-{{ Str::slug($partner) }}">
                                    Lihat File ({{ $partnerFiles->count() }})
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="filesModal-{{ Str::slug($partner) }}" tabindex="-1" aria-labelledby="filesModalLabel-{{ Str::slug($partner) }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="filesModalLabel-{{ Str::slug($partner) }}">
                                                    Files for Partner: {{ $partner }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if ($partnerFiles->isEmpty())
                                                    <p class="text-muted">Tidak ada file yang diunggah.</p>
                                                @else
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Nama File</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($partnerFiles as $index => $file)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $file->name }}</td>
                                                                    <td>
                                                                        <a href="{{ $file->path }}" target="_blank" class="btn btn-sm btn-primary">Download</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <em>Tidak ada file</em>
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
