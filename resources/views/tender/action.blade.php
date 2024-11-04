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
                        <a href="{{ route($route.'.show', $encryptTenderId) }}" class="btn btn-sm btn-info">
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
