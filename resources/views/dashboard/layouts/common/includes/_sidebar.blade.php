<!-- { navigation menu } start -->
<aside class="app-sidebar app-light-sidebar">
    <div class="app-navbar-wrapper">
        <div class="brand-link brand-logo">
            <a href="{{ guard_dashboard_route() }}" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <img src="{{-- $logo --}}" alt="" class="logo logo-lg" width="223" height="35" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="app-navbar">
                <li class="nav-item {{ is_active('admin.dashboard') }}">
                    <a href="{{ guard_dashboard_route() }}" class="nav-link">
                        <span class="nav-icon">
                            <i class="ti ti-layout-2"></i>
                        </span>
                        <span class="nav-text">{{trans('dashboard/header.main_dashboard') }}</span>

                    </a>
                </li>
                <!-- Start AdminPanelSetting -->
                <li class="nav-item nav-hasmenu
                    {{-- is_open([
                                'admin.mainSettings.index',
                            ]) --}}
                ">
                    <a href="#!" class="nav-link">
                        <span class="nav-icon"><i class="ti ti-layout-2"></i></span>
                        <span class="nav-text">{{ trans('dashboard/sidebar.admin_main_settings_sidebar_title') }}</span>
                        <span class="nav-arrow"><i data-feather="{{ chevron_direction() }}"></i></span>
                    </a>
                    <ul class="nav-submenu">
                        <!-- Main Settings -->
                        <li class="nav-item">
                            <a class="nav-link {{-- is_active('admin.mainSettings.index') --}}"
                                href="{{-- route('admin.mainSettings.index') --}}">
                                {{ trans('dashboard/sidebar.main_settings_sidebar_title') }}
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- End AdminPanelSetting -->
                









            </ul>
        </div>
    </div>
</aside>
<!-- { navigation menu } end -->
