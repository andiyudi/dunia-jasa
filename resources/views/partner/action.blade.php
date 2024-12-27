@php
    $encryptPartnerId = encrypt($data->id);
@endphp
@if(Auth::user()->is_admin || Auth::user()->partners->contains($data->id))
<div class="d-grid gap-2 col-8 mx-auto">
    <a href="{{ route($route.'.edit', $encryptPartnerId) }}" class="btn btn-sm btn-warning action icon icon-left">
        Edit
    </a>
    <a href="{{ route($route.'.show', $encryptPartnerId) }}" class="btn btn-sm btn-info action icon icon-left">
        Show
    </a>
    <a href="{{ route($route.'.upload', $encryptPartnerId) }}" class="btn btn-sm btn-primary action icon icon-left">
        Upload
    </a>
    @if(Auth::user()->is_admin)
        <button class="btn btn-sm btn-danger action icon icon-left delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $encryptPartnerId }}" data-id="{{ $encryptPartnerId }}">
            Delete
        </button>
    @else
        <button class="btn btn-sm btn-danger action icon icon-left remove-btn" data-bs-toggle="modal" data-bs-target="#removeModal-{{ $encryptPartnerId }}" data-id="{{ $encryptPartnerId }}">
            Remove
        </button>
    @endif
</div>
<!-- Delete Confirmation Modal (for admin) -->
@if(Auth::user()->is_admin)
<div class="modal fade" id="deleteModal-{{ $encryptPartnerId }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $encryptPartnerId }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel-{{ $encryptPartnerId }}">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this partner? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm-{{ $encryptPartnerId }}" method="POST" action="{{ route($route.'.destroy', $encryptPartnerId) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@else
<!-- Remove Confirmation Modal (for non-admin users) -->
<div class="modal fade" id="removeModal-{{ $encryptPartnerId }}" tabindex="-1" aria-labelledby="removeModalLabel-{{ $encryptPartnerId }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeModalLabel-{{ $encryptPartnerId }}">Confirm Removal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove this partner from your account? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="removeForm-{{ $encryptPartnerId }}" method="POST" action="{{ route($route.'.remove', $encryptPartnerId) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remove</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endif
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // For admin delete buttons
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const encryptedPartnerId = this.getAttribute('data-id');
                const form = document.getElementById('deleteForm-' + encryptedPartnerId);
                if (form) {
                    form.action = "{{ route($route.'.destroy', '') }}/" + encryptedPartnerId;
                }
            });
        });

        // For non-admin remove buttons
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function() {
                const encryptedPartnerId = this.getAttribute('data-id');
                const form = document.getElementById('removeForm-' + encryptedPartnerId);
                if (form) {
                    form.action = "{{ route($route.'.remove', '') }}/" + encryptedPartnerId;
                }
            });
        });
    });
</script>
