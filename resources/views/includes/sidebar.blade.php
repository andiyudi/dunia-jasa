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
                    <li class='sidebar-title'>Category</li>
                    <li class="sidebar-item  ">
                        <a href="{{ route('category.index') }}" class='sidebar-link'>
                            <i data-feather="menu" width="20"></i>
                            <span>Master Category</span>
                        </a>
                    </li>
                    <li class='sidebar-title'>Vendor</li>
                    <li class="sidebar-item  ">
                        <a href="#" class='sidebar-link'>
                            <i data-feather="layers" width="20"></i>
                            <span>Create Vendor</span>
                        </a>
                    </li>
                    <li class="sidebar-item  has-sub ">
                        <a href="#" class='sidebar-link'>
                            <i data-feather="clipboard" width="20"></i>
                            <span>List Vendor</span>
                        </a>
                        <ul class="submenu ">
                            @foreach($categories as $category)
                                <li>
                                    <a href="#">{{ $category->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    <li class='sidebar-title'>Jobs</li>
                    <li class="sidebar-item">
                        <a href="#" class='sidebar-link'>
                            <i data-feather="link" width="20"></i>
                            <span>Create Procurement</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class='sidebar-link'>
                            <i data-feather="list" width="20"></i>
                            <span>List Procurement</span>
                        </a>
                    </li>
                    <li class='sidebar-title'>Other</li>
                    <li class="sidebar-item">
                        <a href="#" class='sidebar-link'>
                            <i data-feather="settings" width="20"></i>
                            <span>Settings</span>
                        </a>
                    </li>
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