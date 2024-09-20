<div id="sidebar" class='active'>
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <img src="{{ asset('') }}cms/assets/images/logo.svg" alt="" srcset="">
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class='sidebar-title'>Main Menu</li>
                    <li class="sidebar-item ">
                        <a href="{{ route('dashboard') }}" class='sidebar-link'>
                            <i data-feather="home" width="20"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    @if(auth()->user()->is_admin)
                        <li class='sidebar-title'>Category</li>
                        <li class="sidebar-item">
                            <a href="{{ route('category.index') }}" class='sidebar-link'>
                                <i data-feather="menu" width="20"></i>
                                <span>Master Category</span>
                            </a>
                        </li>
                    @endif
                    <li class='sidebar-title'>Vendor</li>
                        {{-- <li class="sidebar-item  ">
                            <a href="#" class='sidebar-link'>
                                <i data-feather="layers" width="20"></i>
                                <span>Create Vendor</span>
                            </a>
                        </li> --}}
                        <li class="sidebar-item  ">
                            <a href="{{ route('vendor.index') }}" class='sidebar-link'>
                                <i data-feather="clipboard" width="20"></i>
                                <span>List Vendor</span>
                            </a>
                        </li>
                        {{-- <li class="sidebar-item  has-sub ">
                            <a href="#" class='sidebar-link'>
                                <i data-feather="clipboard" width="20"></i>
                                <span>List Vendor</span>
                            </a>
                            <ul class="submenu" id="category-submenu">
                                <!-- Categories will be dynamically loaded here -->
                            </ul>
                        </li> --}}
                    <li class='sidebar-title'>Jobs</li>
                    {{-- <li class="sidebar-item">
                        <a href="#" class='sidebar-link'>
                            <i data-feather="link" width="20"></i>
                            <span>Create Procurement</span>
                        </a>
                    </li> --}}
                    <li class="sidebar-item">
                        <a href="#" class='sidebar-link'>
                            <i data-feather="list" width="20"></i>
                            <span>List Procurement</span>
                        </a>
                    </li>
                    <li class='sidebar-title'>Other</li>
                    @if(auth()->user()->is_admin)
                    <li class="sidebar-item">
                        <a href="#" class='sidebar-link'>
                            <i data-feather="settings" width="20"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    @endif
                    <li class="sidebar-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class='sidebar-link'>
                                <i data-feather="log-out" width="20"></i>
                                <span>Log Out</span>
                            </a>
                        </form>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class='sidebar-link'>
                            <i data-feather="help-circle" width="20"></i>
                            <span>Help</span>
                        </a>
                    </li>
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>
{{-- <script>
    function loadCategories() {
    $.ajax({
        url: "{{ route('categories.refresh') }}",
        type: 'GET',
        success: function(response) {
            let categorySubmenu = $('#category-submenu');
            categorySubmenu.empty(); // Clear existing categories

            $.each(response, function(index, category) {
                categorySubmenu.append(`<li><a href="#">${category.name}</a></li>`);
            });
        },
        error: function() {
            console.error('Failed to load categories.');
        }
    });
}

// Initial load when the page is ready
$(document).ready(function() {
    loadCategories();

    // Set interval for polling (e.g., every 3 seconds)
    setInterval(loadCategories, 3000);
});

</script> --}}
