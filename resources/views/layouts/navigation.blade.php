<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">LM-MAX</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
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
            <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
            </a>
        </li><!-- End Search Icon-->

        {{-- <li class="nav-item dropdown">

            <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">4</span>
            </a><!-- End Notification Icon -->

            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
                You have 4 new notifications
                <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
                <i class="bi bi-exclamation-circle text-warning"></i>
                <div>
                <h4>Lorem Ipsum</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>30 min. ago</p>
                </div>
            </li>

            <li>
                <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
                <i class="bi bi-x-circle text-danger"></i>
                <div>
                <h4>Atque rerum nesciunt</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>1 hr. ago</p>
                </div>
            </li>

            <li>
                <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
                <i class="bi bi-check-circle text-success"></i>
                <div>
                <h4>Sit rerum fuga</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>2 hrs. ago</p>
                </div>
            </li>

            <li>
                <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
                <i class="bi bi-info-circle text-primary"></i>
                <div>
                <h4>Dicta reprehenderit</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>4 hrs. ago</p>
                </div>
            </li>

            <li>
                <hr class="dropdown-divider">
            </li>
            <li class="dropdown-footer">
                <a href="#">Show all notifications</a>
            </li>

            </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav --> --}}

        {{-- <li class="nav-item dropdown">

            <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-chat-left-text"></i>
            <span class="badge bg-success badge-number">3</span>
            </a><!-- End Messages Icon -->

            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
            <li class="dropdown-header">
                You have 3 new messages
                <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>

            <li class="message-item">
                <a href="#">
                <img src="assets/img/messages-1.jpg" alt="" class="rounded-circle">
                <div>
                    <h4>Maria Hudson</h4>
                    <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                    <p>4 hrs. ago</p>
                </div>
                </a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>

            <li class="message-item">
                <a href="#">
                <img src="assets/img/messages-2.jpg" alt="" class="rounded-circle">
                <div>
                    <h4>Anna Nelson</h4>
                    <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                    <p>6 hrs. ago</p>
                </div>
                </a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>

            <li class="message-item">
                <a href="#">
                <img src="assets/img/messages-3.jpg" alt="" class="rounded-circle">
                <div>
                    <h4>David Muldon</h4>
                    <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                    <p>8 hrs. ago</p>
                </div>
                </a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>

            <li class="dropdown-footer">
                <a href="#">Show all messages</a>
            </li>

            </ul><!-- End Messages Dropdown Items -->

        </li><!-- End Messages Nav --> --}}

        <li class="nav-item dropdown pe-3">

            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            {{-- <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> --}}
            <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->name }}</span>
            </a><!-- End Profile Iamge Icon -->

            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
                <h6>{{ Auth::user()->name }}</h6>
                <span>{{ Auth::user()->getRoleNames()->first() }}</span>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>

            <li>
                <a class="dropdown-item d-flex align-items-center" href="{{route('profile.edit')}}">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
                </a>
            </li>
            {{-- <li>
                <hr class="dropdown-divider">
            </li> --}}

            {{-- <li>
                <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
                </a>
            </li> --}}
            {{-- <li>
                <hr class="dropdown-divider">
            </li> --}}

            {{-- <li>
                <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>Need Help?</span>
                </a>
            </li> --}}
            <li>
                <hr class="dropdown-divider">
            </li>

            <li>
                <form method="POST" action="{{ route('logout') }}" class="dropdown-item d-flex align-items-center">
                    @csrf
                    <i class="bi bi-box-arrow-right"></i>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();">
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
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{route('dashboard')}}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        @if (auth()->user()->hasPermissionTo('Coordinator'))
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{route('pages.upload')}}">
                <i class="bi bi-file-earmark-richtext-fill"></i>
                <span>My Courses</span>
            </a>
        </li><!-- End Profile Page Nav -->
        @endif
    
        {{-- @if (auth()->user()->hasPermissionTo('Administrator'))
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{route('pages.uploadFinalExam')}}">
                <i class="bi bi-file-earmark-richtext-fill"></i>
                <span>Final Examinations</span>
            </a>
        </li><!-- End Profile Page Nav -->
        @endif

        @if (auth()->user()->hasPermissionTo('Administrator'))
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{route('pages.uploadFinalExamAndCa')}}">
                <i class="bi bi-file-earmark-richtext-fill"></i>
                <span>Final Examinations and CA</span>
            </a>
        </li><!-- End Profile Page Nav -->
        @endif --}}

        @if (auth()->user()->hasPermissionTo('Registrar'))
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{route('admin.viewCoordinators')}}">
                <i class="bi bi-person-lines-fill"></i>
                <span>All Coordinators</span>
            </a>
        </li><!-- End Profile Page Nav -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{route('admin.viewDeans')}}">
                <i class="bi bi-person-square"></i>
                <span>All Deans</span>
            </a>
        </li><!-- End Profile Page Nav -->
        @endif

        @if (auth()->user()->hasPermissionTo('Administrator'))
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-menu-button-wide"></i>
                <span>Administration</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('users.index') }}">
                        <i class="bi bi-circle"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('roles') }}">
                        <i class="bi bi-circle"></i>
                        <span>Roles</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('permissions') }}">
                        <i class="bi bi-circle"></i>
                        <span>Permissions</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.auditTrails') }}">
                        <i class="bi bi-circle"></i>
                        <span>Audit Trails</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('caAssessmentTypes') }}">
                        <i class="bi bi-circle"></i>
                        <span>CA Type Management</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('courseComponents') }}">
                        <i class="bi bi-circle"></i>
                        <span>Course Components</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.index') }}">
                        <i class="bi bi-gear"></i>
                        <span>Application Settings</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Components Nav -->
        @endif

        @if (auth()->user()->hasPermissionTo('Dean') && (!auth()->user()->hasPermissionTo('Registrar') || !auth()->user()->hasPermissionTo('Administrator')))
        @php
            $results = \App\Models\User::where('id', auth()->user()->id)->first();
            $schoolId = $results->school_id;
        @endphp
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{route('admin.viewCoordinatorsUnderDean', ['schoolId' => encrypt($schoolId)])}}">
                <i class="bi bi-person"></i>
                <span>Course Coordinators</span>
            </a>
        </li><!-- End Profile Page Nav -->
        @endif

        {{-- <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-journal-text"></i>
                <span>Registrar</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="forms-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{route('admin.viewDeans')}}">
                        <i class="bi bi-circle"></i>
                        <span>All Deans</span>
                    </a>
                </li>
                <li>
                    <a href="{{route('admin.viewCoordinators')}}">
                        <i class="bi bi-circle"></i>
                        <span>All Coordinators</span>
                    </a>
                </li>
            </ul>
        </li><!-- End Forms Nav --> --}}
    </ul>
