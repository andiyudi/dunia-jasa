<div id="sidebar" class='active'>
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <img src="{{ asset('') }}cms/assets/images/logo.svg" alt="" srcset="">
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class='sidebar-title'>Main Menu</li>
                    <li class="sidebar-item">
                        <a href="{{ route('dashboard') }}" class='sidebar-link'>
                            <i data-feather="home" width="20"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    @if(auth()->user()->is_admin)
                        <li class='sidebar-title'>Master Data</li>
                        <li class="sidebar-item">
                            <a href="{{ route('category.index') }}" class='sidebar-link'>
                                <i data-feather="menu" width="20"></i>
                                <span>Master Category</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('type.index') }}" class='sidebar-link'>
                                <i data-feather="list" width="20"></i>
                                <span>Master Type</span>
                            </a>
                        </li>
                    @endif
                    <li class='sidebar-title'>Vendor</li>
                        <li class="sidebar-item">
                            <a href="{{ route('partner.index') }}" class='sidebar-link'>
                                <i data-feather="clipboard" width="20"></i>
                                <span>Company Account</span>
                            </a>
                        </li>
                    <li class='sidebar-title'>Jobs</li>
                    <li class="sidebar-item">
                        <a href="{{ route('tender.index') }}" class='sidebar-link'>
                            <i data-feather="more-vertical" width="20"></i>
                            <span>Inquiry</span>
                        </a>
                        <a href="{{ route('quotation.index') }}" class='sidebar-link'>
                            <i data-feather="more-horizontal" width="20"></i>
                            <span>Submit Quotation</span>
                        </a>
                    </li>
                    <li class='sidebar-title'>Other</li>
                    <li class="sidebar-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class='sidebar-link'>
                                <i data-feather="log-out" width="20"></i>
                                <span>Log Out</span>
                            </a>
                        </form>
                    </li>
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>
