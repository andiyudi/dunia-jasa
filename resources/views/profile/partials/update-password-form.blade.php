<div class="card mt-3">
    <div class="card-header">
        Update Password
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')
            <div class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Your Current Password">
                    </div>
                    <div class="col-md-4">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Your New Password">
                    </div>
                    <div class="col-md-4">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Your New Password">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary float-end">Submit</button>
            @if (session('status') === 'password-updated')
            <p class="text-sm text-success">
                Password updated successfully!
            </p>
            @endif
        </form>
    </div>
</div>
