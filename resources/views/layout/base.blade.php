<!DOCTYPE html>
<html lang="en">

<head>
    <link href="{{ asset('assets/src/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> ERMS - Melchia </title>
    <meta name="Description" content="ERMS - Melchia">
    <meta name="Author" content="ERMS - Melchia">
    <meta name="keywords" content="ERMS - Melchia">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/level10.png') }}" type="image/x-icon">

    <!-- Choices JS -->
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

    <!-- Main Theme Js -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap-select.min.css') }}" rel="stylesheet">

    <!-- Style Css -->
    <link href="{{ asset('assets/css/styles.css?t=' . time()) }}" rel="stylesheet">

    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">

    <!-- Node Waves Css -->
    <link href="{{ asset('assets/libs/node-waves/waves.min.css') }}" rel="stylesheet">

    <!-- Simplebar Css -->
    <link href="{{ asset('assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/@simonwep/pickr/themes/nano.min.css') }}">

    <!-- Choices Css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">

    <!-- Auto Complete CSS -->
    <link rel="stylesheet" href="{{ asset('assets/libs/@tarekraafat/autocomplete.js/css/autoComplete.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @yield('page-styles')
</head>

<body>

    @php use Illuminate\Support\Str; @endphp
    <!--*******************
        Preloader start
    ********************-->
    <div id="loader">
        <img src="{{ asset('assets/images/media/loader.svg') }}" alt="">
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div class="page">
        <!-- app-header -->
        <header class="app-header">

            <!-- Start::main-header-container -->
            <div class="main-header-container container-fluid">

                <!-- Start::header-content-left -->
                <div class="header-content-left">

                    <!-- Start::header-element -->
                    <div class="header-element">
                        <div class="horizontal-logo">
                            <a href="{{ route('dashboard.operational') }}" class="header-logo">
                                <img src="{{ asset('assets/images/arms.png') }}" alt="logo" class="desktop-logo">
                                <img src="{{ asset('assets/images/arms.png') }}" alt="logo" class="toggle-logo">
                                <img src="{{ asset('assets/images/arms.png') }}" alt="logo" class="desktop-white">
                                <img src="{{ asset('assets/images/arms.png') }}" alt="logo" class="toggle-white">
                            </a>
                        </div>
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element mx-lg-0 mx-2">
                        <a aria-label="Hide Sidebar"
                            class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle"
                            data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a>
                    </div>

                    @auth
                    @if (auth()->user()->access_level === 'GOG_Administrator')
                        <div style="text-align: center; margin-top: 20px;margin-left:20px">
                            <p style="font-size: 16px;">GOG Administrator</p>
                        </div>
                    @endif
                    @endauth
                </div>
                <!-- End::header-content-left -->

                <!-- Start::header-content-right -->
                <div class="header-content-right">

                    <!-- Start::header-element -->
                    <div class="header-element d-lg-none d-flex">
                        <a href="javascript:void(0);" class="header-link" data-bs-toggle="modal"
                            data-bs-target="#responsive-searchModal">
                            <!-- Start::header-link-icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon" height="24px"
                                viewBox="0 -960 960 960" width="24px" fill="#5f6368">
                                <path
                                    d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z" />
                            </svg>
                            <!-- End::header-link-icon -->
                        </a>
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->

                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element header-theme-mode">
                        <!-- Start::header-link|layout-setting -->
                        <a href="javascript:void(0);" class="header-link layout-setting">
                            <span class="light-layout">
                                <!-- Start::header-link-icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon"
                                    enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px"
                                    fill="#5f6368">
                                    <rect fill="none" height="24" width="24" />
                                    <path
                                        d="M9.37,5.51C9.19,6.15,9.1,6.82,9.1,7.5c0,4.08,3.32,7.4,7.4,7.4c0.68,0,1.35-0.09,1.99-0.27C17.45,17.19,14.93,19,12,19 c-3.86,0-7-3.14-7-7C5,9.07,6.81,6.55,9.37,5.51z M12,3c-4.97,0-9,4.03-9,9s4.03,9,9,9s9-4.03,9-9c0-0.46-0.04-0.92-0.1-1.36 c-0.98,1.37-2.58,2.26-4.4,2.26c-2.98,0-5.4-2.42-5.4-5.4c0-1.81,0.89-3.42,2.26-4.4C12.92,3.04,12.46,3,12,3L12,3z" />
                                </svg>
                                <!-- End::header-link-icon -->
                            </span>
                            <span class="dark-layout">
                                <!-- Start::header-link-icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon" height="24px"
                                    viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                    <path
                                        d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79zM1 10.5h3v2H1zM11 .55h2V3.5h-2zm8.04 2.495l1.408 1.407-1.79 1.79-1.407-1.408zm-1.8 15.115l1.79 1.8 1.41-1.41-1.8-1.79zM20 10.5h3v2h-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm-1 4h2v2.95h-2zm-7.45-.96l1.41 1.41 1.79-1.8-1.41-1.41z" />
                                </svg>
                                <!-- End::header-link-icon -->
                            </span>
                        </a>
                        <!-- End::header-link|layout-setting -->
                    </div>
                    <!-- End::header-element -->


                    <!-- Start::header-element -->
                    <div class="header-element header-shortcuts-dropdown dropdown">
                        <!-- Start::header-link|dropdown-toggle -->
                        <a href="javascript:void(0);" class="header-link dropdown-toggle" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" id="notificationDropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon"
                                enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px"
                                fill="#5f6368">
                                <g>
                                    <rect fill="none" height="24" width="24" />
                                </g>
                                <g>
                                    <g>
                                        <g>
                                            <path
                                                d="M3,3v8h8V3H3z M9,9H5V5h4V9z M3,13v8h8v-8H3z M9,19H5v-4h4V19z M13,3v8h8V3H13z M19,9h-4V5h4V9z M13,13v8h8v-8H13z M19,19h-4v-4h4V19z" />
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </a>
                        <!-- End::header-link|dropdown-toggle -->
                        <!-- Start::main-header-dropdown -->
                        <div class="main-header-dropdown header-shortcuts-dropdown dropdown-menu pb-0 dropdown-menu-end"
                            aria-labelledby="notificationDropdown">
                            <div class="p-3 bg-light bg-opacity-75">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0 fw-semibold">Current Time </p>
                                    <span class="badge bg-pink"> -
                                        {{ \Carbon\Carbon::now()->format('l, F j, Y  ') }}</span>
                                </div>
                            </div>
                            <div class="dropdown-divider mb-0"></div>
                            <div class="main-header-shortcuts p-3" id="header-shortcut-scroll">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <a href="javascript:void(0);" class="related-apps">
                                            <div
                                                class="text-center p-3 related-app pink bg-pink-transparent border border-pink border-opacity-10">
                                                <span
                                                    class="avatar avatar-md avatar-rounded bg-pink bg-opacity-10 border border-pink border-opacity-10 p-2 mb-2">
                                                    <img src="{{ asset('assets/images/arms.png') }}" alt="">
                                                </span>
                                                <span class="d-block fs-12">Pay Bill </span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="javascript:void(0);" class="related-apps">
                                            <div
                                                class="text-center p-3 related-app success bg-success-transparent border border-success border-opacity-10">
                                                <span
                                                    class="avatar avatar-md avatar-rounded bg-success bg-opacity-10 border border-success border-opacity-10 p-2 mb-2">
                                                    <img src="{{ asset('assets/images/arms.png') }}" alt="">
                                                </span>
                                                <span class="d-block fs-12">View Bills</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="javascript:void(0);" class="related-apps">
                                            <div
                                                class="text-center p-3 related-app primary bg-primary-transparent border border-primary border-opacity-10">
                                                <span
                                                    class="avatar avatar-md avatar-rounded bg-primary bg-opacity-10 border border-primary border-opacity-10 p-2 mb-2">
                                                    <img src="{{ asset('assets/images/arms.png') }}" alt="">
                                                </span>
                                                <span class="d-block fs-12">Dashboard</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="javascript:void(0);" class="related-apps">
                                            <div
                                                class="text-center p-3 related-app info bg-info-transparent border border-info border-opacity-10">
                                                <span
                                                    class="avatar avatar-md avatar-rounded bg-info bg-opacity-10 border border-info border-opacity-10 p-2 mb-2">
                                                    <img src="{{ asset('assets/images/arms.png') }}" alt="">
                                                </span>
                                                <span class="d-block fs-12">Rate Payers</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="javascript:void(0);" class="related-apps">
                                            <div
                                                class="text-center p-3 related-app secondary bg-secondary-transparent border border-secondary border-opacity-10">
                                                <span
                                                    class="avatar avatar-md avatar-rounded bg-secondary bg-opacity-10 border border-secondary border-opacity-10 p-2 mb-2">
                                                    <img src="{{ asset('assets/images/arms.png') }}" alt="">
                                                </span>
                                                <span class="d-block fs-12">Properties</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="javascript:void(0);" class="related-apps">
                                            <div
                                                class="text-center p-3 related-app danger bg-danger-transparent border border-danger border-opacity-10">
                                                <span
                                                    class="avatar avatar-md avatar-rounded bg-danger bg-opacity-10 border border-danger border-opacity-10 p-2 mb-2">
                                                    <img src="{{ asset('assets/images/arms.png') }}" alt="">
                                                </span>
                                                <span class="d-block fs-12">Fee Fixing</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3 border-top">
                                <div class="d-grid">
                                    <a href="{{ route('dashboard.operational') }}" class="btn btn-primary">Access
                                        Dashboard</a>
                                </div>
                            </div>
                        </div>
                        <!-- End::main-header-dropdown -->
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element header-fullscreen">
                        <!-- Start::header-link -->
                        <a onclick="openFullscreen();" href="javascript:void(0);" class="header-link">
                            <svg xmlns="http://www.w3.org/2000/svg" class="full-screen-open header-link-icon"
                                height="24px" viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                <path d="M0 0h24v24H0V0z" fill="none" />
                                <path
                                    d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="full-screen-close header-link-icon d-none"
                                height="24px" viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                <path d="M0 0h24v24H0V0z" fill="none" />
                                <path
                                    d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z" />
                            </svg>
                        </a>
                        <!-- End::header-link -->
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element dropdown">
                        <!-- Start::header-link|dropdown-toggle -->
                        <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <span class="avatar avatar-sm avatar-rounded">
                                @if (auth()->check() && auth()->user()->access_level == 'Super_User')
                                    <img src="{{ asset('assets/images/arms.png') }}" alt="img"
                                        class="img-fluid avatar-img">
                                @elseif(auth()->check() && in_array(auth()->user()->access_level, ['Assembly_Agent', 'Assembly_Supervisor', 'Assembly_Administrator']))
                                    <img src="{{ asset('assets/images/arms.png') }}" alt="img"
                                        class="img-fluid avatar-img">
                                @elseif(auth()->check() && auth()->user()->access_level == 'GOG_Administrator')
                                    <img src="{{ asset('assets/images/arms.png') }}" alt="img"
                                        class="img-fluid avatar-img">
                                @else
                                    <img src="{{ asset('assets/images/arms.png') }}" alt="img"
                                        class="img-fluid avatar-img">
                                @endif
                            </span>
                        </a>

                        <!-- End::header-link|dropdown-toggle -->
                        <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
                            aria-labelledby="mainHeaderProfile">
                            <li class="p-3 bg-light bg-opacity-75 border-bottom">
                                <div class="d-flex align-items-center justify-content-between gap-4">
                                    <div>
                                        <p class="mb-0 fw-semibold lh-1">
                                        <h6>{{ auth()->user()->name ?? '' }}</h6>
                                        </p>
                                        <span class="fs-11 text-muted">
                                            {{ Str::of(auth()->user()->access_level ?? '')->replace('_', ' ')->title() }}
                                        </span>
                                    </div>
                                    <span
                                        class="badge bg-pink align-self-end mb-1">{{ auth()->user()->phone ?? '' }}</span>
                                </div>
                            </li>

                            @auth
                            @if (auth()->user()->access_level === 'customer')
                                <li>
                                    <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('dashboard.myprofile') }}">
                                        <i class="ti ti-user fs-18 me-2 text-gray fw-normal"></i>Profile
                                    </a>
                                </li>
                            @endif
                            @endauth

                            <li>
                                <a class="dropdown-item d-flex align-items-center"
                                    href="{{ route('auth.logout') }}"><i
                                        class="ti ti-logout fs-18 me-2 text-gray fw-normal"></i>Sign Out
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    {{-- <div class="header-element">
                        <!-- Start::header-link|switcher-icon -->
                        <a href="javascript:void(0);" class="header-link switcher-icon" data-bs-toggle="offcanvas"
                            data-bs-target="#switcher-canvas">
                            <svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon" height="24px"
                                viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                <path d="M0 0h24v24H0V0z" fill="none" />
                                <path
                                    d="M19.43 12.98c.04-.32.07-.64.07-.98 0-.34-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.09-.16-.26-.25-.44-.25-.06 0-.12.01-.17.03l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.06-.02-.12-.03-.18-.03-.17 0-.34.09-.43.25l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98 0 .33.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.09.16.26.25.44.25.06 0 .12-.01.17-.03l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.06.02.12.03.18.03.17 0 .34-.09.43-.25l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zm-1.98-1.71c.04.31.05.52.05.73 0 .21-.02.43-.05.73l-.14 1.13.89.7 1.08.84-.7 1.21-1.27-.51-1.04-.42-.9.68c-.43.32-.84.56-1.25.73l-1.06.43-.16 1.13-.2 1.35h-1.4l-.19-1.35-.16-1.13-1.06-.43c-.43-.18-.83-.41-1.23-.71l-.91-.7-1.06.43-1.27.51-.7-1.21 1.08-.84.89-.7-.14-1.13c-.03-.31-.05-.54-.05-.74s.02-.43.05-.73l.14-1.13-.89-.7-1.08-.84.7-1.21 1.27.51 1.04.42.9-.68c.43-.32.84-.56 1.25-.73l1.06-.43.16-1.13.2-1.35h1.39l.19 1.35.16 1.13 1.06.43c.43.18.83.41 1.23.71l.91.7 1.06-.43 1.27-.51.7 1.21-1.07.85-.89.7.14 1.13zM12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 6c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z" />
                            </svg>
                        </a>
                        <!-- End::header-link|switcher-icon -->
                    </div> --}}
                    <!-- End::header-element -->

                </div>
                <!-- End::header-content-right -->

            </div>
            <!-- End::main-header-container -->

        </header>
        <!-- /app-header -->
        <!-- Start::app-sidebar -->
        <aside class="app-sidebar sticky" id="sidebar">

            <!-- Start::main-sidebar-header -->
            <div class="main-sidebar-header">
                <a href="{{ route('dashboard.operational') }}" class="header-logo">
                    <img src="{{ asset('assets/images/arms.png') }}" alt="logo" class="desktop-logo">
                    <img src="{{ asset('assets/images/arms.png') }}" alt="logo" class="toggle-logo">
                    <img src="{{ asset('assets/images/arms.png') }}" alt="logo" class="desktop-white">
                    <img src="{{ asset('assets/images/arms.png') }}" alt="logo" class="toggle-white">
                </a>
            </div>
            <!-- End::main-sidebar-header -->

            <!-- Start::main-sidebar -->
            <div class="main-sidebar" id="sidebar-scroll">

                <!-- Start::nav -->
                <nav class="main-menu-container nav nav-pills flex-column sub-open">
                    <div class="slide-left" id="slide-left">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                        </svg>
                    </div>
                    <ul class="main-menu">
                        <!-- Start::slide__category -->
                        {{-- <li class="slide__category"><span class="category-name">Executive Summary</span></li> --}}
                        <!-- End::slide__category -->

                        <!-- Start::slide -->
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                    viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                    <path
                                        d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3zm5 15h-2v-6H9v6H7v-7.81l5-4.5 5 4.5V18z" />
                                    <path d="M7 10.19V18h2v-6h6v6h2v-7.81l-5-4.5z" opacity=".3" />
                                </svg>
                                <span class="side-menu__label">Dashboards</span>
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide side-menu__label1">
                                    <a href="{{ route('dashboard.operational') }}">Dashboards</a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('dashboard.operational') }}"
                                        class="side-menu__item">Overview</a>
                                </li>
                            </ul>
                        </li>
                        <!-- End::slide -->

                        <!-- Start::slide -->
                        <!-- End::slide -->
                        @canany(['roles.view', 'roles.create', 'roles.update', 'permissions.view'])
                            <!-- End::slide -->
                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path d="M13 4H6v16h12V9h-5V4zm3 14H8v-2h8v2zm0-6v2H8v-2h8z" opacity=".3" />
                                        <path
                                            d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z" />
                                    </svg>
                                    <span class="side-menu__label">Roles & Permissions</span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">

                                    @canany(['roles.view', 'roles.create', 'roles.update'])
                                        <li class="slide">
                                            <a href="{{ route('roles.index') }}" class="side-menu__item">Roles
                                            </a>
                                        </li>
                                    @endcanany
{{--                                    @can('permissions.view')--}}
{{--                                        <li class="slide">--}}
{{--                                            <a href="{{ route('permissions.index') }}" class="side-menu__item">--}}
{{--                                                Permissions</a>--}}
{{--                                        </li>--}}
{{--                                    @endcan--}}
                                </ul>
                            </li>
                        @endcanany

                        <!-- Start::slide -->
                        @canany(['users.view'])
                            {{-- <li class="slide__category"><span class="category-name">User & Security </span></li> --}}
                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path d="M13 4H6v16h12V9h-5V4zm3 14H8v-2h8v2zm0-6v2H8v-2h8z" opacity=".3" />
                                        <path
                                            d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z" />
                                    </svg>
                                    <span class="side-menu__label">User Mgnt</span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">
                                    @can('users.view')
                                        <li class="slide">
                                            <a href="{{ route('users.index') }}" class="side-menu__item">Users</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcanany
                        <!-- End::slide -->

                        <!-- Start::slide -->
                        @canany(['customer-types.view', 'customers.view'])
                            {{-- <li class="slide__category"><span class="category-name">Rate Payer Mnaagement </span></li> --}}

                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path d="M13 4H6v16h12V9h-5V4zm3 14H8v-2h8v2zm0-6v2H8v-2h8z" opacity=".3" />
                                        <path
                                            d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z" />
                                    </svg>
                                    <span class="side-menu__label">Rate Payer Mgnt</span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">
                                    {{-- @can('customers.view')
                                        <li class="slide">
                                            <a href="{{ route('citizens.index') }}" class="side-menu__item">View Rate Payers
                                            </a>
                                        </li>
                                    @endcan --}}

                                    @can('customers.view')
                                        <li class="slide">
                                            <a href="{{ route('owners.index') }}" class="side-menu__item">View Rate Payers
                                            </a>
                                        </li>
                                    @endcan

                                    {{-- @can('customer-types.view')
                                        <li class="slide">
                                            <a href="{{ route('customer-types.index') }}" class="side-menu__item">View
                                                Rate Payer Types</a>
                                        </li>
                                    @endcan --}}
                                </ul>
                            </li>
                        @endcanany

                        <!-- End::slide -->

                        <!-- Start::slide -->
                        @canany(['properties.view'])
                            {{-- <li class="slide__category"><span class="category-name">Property & Business </span>
                            </li> --}}
                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <g fill="none">
                                            <path d="M0 0h24v24H0V0z" />
                                            <path d="M0 0h24v24H0V0z" opacity=".87" />
                                        </g>
                                        <path d="M6 20h12V10H6v10zm6-7c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"
                                            opacity=".3" />
                                        <path
                                            d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z" />
                                    </svg>
                                    <span class="side-menu__label">Property Mgnt </span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">

                                    {{-- @can('properties.view')
                                        <li class="slide">
                                            <a href="{{ route('properties.index') }}" class="side-menu__item">Properties </a>
                                        </li>
                                    @endcan --}}

                                    @can('properties.view')
                                        <li class="slide">
                                            <a href="{{ route('new-properties.index') }}" class="side-menu__item">Properties
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcanany
                        <!-- End::slide -->

                        @canany(['businesses.view', 'business-classes.view', 'business-types.view'])
                            {{-- <li class="slide__category"><span class="category-name">Property & Business </span>
                            </li> --}}
                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <g fill="none">
                                            <path d="M0 0h24v24H0V0z" />
                                            <path d="M0 0h24v24H0V0z" opacity=".87" />
                                        </g>
                                        <path d="M6 20h12V10H6v10zm6-7c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"
                                            opacity=".3" />
                                        <path
                                            d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z" />
                                    </svg>
                                    <span class="side-menu__label">Business Mgnt </span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">

                                    {{-- @can('businesses.view')
                                        <li class="slide">
                                            <a href="{{ route('businesses.index') }}" class="side-menu__item">Businesses</a>
                                        </li>
                                    @endcan --}}

                                    @can('businesses.view')
                                        <li class="slide">
                                            <a href="{{ route('new-businesses.index') }}"
                                                class="side-menu__item">Businesses</a>
                                        </li>
                                    @endcan

                                    {{-- @can('business-classes.view')
                                        <li class="slide">
                                            <a href="{{ route('business-classes.index') }} " class="side-menu__item">Business
                                                Class</a>
                                        </li>
                                    @endcan --}}

                                    {{-- @can('business-types.view')
                                        <li class="slide">
                                            <a href="{{ route('business-types.index') }} " class="side-menu__item">Business
                                                Type</a>
                                        </li>
                                    @endcan --}}
                                </ul>
                            </li>
                        @endcanany

                        @canany(['properties.view'])
                            {{-- <li class="slide__category"><span class="category-name">Property & Business </span>
                        </li> --}}
                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <g fill="none">
                                            <path d="M0 0h24v24H0V0z" />
                                            <path d="M0 0h24v24H0V0z" opacity=".87" />
                                        </g>
                                        <path d="M6 20h12V10H6v10zm6-7c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"
                                            opacity=".3" />
                                        <path
                                            d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z" />
                                    </svg>
                                    <span class="side-menu__label">Revenue Stream </span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">

                                    @can('properties.view')
                                        <li class="slide">
                                            <a href="{{ route('revenue-stream-types.index') }}"
                                                class="side-menu__item">Revenue
                                                Stream Type</a>
                                        </li>
                                    @endcan

                                    @can('zones.view')
                                        <li class="slide">
                                            <a href="{{ route('zones.index') }}" class="side-menu__item">
                                                Zone Type</a>
                                        </li>
                                    @endcan

                                    @can('zones.view')
                                        <li class="slide">
                                            <a href="{{ route('property-classes.index') }}" class="side-menu__item">
                                                Property Classes</a>
                                        </li>
                                    @endcan

                                    @can('zones.view')
                                        <li class="slide">
                                            <a href="{{ route('property-types.index') }}" class="side-menu__item">
                                                Property Type</a>
                                        </li>
                                    @endcan

                                    @can('zones.view')
                                        <li class="slide">
                                            <a href="{{ route('categories.index') }}" class="side-menu__item">
                                                Category Type</a>
                                        </li>
                                    @endcan

                                    @can('zones.view')
                                        <li class="slide">
                                            <a href="{{ route('item-descriptions.index') }}" class="side-menu__item">
                                                Item Description</a>
                                        </li>
                                    @endcan

                                    @can('zones.view')
                                        <li class="slide">
                                            <a href="{{ route('main-items.index') }}" class="side-menu__item">
                                                Main Item</a>
                                        </li>
                                    @endcan

                                    @can('zones.view')
                                        <li class="slide">
                                            <a href="{{ route('sub-items.index') }}" class="side-menu__item">
                                                Sub Item</a>
                                        </li>
                                    @endcan

                                    @can('properties.view')
                                        <li class="slide">
                                            <a href="{{ route('revenue-streams.index') }}" class="side-menu__item">Revenue
                                                Stream (PR)</a>
                                        </li>
                                    @endcan

                                    @can('properties.view')
                                        <li class="slide">
                                            <a href="{{ route('revenue-streams.indexBOP') }}" class="side-menu__item">Revenue
                                                Stream (BOP)
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcanany

                        <!-- Start::slide -->
                        @canany(['bills.view'])
                            {{-- <li class="slide__category"><span class="category-name">Bills Generation </span></li> --}}
                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path d="M5 5v14h14V5H5zm4 12H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"
                                            opacity=".3" />
                                        <path
                                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z" />
                                    </svg>
                                    <span class="side-menu__label">Bills Mgnt</span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">
                                    @can('bills.view')
                                        <li class="slide">
                                            <a href="{{ route('bills.fetchBill') }} " class="side-menu__item">All Bills</a>
                                        </li>
                                    @endcan
                                    @can('bills.create')
                                        <li class="slide">
                                            <a href="{{ route('bill-adjustments.index') }}" class="side-menu__item">
                                                Bill Adjustment</a>
                                        </li>
                                    @endcan
                                    @can('bills.create')
                                        <li class="slide">
                                            <a href="{{ route('bills.index') }}" class="side-menu__item">Generate Property
                                                Bill</a>
                                        </li>
                                    @endcan
                                    @can('bills.create')
                                        <li class="slide">
                                            <a href="{{ route('bills.bus.index') }}" class="side-menu__item">Generate BoP
                                                Bill</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcanany
                        <!-- End::slide -->

                        @canany(['payments.view'])
                            <!-- Start::slide__category -->
                            {{-- <li class="slide__category"><span class="category-name">Payments and Reports</span></li> --}}
                            <!-- End::slide__category -->

                            <!-- Start::slide -->
                            @can('payments.view')
                                <li class="slide has-sub">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"
                                            enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24"
                                            width="24px" fill="#5f6368">
                                            <g>
                                                <rect fill="none" height="24" width="24" />
                                            </g>
                                            <g>
                                                <g>
                                                    <rect height="4" opacity=".3" width="4" x="5" y="5" />
                                                    <rect height="4" opacity=".3" width="4" x="5" y="15" />
                                                    <rect height="4" opacity=".3" width="4" x="15" y="15" />
                                                    <rect height="4" opacity=".3" width="4" x="15" y="5" />
                                                    <path d="M3,21h8v-8H3V21z M5,15h4v4H5V15z" />
                                                    <path d="M3,11h8V3H3V11z M5,5h4v4H5V5z" />
                                                    <path d="M13,21h8v-8h-8V21z M15,15h4v4h-4V15z" />
                                                    <path d="M13,3v8h8V3H13z M19,9h-4V5h4V9z" />
                                                </g>
                                            </g>
                                        </svg>
                                        <span class="side-menu__label">Payment Mgnt</span>
                                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child1">
                                        <li class="slide">
                                            <a href="{{ route('payments.index') }}" class="side-menu__item">View Payments
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                            <!-- End::slide -->
                        @endcanany

                        <!-- Start::slide -->
                        @canany(['reports.view'])
                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path d="M6.26 9L12 13.47 17.74 9 12 4.53z" opacity=".3" />
                                        <path
                                            d="M19.37 12.8l-7.38 5.74-7.37-5.73L3 14.07l9 7 9-7zM12 2L3 9l1.63 1.27L12 16l7.36-5.73L21 9l-9-7zm0 11.47L6.26 9 12 4.53 17.74 9 12 13.47z" />
                                    </svg>
                                    <span class="side-menu__label">Reports</span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">
                                    @can('reports.view')
                                        <li class="slide">

                                            <a href="{{ route('customer-reports.index') }}" class="side-menu__item">Tax Payer
                                                Report</a>
                                        </li>
                                    @endcan
                                    @can('reports.view')
                                        <li class="slide">
                                            <a href="{{ route('business-reports.index') }}" class="side-menu__item">Business
                                                Report</a>
                                        </li>
                                    @endcan
                                    @can('reports.view')
                                        <li class="slide">
                                            <a href="{{ route('property-reports.index') }}" class="side-menu__item">Property
                                                Report</a>
                                        </li>
                                    @endcan
                                    @can('reports.view')
                                        <li class="slide">
                                            <a href="{{ route('bill-reports.index') }}" class="side-menu__item">Bills
                                                Report</a>
                                        </li>
                                    @endcan
                                    @can('reports.view')
                                        <li class="slide">
                                            <a href="{{ route('payment-reports.index') }}" class="side-menu__item">Payment
                                                Report</a>
                                        </li>
                                    @endcan
                                    {{-- @can('reports.view')
                                        <li class="slide">
                                            <a href="{{ route('payment-history-reports.index') }}"
                                                class="side-menu__item">Payment
                                                History Report</a>
                                        </li>
                                    @endcan --}}
                                    @can('reports.view')
                                        <li class="slide">
                                            <a href=" {{ route('debtors-reports.index') }}"
                                                class="side-menu__item">Outstanding Bills
                                                Report</a>
                                        </li>
                                    @endcan
                                    @can('reports.view')
                                        <li class="slide">
                                            <a href=" {{ route('support-request-reports.index') }}"
                                                class="side-menu__item">Support
                                                Request Report</a>
                                        </li>
                                    @endcan
                                    @can('reports.view')
                                        <li class="slide">
                                            <a href=" {{ route('tax-collection-reports.index') }}"
                                                class="side-menu__item">Tax Collection Summary Report</a>
                                        </li>
                                    @endcan
                                    {{-- @can('reports.view')
                                        <li class="slide">
                                            <a href=" {{ route('revenue-property-type-reports.index') }}"
                                                class="side-menu__item">Revenue By Property Type Report</a>
                                        </li>
                                    @endcan --}}
                                    {{-- @can('reports.view')
                                        <li class="slide">
                                            <a href=" {{ route('revenue-collection-efficiency-reports.index') }}"
                                                class="side-menu__item">Revenue Collection Efficiency Report</a>
                                        </li>
                                    @endcan --}}
                                    @can('reports.view')
                                        <li class="slide">
                                            <a href="{{ route('service-usage-reports.index') }}"
                                                class="side-menu__item">Service Usage Report</a>
                                        </li>
                                    @endcan
                                    @can('reports.view')
                                        <li class="slide">
                                            <a href="{{ route('location-analysis-reports.index') }}"
                                                class="side-menu__item">Location Analysis Report</a>
                                        </li>
                                    @endcan
                                    @can('reports.view')
                                        <li class="slide">
                                            <a href="{{ route('audit-trail-reports.index') }}" class="side-menu__item">Audit
                                                Trail Report</a>
                                        </li>
                                    @endcan
                                    @can('reports.view')
                                        <li class="slide">
                                            <a href="{{ route('job-allocation-reports.index') }}" class="side-menu__item">Job
                                                Allocation Report</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcanany
                        <!-- End::slide -->

                        @canany(['analytics.view'])
                            @if (auth()->user()->access_level === 'Super_User' || auth()->user()->access_level === 'GOG_Administrator')
                                <li class="slide has-sub">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                            viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                            <path d="M0 0h24v24H0V0z" fill="none" />
                                            <path d="M6.26 9L12 13.47 17.74 9 12 4.53z" opacity=".3" />
                                            <path
                                                d="M19.37 12.8l-7.38 5.74-7.37-5.73L3 14.07l9 7 9-7zM12 2L3 9l1.63 1.27L12 16l7.36-5.73L21 9l-9-7zm0 11.47L6.26 9 12 4.53 17.74 9 12 13.47z" />
                                        </svg>
                                        <span class="side-menu__label">Analytics</span>
                                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child1">
                                        @can('analytics.view')
                                            <li class="slide">
                                                <a href="{{ route('dashboard.propertyAnalytic') }}"
                                                    class="side-menu__item">Property</a>

                                                <a href="{{ route('dashboard.billAnalytic') }}"
                                                    class="side-menu__item">Bill</a>

                                                <a href="{{ route('dashboard.paymentAnalytic') }}"
                                                    class="side-menu__item">Payment</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endif
                        @endcanany

                        {{-- @canany(['nationwide.view'])
                            @if (!Str::contains(auth()->user()->access_level, 'Assembly'))
                                <li class="slide has-sub">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                            viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                            <path d="M0 0h24v24H0V0z" fill="none" />
                                            <path d="M6.26 9L12 13.47 17.74 9 12 4.53z" opacity=".3" />
                                            <path
                                                d="M19.37 12.8l-7.38 5.74-7.37-5.73L3 14.07l9 7 9-7zM12 2L3 9l1.63 1.27L12 16l7.36-5.73L21 9l-9-7zm0 11.47L6.26 9 12 4.53 17.74 9 12 13.47z" />
                                        </svg>
                                        <span class="side-menu__label">Nationwide Overview</span>
                                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child1">
                                        @can('nationwide.view')
                                            <li class="slide">
                                                <a href="{{ route('dashboard.overview') }}"
                                                    class="side-menu__item">Overview</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endif
                        @endcanany --}}

                        <!-- Start::slide -->
                        @canany(['assemblies.view', 'divisions.view', 'blocks.view', 'zones.view', 'property-uses.view',
                            'rates.view'])
                            {{-- <li class="slide__category"><span class="category-name">Fee Fixing / Assembly MGT</span></li> --}}

                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path d="M6.26 9L12 13.47 17.74 9 12 4.53z" opacity=".3" />
                                        <path
                                            d="M19.37 12.8l-7.38 5.74-7.37-5.73L3 14.07l9 7 9-7zM12 2L3 9l1.63 1.27L12 16l7.36-5.73L21 9l-9-7zm0 11.47L6.26 9 12 4.53 17.74 9 12 13.47z" />
                                    </svg>
                                    <span class="side-menu__label">Assembly Setup</span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">
                                    @can('assemblies.view')
                                        {{-- <li class="slide">
                                            <a href="{{ route('mmdas.index') }}" class="side-menu__item">
                                                Manage MMDA</a>
                                        </li> --}}
                                    @endcan
                                    @can('assemblies.view')
                                        <li class="slide">
                                            <a href="{{ route('assembly.index') }}" class="side-menu__item">
                                                Manage Assembly</a>
                                        </li>
                                    @endcan
                                    {{-- @can('divisions.view')
                                        <li class="slide">
                                            <a href="{{ route('divisions.index') }}" class="side-menu__item">
                                                Manage Division</a>
                                        </li>
                                    @endcan --}}
                                    @can('divisions.view')
                                        <li class="slide">
                                            <a href="{{ route('new-divisions.index') }}" class="side-menu__item">
                                                Manage Division</a>
                                        </li>
                                    @endcan
                                    {{-- @can('blocks.view')
                                        <li class="slide">
                                            <a href="{{ route('blocks.index') }}" class="side-menu__item">
                                                Manage Block</a>
                                        </li>
                                    @endcan --}}
                                    @can('blocks.view')
                                        <li class="slide">
                                            <a href="{{ route('new-blocks.index') }}" class="side-menu__item">
                                                Manage Block</a>
                                        </li>
                                    @endcan

                                    {{-- @can('property-uses.view')
                                        <li class="slide">
                                            <a href="{{ route('property-users.index') }}" class="side-menu__item">
                                                Property Uses</a>
                                        </li>
                                    @endcan --}}
                                    @can('rates.view')
                                        {{-- <li class="slide">
                                            <a href=" {{ route('rates.index') }}" class="side-menu__item">
                                                Manage Property Rate</a>
                                        </li> --}}

                                        {{-- <li class="slide">
                                            <a href=" {{ route('rates.bus.index') }}" class="side-menu__item">
                                                Manage Bop Rate</a>
                                        </li> --}}
                                    @endcan
                                </ul>
                            </li>
                        @endcanany
                        <!-- End::slide -->

                        @canany(['agent-assignments.view', 'task-assignments.view', 'blocks.view'])
                            {{-- <li class="slide__category"><span class="category-name">Fee Fixing / Assembly MGT</span></li> --}}

                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path d="M6.26 9L12 13.47 17.74 9 12 4.53z" opacity=".3" />
                                        <path
                                            d="M19.37 12.8l-7.38 5.74-7.37-5.73L3 14.07l9 7 9-7zM12 2L3 9l1.63 1.27L12 16l7.36-5.73L21 9l-9-7zm0 11.47L6.26 9 12 4.53 17.74 9 12 13.47z" />
                                    </svg>
                                    <span class="side-menu__label">Operations</span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">
                                    @can('agent-assignments.view')
                                        <li class="slide">
                                            <a href=" {{ route('agent-assignments.index') }}" class="side-menu__item">
                                                Agent Assignment</a>
                                        </li>
                                    @endcan
                                    @can('task-assignments.view')
                                        <li class="slide">
                                            <a href=" {{ route('task-assignments.index') }}" class="side-menu__item">
                                                Task Assignment</a>
                                        </li>
                                    @endcan
                                    @can('task-assignments.view')
                                        <li class="slide">
                                            <a href=" {{ route('customer-supports.index') }}" class="side-menu__item">
                                                Customer Support</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcanany

                        <!-- Start::slide__category -->
                        {{-- <li class="slide__category"><span class="category-name">Notifications & Assistance</span></li> --}}
                        <!-- End::slide__category -->

                        <!-- Start::slide -->
                        {{-- <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"
                                    enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24"
                                    width="24px" fill="#5f6368">
                                    <g>
                                        <rect fill="none" height="24" width="24" />
                                    </g>
                                    <g>
                                        <g>
                                            <polygon opacity=".3" points="4,7 20,7 20,3.98 4,4" />
                                            <path d="M5,20h14V9H5V20z M9,12h6v2H9V12z" opacity=".3" />
                                            <path
                                                d="M20,2H4C3,2,2,2.9,2,4v3.01C2,7.73,2.43,8.35,3,8.7V20c0,1.1,1.1,2,2,2h14c0.9,0,2-0.9,2-2V8.7c0.57-0.35,1-0.97,1-1.69V4 C22,2.9,21,2,20,2z M19,20H5V9h14V20z M20,7H4V4l16-0.02V7z" />
                                            <rect height="2" width="6" x="9" y="12" />
                                        </g>
                                    </g>
                                </svg>
                                <span class="side-menu__label">Notifications</span>
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1 mega-menu">

                                <li class="slide">
                                    <a href="empty.html" class="side-menu__item">Payment Alerts</a>
                                </li>
                                <li class="slide">
                                    <a href="empty.html" class="side-menu__item">System Updates</a>
                                </li>
                                <li class="slide">
                                    <a href="empty.html" class="side-menu__item">User Reminders</a>
                                </li>
                            </ul>
                        </li> --}}
                        <!-- End::slide -->

                        <!-- Start::slide -->
                        {{-- <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon"
                                    enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24"
                                    width="24px" fill="#5f6368">
                                    <g>
                                        <rect fill="none" height="24" width="24" y="0" />
                                    </g>
                                    <g>
                                        <g>
                                            <polygon opacity=".3" points="12.35,16.18 7.82,11.65 5.3,18.7" />
                                            <path
                                                d="M2,22l14-5L7,8L2,22z M12.35,16.18L5.3,18.7l2.52-7.05L12.35,16.18z" />
                                            <path
                                                d="M14.53,12.53l5.59-5.59c0.49-0.49,1.28-0.49,1.77,0l0.59,0.59l1.06-1.06l-0.59-0.59c-1.07-1.07-2.82-1.07-3.89,0 l-5.59,5.59L14.53,12.53z" />
                                            <path
                                                d="M9.47,7.47l1.06,1.06l0.59-0.59c1.07-1.07,1.07-2.82,0-3.89l-0.59-0.59L9.47,4.53l0.59,0.59c0.48,0.48,0.48,1.28,0,1.76 L9.47,7.47z" />
                                            <path
                                                d="M17.06,11.88l-1.59,1.59l1.06,1.06l1.59-1.59c0.49-0.49,1.28-0.49,1.77,0l1.61,1.61l1.06-1.06l-1.61-1.61 C19.87,10.81,18.13,10.81,17.06,11.88z" />
                                            <path
                                                d="M15.06,5.88l-3.59,3.59l1.06,1.06l3.59-3.59c1.07-1.07,1.07-2.82,0-3.89l-1.59-1.59l-1.06,1.06l1.59,1.59 C15.54,4.6,15.54,5.4,15.06,5.88z" />
                                        </g>
                                    </g>
                                </svg>
                                <span class="side-menu__label">AI Assistant</span>
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">


                            </ul>
                        </li> --}}
                        <!-- End::slide -->


                        <!-- Start::slide__category -->
                        {{-- <li class="slide__category"><span class="category-name">Feedback & Support</span></li> --}}
                        <!-- End::slide__category -->
                        @auth
                        @if (auth()->user()->access_level == 'customer')
                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path d="M13 4H6v16h12V9h-5V4zm3 14H8v-2h8v2zm0-6v2H8v-2h8z" opacity=".3" />
                                        <path
                                            d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z" />
                                    </svg>
                                    <span class="side-menu__label">My Account</span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">
                                    <li class="slide">
                                        <a href="{{ route('dashboard.mybills') }}" class="side-menu__item">Bills</a>
                                    </li>
                                    <li class="slide">
                                        <a href="{{ route('dashboard.myproperties') }}"
                                            class="side-menu__item">Properties</a>
                                    </li>
                                    {{-- <li class="slide">
                                        <a href="{{ route('dashboard.mybusiness') }}"
                                            class="side-menu__item">Businesses</a>
                                    </li> --}}
                                </ul>
                            </li>

                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path d="M13 4H6v16h12V9h-5V4zm3 14H8v-2h8v2zm0-6v2H8v-2h8z" opacity=".3" />
                                        <path
                                            d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z" />
                                    </svg>
                                    <span class="side-menu__label">My Payments</span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">
                                    <li class="slide">
                                        <a href="{{ route('dashboard.mypaymenthistory') }}"
                                            class="side-menu__item">View
                                            Payment History</a>
                                    </li>

                                </ul>
                            </li>

                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="#5f6368">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path d="M13 4H6v16h12V9h-5V4zm3 14H8v-2h8v2zm0-6v2H8v-2h8z" opacity=".3" />
                                        <path
                                            d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z" />
                                    </svg>
                                    <span class="side-menu__label">Help & Support </span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">
                                    <li class="slide">
                                        <a href="{{ route('dashboard.faq') }}" class="side-menu__item">FAQ's</a>
                                    </li>

                                </ul>
                            </li>
                        @endif
                        @endauth

                    </ul>
                    <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                            width="24" height="24" viewBox="0 0 24 24">
                            <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                        </svg></div>
                </nav>
                <!-- End::nav -->

            </div>
            <!-- End::main-sidebar -->

        </aside>
        <!-- End::app-sidebar -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    {{-- <h1 class="page-title fw-semibold fs-18 mb-0">Melchia Dashboard</h1> --}}
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <!-- <ol class="breadcrumb mb-0"> -->
                            <!-- <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboards</a></li> -->

                            <!-- </ol> -->
                        </nav>
                    </div>
                </div>
                <!-- Page Header Close -->

                <!-- Start:: row-4 -->
                @yield('page-content')
                <!-- End:: row-4 -->

            </div>
        </div>
        <!-- End::app-content -->


        <!-- Footer Start -->
        <footer class="footer mt-auto py-3 bg-white text-center">
            <div class="container">
                <span class="text-muted">
                    Copyright © 2025<span id="year"></span>
                    All rights reserved.
                </span>
            </div>
        </footer>

        <!-- Footer End -->
        <div class="modal fade" id="responsive-searchModal" tabindex="-1" aria-labelledby="responsive-searchModal"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="input-group">
                            <input type="text" class="form-control border-end-0" placeholder="Search Anything ..."
                                aria-label="Search Anything ..." aria-describedby="button-addon2">
                            <button class="btn btn-primary" type="button" id="button-addon2"><i
                                    class="bi bi-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Scroll To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>
    <div id="responsive-overlay"></div>
    <!-- Scroll To Top -->

    <!-- Jquery Cdn -->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

    <!-- Popper JS -->
    <script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap-select.min.js') }}"></script>

    <!-- Defaultmenu JS -->
    <script src="{{ asset('assets/js/defaultmenu.js') }}"></script>

    <!-- Node Waves JS-->
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

    <!-- Sticky JS -->
    <script src="{{ asset('assets/js/sticky.js') }}"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/simplebar.js') }}"></script>

    <!-- Auto Complete JS -->
    <script src="{{ asset('assets/libs/@tarekraafat/autocomplete.js/autoComplete.min.js') }}"></script>

    <!-- Color Picker JS -->
    <script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>


    <!-- Apex Charts JS -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Jobs-Dashboard -->
    <script src="{{ asset('assets/js/jobs-dashboard.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>


    <!-- Custom-Switcher JS -->
    <script src="{{ asset('assets/js/custom-switcher.js') }}"></script>

    <!-- JSVector Maps MapsJS -->
    <script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>
    <script src="{{ asset('assets/js/us-merc-en.js') }}"></script>
    <script src="{{ asset('assets/js/russia.js') }}"></script>
    <script src="{{ asset('assets/js/spain.js') }}"></script>
    <script src="{{ asset('assets/js/canada.js') }}"></script>
    <script src="{{ asset('assets/js/jsvectormap.js') }}"></script>


    <!-- Jquery Cdn -->

    <!-- Datatables Cdn -->
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.6/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- Internal Datatables JS -->
    <script src="{{ asset('assets/js/datatables.js?t=' . time()) }}"></script>

    <!-- Custom-Switcher JS -->
    <script src="{{ asset('assets/js/custom-switcher.js') }}"></script>
    <script src="{{ asset('assets/js/sticky.js') }}"></script>
    <!-- Defaultmenu JS -->
    <script src="{{ asset('assets/js/defaultmenu.js') }}"></script>

    <script src="{{ asset('assets/js/apexcharts-column.js') }}"></script>

    <!-- Internal Apex Mixed Charts JS -->
    <script src="{{ asset('assets/js/apexcharts-mixed.js') }}"></script>

    <!-- Internal Choices JS -->
    <script src="{{ asset('assets/js/choices.js') }}"></script>

    @yield('page-scripts')
</body>

</html>
