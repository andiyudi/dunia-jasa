@php
    $encryptTenderId = encrypt($data->id);
@endphp
<div class="d-grid gap-2 col-8 mx-auto">
    @if(Auth::id() === $creatorId)
        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
            <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Action
                </button>
                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <li>
                        <a href="{{ route($route.'.edit', $encryptTenderId) }}" class="dropdown-item">
                            Edit
                        </a>
                    </li>
                    <li>
                        <a href="{{ route($route.'.show', $encryptTenderId) }}" class="dropdown-item">
                            Show
                        </a>
                    </li>
                    <li>
                        <button class="dropdown-item remove-btn" data-bs-toggle="modal" data-bs-target="#removeModal-{{ $encryptTenderId }}" data-id="{{ $encryptTenderId }}">
                            Remove
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    @else
        @if(Auth::user()->is_admin)
            <button class="btn btn-sm btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $encryptTenderId }}" data-id="{{ $encryptTenderId }}">
                Delete
            </button>
        @endif
    @endif
</div>
<div class="modal fade" id="removeModal-{{ $encryptTenderId }}" tabindex="-1" aria-labelledby="removeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeModalLabel">Confirm Removal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove this tender?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('tender.destroy', $encryptTenderId) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remove</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteModal-{{ $encryptTenderId }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this tender permanently?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('tender.destroy', $encryptTenderId) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
