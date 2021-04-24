<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.portfolio.index') }}" class="brand-link">
        <img
            src="{{ asset('backend/img/logo.png') }}"
            alt="Portfolio Showcase"
            class="brand-image img-circle elevation-3"
            style="opacity: 0.8" />
        <span class="brand-text font-weight-light">Portfolio Showcase</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}"
                    class="img-circle elevation-2"
                    alt="User Image" />
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul
                class="nav nav-pills nav-sidebar flex-column nav-child-indent nav-legacy"
                data-widget="treeview"
                role="menu"
                data-accordion="false">
                <li class="nav-item has-treeview @if(Route::is('admin.user.*') || Route::is('admin.esp.*') || Route::is('admin.pms.*') || Route::is('admin.framework.*')) menu-open @endIf">
                    <a href="#" class="nav-link @if(Route::is('admin.user.*') || Route::is('admin.esp.*') || Route::is('admin.pms.*') || Route::is('admin.framework.*')) active @endIf">
                        <i class="fas fa-table nav-icon"></i>
                        <p>
                            Masters
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview nav-child-indent">
                        <li class="nav-item has-treeview @if(Route::is('admin.user.*')) menu-open @endIf">
                            <a href="#" class="nav-link @if(Route::is('admin.user.*')) active @endIf">
                                <i class="fas fa-users nav-icon"></i>
                                <p>
                                    Users
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.user.index') }}" class="nav-link @if(Route::is('admin.user.index')) active @endIf">
                                        <i class="{{ (Route::is('admin.user.index'))? 'fas': 'far' }} fa-circle nav-icon"></i>
                                        <p>Index</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.user.create') }}" class="nav-link @if(Route::is('admin.user.create')) active @endIf">
                                        <i class="{{ (Route::is('admin.user.create'))? 'fas': 'far' }} fa-circle nav-icon"></i>
                                        <p>Create</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item has-treeview @if(Route::is('admin.esp.*')) menu-open @endIf">
                            <a href="#" class="nav-link @if(Route::is('admin.esp.*')) active @endIf">
                                <i class="fas fa-align-left nav-icon"></i>
                                <p>
                                    ESPs
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.esp.index') }}" class="nav-link @if(Route::is('admin.esp.index')) active @endIf">
                                        <i class="{{ (Route::is('admin.esp.index'))? 'fas': 'far' }} fa-circle nav-icon"></i>
                                        <p>Index</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.esp.create') }}" class="nav-link @if(Route::is('admin.esp.create')) active @endIf">
                                        <i class="{{ (Route::is('admin.esp.create'))? 'fas': 'far' }} fa-circle nav-icon"></i>
                                        <p>Create</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item has-treeview @if(Route::is('admin.pms.*')) menu-open @endIf">
                            <a href="#" class="nav-link @if(Route::is('admin.pms.*')) active @endIf">
                                <i class="fas fa-align-left nav-icon"></i>
                                <p>
                                    PMS
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.pms.index') }}" class="nav-link @if(Route::is('admin.pms.index')) active @endIf">
                                        <i class="{{ (Route::is('admin.pms.index'))? 'fas': 'far' }} fa-circle nav-icon"></i>
                                        <p>Index</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.pms.create') }}" class="nav-link @if(Route::is('admin.pms.create')) active @endIf">
                                        <i class="{{ (Route::is('admin.pms.create'))? 'fas': 'far' }} fa-circle nav-icon"></i>
                                        <p>Create</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item has-treeview @if(Route::is('admin.framework.*')) menu-open @endIf">
                            <a href="#" class="nav-link @if(Route::is('admin.framework.*')) active @endIf">
                                <i class="fas fa-align-left nav-icon"></i>
                                <p>
                                    Framework
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.framework.index') }}" class="nav-link @if(Route::is('admin.framework.index')) active @endIf">
                                        <i class="{{ (Route::is('admin.framework.index'))? 'fas': 'far' }} fa-circle nav-icon"></i>
                                        <p>Index</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.framework.create') }}" class="nav-link @if(Route::is('admin.framework.create')) active @endIf">
                                        <i class="{{ (Route::is('admin.framework.create'))? 'fas': 'far' }} fa-circle nav-icon"></i>
                                        <p>Create</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview @if(Route::is('admin.portfolio.*')) menu-open @endIf">
                    <a href="#" class="nav-link @if(Route::is('admin.portfolio.*')) active @endIf">
                        <i class="fas fa-align-left nav-icon"></i>
                        <p>
                            Portfolios
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.portfolio.index') }}" class="nav-link @if(Route::is('admin.portfolio.index')) active @endIf">
                                <i class="{{ (Route::is('admin.portfolio.index'))? 'fas': 'far' }} fa-circle nav-icon"></i>
                                <p>Index</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.portfolio.create') }}" class="nav-link @if(Route::is('admin.portfolio.create')) active @endIf">
                                <i class="{{ (Route::is('admin.portfolio.create'))? 'fas': 'far' }} fa-circle nav-icon"></i>
                                <p>Create</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- <li class="nav-item">
                    <a href="../widgets.html" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Widgets
                            <span class="right badge badge-danger">New</span>
                        </p>
                    </a>
                </li> --}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>