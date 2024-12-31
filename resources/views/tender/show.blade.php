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
                    <th rowspan="2" class="text-center table-warning">No</th>
                    <th colspan="5" class="text-center table-warning">Item</th>
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
                        <th colspan="4" class="text-center table-secondary">{{ $partner }}</th>
                    @endforeach
                </tr>
                <tr>
                    <th class="table-warning">Description</th>
                    <th class="table-warning">Specification</th>
                    <th class="table-warning">Quantity</th>
                    <th class="table-warning">Unit</th>
                    <th class="table-warning">Delivery</th>
                    @foreach ($partners as $partner)
                        <th class="text-center table-secondary">Remark</th>
                        <th class="text-center table-secondary">Delivery Time</th>
                        <th class="text-center table-secondary">Price</th>
                        <th class="text-center table-secondary">Total Price</th>
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
                <tr class="table-info">
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
                <!-- Baris Terms of Payment -->
                <td colspan="6"><strong>Terms of Payment</strong></td>
                @foreach ($tender->payments as $payment)
                    <td colspan="4">{{ $payment->terms_of_payment }}</td>
                @endforeach
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
    <!-- Back Button -->
    <div class="d-grid gap-2 d-md-flex mt-3 justify-content-md-end">
        <a href="{{ route('tender.index') }}" class="btn btn-secondary">
            Back
        </a>
        <!-- Close Tender Button -->
        <button type="button" class="btn btn-success" id="closeTenderButton" data-url="{{ route('tender.close', $tender->id) }}">
            Close Tender
        </button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#closeTenderButton').on('click', function() {
            var url = $(this).data('url');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, close it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'PATCH',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Closed!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    window.location.href = "{{ route('tender.index') }}";
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'An error occurred while closing the tender.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
