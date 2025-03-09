<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center">
            <img src="{{ asset('assets/img/logo.png') }}" alt="">
            <span class="d-none d-lg-block">LM-MAX</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn" @click="sidebarOpen = !sidebarOpen"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="POST" action="#">
            <input id="search" type="text" name="query" placeholder="Enter student number.." title="Search for student">
            <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle" href="#">
                    <i class="bi bi-search"></i>
                </a>
            </li><!-- End Search Icon-->

            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->name }}</span>
                </a><!-- End Profile Image Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6>{{ Auth::user()->name }}</h6>
                        <span>{{ Auth::user()->getRoleNames()->first() }}</span>
                    </li>
                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{route('profile.edit')}}">
                            <i class="bi bi-person"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </form>
                    </li>
                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->
        </ul>
    </nav><!-- End Icons Navigation -->
</header>

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar" :class="{'active': sidebarOpen}">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? '' : 'collapsed' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>

        @if (auth()->user()->hasPermissionTo('Coordinator'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('coordinator.*') || request()->routeIs('pages.*') ? '' : 'collapsed' }}" data-bs-target="#coordinator-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-menu-button-wide"></i><span>Coordinator</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="coordinator-nav" class="nav-content collapse {{ request()->routeIs('coordinator.*') || request()->routeIs('pages.*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('pages.upload') }}" class="{{ request()->routeIs('pages.upload') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Upload CA</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pages.uploadFinalExam') }}" class="{{ request()->routeIs('pages.uploadFinalExam') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Upload Final Exam</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pages.uploadFinalExamAndCa') }}" class="{{ request()->routeIs('pages.uploadFinalExamAndCa') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Upload CA & Final Exam</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if (auth()->user()->hasPermissionTo('Registrar'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.*') ? '' : 'collapsed' }}" data-bs-target="#registrar-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-journal-text"></i><span>Registrar</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="registrar-nav" class="nav-content collapse {{ request()->routeIs('admin.*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('admin.viewDeans') }}" class="{{ request()->routeIs('admin.viewDeans') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>View Deans</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.viewCoordinators') }}" class="{{ request()->routeIs('admin.viewCoordinators') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>View Coordinators</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if (auth()->user()->hasPermissionTo('Administrator'))
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.*') ? '' : 'collapsed' }}" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-layout-text-window-reverse"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="admin-nav" class="nav-content collapse {{ request()->routeIs('admin.*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Users</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        <li class="nav-heading">Pages</li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{ route('profile.edit') }}">
                <i class="bi bi-person"></i>
                <span>Profile</span>
            </a>
        </li>
    </ul>
</aside><!-- End Sidebar -->

<style>
/* Ensure the autocomplete suggestions appear on top */
.ui-autocomplete {
    z-index: 9999 !important;
}

/* Sidebar active state */
.sidebar.active {
    left: 0;
}

/* Responsive sidebar */
@media (max-width: 1199px) {
    .sidebar {
        left: -300px;
        transition: all 0.3s;
    }
    
    .sidebar.active {
        left: 0;
    }
}
</style>
