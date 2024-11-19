@php
    $encryptTenderId = encrypt($data->id);
@endphp
<div class="d-grid gap-2 col-8 mx-auto">
    <a href="{{ route('quotation.create', ['tender_id' => $encryptTenderId]) }}" class="join-tender btn btn-success btn-sm {{ $disabled }}" {{ $disabled ? 'disabled' : '' }}>
        Submit
    </a>
</div>
