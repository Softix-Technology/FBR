@php
    $user = auth()->user();
    $companyLogo = optional($user?->company)->logo;
    $companyLogoUrl = $companyLogo
        ? route('company.logo.file', ['filename' => $companyLogo])
        : asset('assets/images/users/avatar-1.jpg');
@endphp

<div class="leftside-menu">

    <!-- Logo -->
    <a href="index.html" class="logo logo-light">
        <span class="logo-lg">
            <img src="{{ asset('assets/images/logo.png') }}" alt="logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo">
        </span>
    </a>

    <a href="index.html" class="logo logo-dark">
        <span class="logo-lg">
            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="dark logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('assets/images/logo-dark-sm.png') }}" alt="small logo">
        </span>
    </a>

    <!-- Sidebar -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>

        <!-- User -->
        <div class="leftbar-user flex flex-col items-center justify-center py-4">
            <a href="{{ route('company.logo.edit') }}" class="flex flex-col items-center" title="Change Company Logo">
                <img src="{{ $companyLogoUrl }}"
                     alt="{{ $user->name ?? 'Company Logo' }}"
                     class="rounded-full shadow-sm border-2 border-gray-200 mb-2 mx-auto"
                     style="width:52px;height:52px;object-fit:cover;"
                     height="52" width="52">
                <span class="text-xs font-semibold">
                    {{ $user->name ?? 'Guest' }}
                </span>
            </a>
        </div>

        <!-- Menu -->
        <ul class="side-nav">

            <li class="side-nav-title">Apps</li>

            {{-- ADMIN MENU --}}
            @if ($user?->is_admin == 1)

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#reportsMenu" class="side-nav-link">
                        <i class="uil-window"></i>
                        <span>Reports</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="reportsMenu">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="{{ route('reports.sales') }}">Sales Reports</a>
                            </li>
                            <li>
                                <a href="{{ route('reports.purchase') }}">Purchase Reports</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#salesMenu" class="side-nav-link">
                        <i class="uil-envelope"></i>
                        <span>Sales</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="salesMenu">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="{{ route('invoicing.index') }}">Sale Invoice</a>
                            </li>
                            <li>
                                <a href="{{ route('drafts.index') }}">Draft Invoices</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#purchaseMenu" class="side-nav-link">
                        <i class="uil-shopping-cart-alt"></i>
                        <span>Purchase</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="purchaseMenu">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="{{ route('purchase.invoicing.index') }}">Purchase Invoice</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="side-nav-item">
                    <a href="{{ route('company.logo.edit') }}" class="side-nav-link">
                        <i class="uil-image-upload"></i>
                        <span>Company Logo</span>
                    </a>
                </li>

                @if ($user?->company?->can_change_password)
                    <li class="side-nav-item">
                        <a href="{{ route('password.change.edit') }}" class="side-nav-link">
                            <i class="uil-lock-alt"></i>
                            <span>Change Password</span>
                        </a>
                    </li>
                @endif

        


            {{-- USER MENU --}}
            @elseif ($user?->is_admin == 3)

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#accountsMenu" class="side-nav-link">
                        <i class="uil-window"></i>
                        <span>Accounts</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="accountsMenu">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="{{ route('premiertax.companies.index') }}">Company</a>
                            </li>
                            <li>
                                <a href="{{ route('users.index') }}">Users</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="side-nav-item">
                    <a href="{{ route('advance_features.index') }}" class="side-nav-link">
                        <i class="uil-sliders-v-alt"></i>
                        <span>Advance Feature</span>
                    </a>
                </li>

            @endif

        </ul>
    </div>
</div>