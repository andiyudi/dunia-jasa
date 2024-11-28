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
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Partner</th>
                @foreach ($tender->items as $item)
                    <th>{{ $item->description }}</th>
                @endforeach
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
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
                <tr>
                    <td>{{ $partner }}</td>
                    @foreach ($tender->items as $item)
                        @php
                            $quotation = $item->quotations->first(function ($q) use ($partner) {
                                return $q->partnerUser->partner->name === $partner;
                            });
                        @endphp
                        <td>
                            @if ($quotation)
                                <strong>Harga:</strong> {{ number_format($quotation->price) }}<br>
                                <strong>Delivery Time:</strong> {{ $quotation->delivery_time }}<br>
                                <strong>Total Price:</strong> {{ number_format($quotation->total_price) }}<br>
                                <strong>Remark:</strong> {{ $quotation->remark }}
                            @else
                                <em>Tidak ada data</em>
                            @endif
                        </td>
                    @endforeach
                    <td>
                        @php
                            $partnerFiles = $tender->files->filter(function ($file) use ($partner) {
                                return $file->partner->name === $partner;
                            });
                        @endphp

                        @if ($partnerFiles->count() > 0)
                            <!-- Tombol Lihat File -->
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
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

