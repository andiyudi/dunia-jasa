<div class="d-grid gap-2 mx-auto">
    <a href="{{ route($route.'.edit', $data->id) }}" class="btn btn-sm btn-warning action">Edit</a>
    <a href="{{ route($route.'.show', $data->id) }}" class="btn btn-sm btn-info action">Show</a>
    <form action="{{ route($route.'.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this data?')" style="display: inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger action">Delete</button>
    </form>
</div>
