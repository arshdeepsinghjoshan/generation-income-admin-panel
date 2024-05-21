@php
use App\Models\User;
$segment1 = request()->segment(1);
$segment2 = request()->segment(2);
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class=" demo menu-text fw-bolder ms-2">
                <h2> {{ ucfirst(env('APP_NAME')) }} </h2>
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ $segment1 != 'dashboard' ? '' : 'active open' }}">
            <a href="{{ url('/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <!-- User Managment -->
        <li class="menu-item {{ $segment1 != 'user' ? '' : 'active open' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">User Management</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ $segment1 != 'user' ? '' : 'active' }}">
                    <a href="{{ url('user') }}" class="menu-link">
                        <div data-i18n="Without menu">Users</div>
                    </a>
                </li>


            </ul>
        </li>
        <!--End User Managment -->

        @if (User::isAdmin())
        <!--Wallet Managment -->
        <li class="menu-item {{ $segment1 != 'wallet' ? '' : 'active open' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Wallet Management">Wallet Management</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ $segment1 != 'wallet' ? '' : 'active' }}">
                    <a href="{{ url('wallet') }}" class="menu-link">
                        <div data-i18n="Account">Wallets</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif
        <!--End Wallet Managment -->



        <!-- Subscription Managment -->

        <li class="menu-header small text-uppercase"><span class="menu-header-text">Subscription</span></li>
        <li class="menu-item {{ $segment2 != 'plan' ? '' : 'active' }}">
            <a href="{{ url('subscription/plan/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Plans</div>
            </a>
        </li>

        <li class="menu-item {{ $segment2 != 'subscribed-plan' ? '' : 'active' }}">
            <a href="{{ url('subscription/subscribed-plan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Basic">Subscribed Plans</div>
            </a>
        </li>

        <!--End Subscription Managment -->


        <li class="menu-header small text-uppercase"><span class="menu-header-text">Income</span></li>
        <li class="menu-item {{ $segment1 != 'user' ? '' : 'active open' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Income Management</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ $segment1 != 'user' ? '' : 'active' }}">
                    <a href="{{ url('user') }}" class="menu-link">
                        <div data-i18n="Without menu">Users</div>
                    </a>
                </li>


            </ul>
        </li>
    </ul>
</aside>