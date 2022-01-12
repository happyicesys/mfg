<!-- Sidebar -->
<nav id="sidebar" class="sidebar-wrapper" >
  <div class="sidebar-content">
    <div class="sidebar-brand" style="background-color:#F5F5F5; border:solid black 1px;">
      <a href="/" class="text-center">
        <img src="/img/logo.png" height="100" width="100">
      </a>
      <div id="close-sidebar">
        <i class="far fa-arrow-alt-circle-left"></i>
      </div>
    </div>

    @php
        $name = \Illuminate\Support\Facades\Route::currentRouteName();
            /** @var \App\Models\User $user */
            $user = \Illuminate\Support\Facades\Auth::user();
    @endphp

    <div class="sidebar-menu">
      <ul>
        <li class="header-menu pb-3">
          <span class="float-left" style="color: white;">
            <i class="fas fa-sign-in-alt"></i>
            Logged in as {{ Auth::user()->name }}
          </span>
          <button class="btn btn-outline-danger btn-block" role="button" href="{{ route('logout') }}"
          onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
           {{ __('Logout') }}
          </button>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
          </form>
        </li>

        <li class="{{ $name == 'home' ? 'active' : '' }}">
          <a href="{{ route('home') }}"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        </li>

        <li class="header-menu">
          <span>Profile Management</span>
        </li>
{{--
        <li class="{{ $name == 'customer' ? 'active' : '' }}">
            <a href="{{ route('customer') }}"><i class="fas fa-users"></i>Customers</a>
        </li> --}}
        @can('profile-access')
        <li class="{{ $name == 'profile' ? 'active' : '' }}">
          <a href="{{ route('profile') }}"><i class="far fa-user-circle"></i>Company Profile</a>
        </li>
        @endcan

        @can('admin-access')
        <li class="{{ $name == 'admin' ? 'active' : '' }}">
          <a href="{{ route('admin') }}"><i class="fas fa-users-cog"></i>Users</a>
        </li>
        @endcan

{{--
        <li class="sidebar-dropdown">
          <a href="#">
            <i class="fas fa-users-cog"></i>
            <span>Admin Setting</span>
          </a>
          <div class="sidebar-submenu">
            <ul>
              <li class="{{ $name == 'admin' ? 'active' : '' }}">
                <a href="{{ route('admin') }}"> Admin</a>
              </li>
            </ul>
          </div>
        </li> --}}

{{--
        <li class="header-menu">
          <span>Reporting</span>
        </li> --}}

        <li class="header-menu">
          <span>VM Mfg Operations</span>
        </li>
        @can('vmmfg-ops-access')
        <li class="{{ $name == 'vmmfg-ops' ? 'active' : '' }}">
          <a href="{{ route('vmmfg-ops') }}"><i class="fas fa-wrench"></i>QA/QC</a>
        </li>
        <li class="{{ $name == 'vmmfg-ops-progress' ? 'active' : '' }}">
          <a href="{{ route('vmmfg-ops-progress') }}"><i class="fas fa-tasks"></i>Progress</a>
        </li>
        <li class="{{ $name == 'vmmfg-ops-dailyreport' ? 'active' : '' }}">
          <a href="{{ route('vmmfg-ops-dailyreport') }}"><i class="far fa-list-alt"></i>Daily Report</a>
        </li>

        <li class="{{ $name == 'vmmfg-report' ? 'active' : '' }}">
          <a href="{{ route('vmmfg-report') }}"><i class="far fa-file-excel"></i>Excel Report</a>
        </li>
        @endcan


        @can('vmmfg-setting-access')
        <li class="header-menu">
          <span>VM Mfg Settings</span>
        </li>
        <li class="{{ $name == 'vmmfg-setting-job' ? 'active' : '' }}">
          <a href="{{ route('vmmfg-setting-job') }}"><i class="fas fa-cog"></i>Jobs</a>
        </li>
        <li class="{{ $name == 'vmmfg-setting-unit' ? 'active' : '' }}">
          <a href="{{ route('vmmfg-setting-unit') }}"><i class="fas fa-cog"></i>Units</a>
        </li>
        <li class="{{ $name == 'vmmfg-setting-scope' ? 'active' : '' }}">
          <a href="{{ route('vmmfg-setting-scope') }}"><i class="fas fa-cog"></i>Scopes</a>
        </li>
        @endcan

        @can('vmmfg-setting-access')
        <li class="header-menu">
          <span>VM Mfg Inventory</span>
        </li>
        <li class="{{ $name == 'vmmfg-bom' ? 'active' : '' }}">
          <a href="{{ route('vmmfg-bom') }}"><i class="fas fa-clipboard-list"></i>BOM</a>
        </li>
        <li class="{{ $name == 'vmmfg-bom-inventory' ? 'active' : '' }}">
          <a href="{{ route('vmmfg-bom-inventory') }}"><i class="fas fa-clipboard-list"></i>Inventory</a>
        </li>
        @endcan
{{--
        <li class="{{ $name == 'app-setting' ? 'active' : '' }}">
          <a href="{{ route('app-setting') }}"><i class="fas fa-mobile-alt"></i>App Setting</a>
        </li>

        <li class="{{ $name == 'product' ? 'active' : '' }}">
          <a href="{{ route('product') }}"><i class="fas fa-layer-group"></i>Products</a>
        </li> --}}
{{--
        <li class="{{ $name == 'voucher.index' ? 'active' : '' }}">
          <a href="{{ route('voucher.index') }}"><i class="fas fa-ticket-alt"></i>Voucher</a>
        </li> --}}
        @can('self-access')
        <li class="header-menu">
          <span>Self Setting</span>
        </li>

        <li class="{{ $name == 'self-setting' ? 'active' : '' }}">
          <a href="{{ route('self-setting') }}">
            <i class="fas fa-user-circle"></i>
            User Account
          </a>
        </li>
        @endcan

      </ul>
    </div>
  </div>
</nav>