</aside><!-- End Sidebar -->

{{-- <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
                @if (auth()->user()->hasPermissionTo('Coordinator'))
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('pages.upload')" :active="request()->routeIs('pages.upload')">
                            {{ __('My Courses') }}
                        </x-nav-link>
                    </div>               
                @endif
                @if (auth()->user()->hasPermissionTo('Administrator'))
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')">
                            {{ __('Administration') }}
                        </x-nav-link>
                    </div>
                @endif
                @if (auth()->user()->hasPermissionTo('Dean')) 
                    @php
                        $results = \App\Models\User::where('id', auth()->user()->id)->first();
                        $schoolId = $results->school_id;
                    @endphp                   
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('admin.viewCoordinatorsUnderDean', ['schoolId' => encrypt($schoolId)])" :active="request()->routeIs('admin.viewCoordinatorsUnderDean')">
                            {{ __('Course Coordinators') }}
                        </x-nav-link>
                    </div>
                @endif
                @if (auth()->user()->hasPermissionTo('Registrar'))
                    
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('admin.viewCoordinators')" :active="request()->routeIs('admin.viewCoordinators')">
                            {{ __('All Coordinators') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('admin.viewDeans')" :active="request()->routeIs('admin.viewDeans')">
                            {{ __('Deans') }}
                        </x-nav-link>
                    </div>
                @endif
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav> --}}

<style>
/* Ensure the autocomplete suggestions appear on top */
.ui-autocomplete {
    z-index: 1050;
    position: absolute;
    background-color: white;
    border: 1px solid #ccc;
    width: 10%;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Style individual autocomplete items */
.ui-menu-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #f1f1f1;
}

.ui-menu-item:hover {
    background-color: #f9f9f9;
}

/* Ensure the container of the search bar is positioned correctly */
.search-bar {
    position: relative;
    z-index: 1000; /* Lower than the autocomplete but higher than the surrounding elements */
}
</style>

<script>
$(document).ready(function() {
    $("#search").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{ route('coordinator.searchForStudents') }}",
                type: "GET",
                data: { term: request.term },
                success: function(data) {
                    response($.map(data, function(item) {
                        return {
                            label: item.label,  // Display label in the dropdown
                            value: item.value,  // Set value when selected
                            id: item.id        // Use this for form submission
                        };
                    }));
                }
            });
        },
        minLength: 1,
        select: function(event, ui) {
            // Create a dynamic form with method GET to the specific route
            var form = $('<form>', {
                'method': 'GET',
                'action': "{{ route('docket.studentsCAResults') }}" // The route you want to lead to
            });

            // Add CSRF token as a hidden input
            form.append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': $('meta[name="csrf-token"]').attr('content') // Assuming you have a meta tag for CSRF token
            }));

            // Add the student ID as a hidden input
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'studentId',
                'value': ui.item.id // Student ID from the selected item
            }));

            // Append the form to the body
            $('body').append(form);

            // Submit the form
            form.submit();
        }
    }).autocomplete("instance")._renderItem = function(ul, item) {
        // Customize the appearance of the predicted results
        return $("<li>")
            .append("<div class='d-flex bd-highlight'><a href='#'>" + item.label + "</a></div>")
            .appendTo(ul);
    };
});
</script>
