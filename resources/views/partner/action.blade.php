@php
    $encryptPartnerId = encrypt($data->id);
@endphp
@if(Auth::user()->is_admin || Auth::user()->partners->contains($data->id))
<div class="d-grid gap-2 col-8 mx-auto">
    <a href="{{ route($route.'.edit', $encryptPartnerId) }}" class="btn btn-sm btn-warning action icon icon-left">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
            <path d="M12 20h9"></path>
            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
        </svg>
    </a>
    <a href="{{ route($route.'.show', $encryptPartnerId) }}" class="btn btn-sm btn-info action icon icon-left">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
    </a>
    <a href="{{ route($route.'.upload', $encryptPartnerId) }}" class="btn btn-sm btn-primary action icon icon-left">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload-cloud">
            <polyline points="16 16 12 12 8 16"/>
            <line x1="12" y1="12" x2="12" y2="21"/>
            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
            <polyline points="16 16 12 12 8 16"/>
        </svg>
    </a>
    @if(Auth::user()->is_admin)
        <button class="btn btn-sm btn-danger action icon icon-left delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $encryptPartnerId }}" data-id="{{ $encryptPartnerId }}">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </button>
    @else
        <button class="btn btn-sm btn-danger action icon icon-left remove-btn" data-bs-toggle="modal" data-bs-target="#removeModal-{{ $encryptPartnerId }}" data-id="{{ $encryptPartnerId }}">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
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
