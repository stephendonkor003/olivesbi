<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERMS - Enhanced Revenue Management System</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/assetsfront/images/fav.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/assetsfront/css/plugins/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assetsfront/css/plugins/fontawesome-5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assetsfront/css/plugins/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assetsfront/css/plugins/unicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assetsfront/css/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assetsfront/css/style.css?t=' . time()) }}">
</head>

<body class="home-blue medium-blue onepage">

    <!-- start header area -->
    <header class="header-two header--sticky eight">
        <div class="main-header">
            <div class="content">
                <div class="header-left">
                    <a class="thumbnail" href="#">
                        <img style="height: 30%; width:30%;"
                            src="{{ asset('assets/assetsfront/images/logo/ipts-logo-new.png') }}" alt="">
                    </a>
                    <nav class="nav-main mainmenu-nav d-none d-xl-block">
                        <ul class="mainmenu">
                            <li><a href="#banner">Home</a></li>
                            <li><a href="#features">Features</a></li>
                            <li><a href="#about">About Us</a></li>
                            <li><a href="#faq">FAQs</a></li>
                            <li><a href="{{ route('locality.check') }}">Check Locality</a></li>
                            <li><a href="#contact">Contact</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="header-right">
                    <div class="call-area">
                    </div>
                    <button id="menu-btn" class="menu rts-btn btn-primary-alta ml--20">
                        <img class="menu-dark" src="{{ asset('assets/assetsfront/images/icon/menu.png') }}"
                            alt="Menu-icon">
                        <img class="menu-light" src="{{ asset('assets/assetsfront/images/icon/menu-light.png') }}"
                            alt="Menu-icon">
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div id="anywhere-home"></div>
    <div id="side-bar" class="side-bar">
        <button class="close-icon-menu"><i class="far fa-times"></i></button>
        <!-- inner menu area desktop start -->
        <div class="rts-sidebar-menu-desktop">
            <a class="logo-1" href="#"><img style="width: 70%;" class="logo"
                    src="{{ asset('assets/assetsfront/images/logo/ipts-logo-main.png') }}" alt="IPRS_logo"></a>
            <a class="logo-2" href="#"><img style="width: 70%;" class="logo"
                    src="{{ asset('assets/assetsfront/images/logo/ipts-logo-main.png') }}" alt="IPRS_logo"></a>
            <a class="logo-3" href="#"><img style="width: 70%;" class="logo"
                    src="{{ asset('assets/assetsfront/images/logo/ipts-logo-main.png') }}" alt="IPRS_logo"></a>
            <a class="logo-4" href="#"><img style="width: 70%;" class="logo"
                    src="{{ asset('assets/assetsfront/images/logo/ipts-logo-main.png') }}" alt="IPRS_logo"></a>
            <div class="body d-none d-xl-block">
                <p class="disc">
                    IPRS is a transformative solution designed to streamline revenue collection and enhance transparency
                    for local assemblies across Africa.
                </p>
                <div class="get-in-touch">
                    <!-- title -->
                    <div class="h6 title">Get In Touch</div>
                    <!-- title End -->
                    <div class="wrapper">
                        <!-- single -->
                        <div class="single">
                            <i class="fas fa-phone-alt"></i>
                            <a href="tel:+233500503599">+233 50 050 3599</a>
                        </div>
                        <!-- single ENd -->
                        <!-- single -->
                        <div class="single">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:info@level10gh.com">info@level10gh.com</a>
                        </div>
                        <!-- single ENd -->
                        <!-- single -->
                        <div class="single">
                            <i class="fas fa-globe"></i>
                            <a href="https://level10gh.com">www.level10gh.com</a>
                        </div>
                        <!-- single ENd -->
                        <!-- single -->
                        <div class="single">
                            <i class="fas fa-map-marker-alt"></i>
                            <a href="https://maps.app.goo.gl/hyRt9k53TXx1LbKZA">29 Senchi Street,Airport Residential,
                                Accra Ghana</a>
                        </div>
                        <!-- single ENd -->
                    </div>
                    <div class="social-wrapper-two menu">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                        <!-- <a href="#"><i class="fab fa-linkedin"></i></a> -->
                    </div>
                </div>
            </div>
            <div class="body-mobile d-block d-xl-none">
                <nav class="nav-main mainmenu-nav">
                    <ul class="mainmenu">
                        <li><a href="#banner">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="{{ route('locality.check') }}">Check Locality</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </nav>
                <div class="social-wrapper-two menu mobile-menu">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                    <!-- <a href="#"><i class="fab fa-linkedin"></i></a> -->
                </div>
                <a href="{{ route('auth.index') }}"
                    class="rts-btn btn-primary ml--20 ml_sm--5 header-one-btn quote-btnmenu">Log in</a>
            </div>
        </div>
        <!-- inner menu area desktop End -->
    </div>
    <!-- ENd Header Area -->

    <!-- rts banner area start -->
    <div id="banner" class="rts-banner-area-two eight">
        <div class="swiper mySwiperh3_banner">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class=" bg_banner-three bg_image rts-section-gap eight">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="banner-three-inner">
                                        <span class="subtitle-banner">Effortless Rate Payments, Anytime,
                                            Anywhere</span>
                                        <!-- type headline start-->
                                        <h1 class="title cd-headline clip is-full-width">
                                            Real-Time Monitoring
                                        </h1>
                                        <p class="disc">
                                            With our Integrated Property Rate System, managing and paying your rates has
                                            never been easier. Enjoy secure, flexible payment options tailored for your
                                            convenience, all from the comfort of your home or office.
                                        </p>
                                        <div class="button-group">
                                            <a href="{{ route('auth.register') }} " class="rts-btn btn-primary-2">
                                                Sign Up</a>
                                            <a href="{{ route('auth.index') }}"
                                                class="rts-btn btn-primary-2 transparent">Sign In
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class=" bg_banner-three eight-2 bg_image rts-section-gap">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="banner-three-inner">
                                        <span class="subtitle-banner">Revolutionizing Property Rate Collection </span>
                                        <!-- type headline start-->
                                        <h1 class="title cd-headline clip is-full-width">
                                            Simplified Rate Management
                                        </h1>
                                        <p class="disc">
                                            Our system brings innovation to rate administration with cutting-edge
                                            technology, streamlined workflows, and powerful analytics. Simplify the
                                            management of rate records and collections with a robust platform designed
                                            to
                                            deliver efficiency and transparency.
                                        </p>
                                        <div class="button-group">
                                            <a href="{{ route('auth.register') }} " class="rts-btn btn-primary-2">
                                                Sign Up</a>
                                            <a href="{{ route('auth.index') }}"
                                                class="rts-btn btn-primary-2 transparent">Sign In
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class=" bg_banner-three eight-3 bg_image rts-section-gap">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="banner-three-inner">
                                        <span class="subtitle-banner">A Smarter Way to Manage Revenue</span>
                                        <!-- type headline start-->
                                        <h1 class="title cd-headline clip is-full-width">
                                            Smarter Local Revenue
                                        </h1>
                                        <p class="disc">
                                            The Integrated Property Rate System equips local governments with tools to
                                            maximize revenue collection, reduce inefficiencies, and ensure
                                            accountability. Drive sustainable development with data-driven insights and
                                            seamless processes.
                                        </p>
                                        <div class="button-group">
                                            <a href="{{ route('auth.register') }} " class="rts-btn btn-primary-2">
                                                Sign Up</a>
                                            <a href="{{ route('auth.index') }}"
                                                class="rts-btn btn-primary-2 transparent">Sign In
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class=" bg_banner-three eight-4 bg_image rts-section-gap">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="banner-three-inner">
                                        <span class="subtitle-banner">A Smarter Way to Manage Revenue</span>
                                        <!-- type headline start-->
                                        <h1 class="title cd-headline clip is-full-width">
                                            Effortless Revenue Mobilization
                                        </h1>
                                        <p class="disc">
                                            The Integrated Property Rate System equips local governments with tools to
                                            maximize revenue collection, reduce inefficiencies, and ensure
                                            accountability. Drive sustainable development with data-driven insights and
                                            seamless processes.
                                        </p>
                                        <div class="button-group">
                                            <a href="{{ route('auth.register') }} " class="rts-btn btn-primary-2">
                                                Sign Up</a>
                                            <a href="{{ route('auth.index') }}"
                                                class="rts-btn btn-primary-2 transparent">Sign In
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="swiper-slide">
                    <div class=" bg_banner-three eight-5 bg_image rts-section-gap">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="banner-three-inner">
                                        <span class="subtitle-banner">A Smarter Way to Manage Revenue</span>
                                        <!-- type headline start-->
                                        <h1 class="title cd-headline clip is-full-width">
                                            Digital Revenue Growth
                                        </h1>
                                        <p class="disc">
                                            The Integrated Property Rate System equips local governments with tools to
                                            maximize revenue collection, reduce inefficiencies, and ensure
                                            accountability. Drive sustainable development with data-driven insights and
                                            seamless processes.
                                        </p>
                                        <div class="button-group">
                                            <a href="{{ route('auth.register') }} " class="rts-btn btn-primary-2">
                                                Sign Up</a>
                                            <a href="{{ route('auth.index') }}"
                                                class="rts-btn btn-primary-2 transparent">Sign In
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">

                    <div class=" bg_banner-three eight-6 bg_image rts-section-gap">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="banner-three-inner">
                                        <span class="subtitle-banner">A Smarter Way to Manage Revenue</span>
                                        <!-- type headline start-->
                                        <h1 class="title cd-headline clip is-full-width">
                                            Optimize Local Finances
                                        </h1>
                                        <p class="disc">
                                            The Integrated Property Rate System equips local governments with tools to
                                            maximize revenue collection, reduce inefficiencies, and ensure
                                            accountability. Drive sustainable development with data-driven insights and
                                            seamless processes.
                                        </p>
                                        <div class="button-group">
                                            <a href="{{ route('auth.register') }} " class="rts-btn btn-primary-2">
                                                Sign Up</a>
                                            <a href="{{ route('auth.index') }}"
                                                class="rts-btn btn-primary-2 transparent">Sign In
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class=" bg_banner-three eight-7 bg_image rts-section-gap">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="banner-three-inner">
                                        <span class="subtitle-banner">A Smarter Way to Manage Revenue</span>
                                        <!-- type headline start-->
                                        <h1 class="title cd-headline clip is-full-width">
                                            Seamless Revenue Collection
                                        </h1>
                                        <p class="disc">
                                            The Integrated Property Rate System equips local governments with tools to
                                            maximize revenue collection, reduce inefficiencies, and ensure
                                            accountability. Drive sustainable development with data-driven insights and
                                            seamless processes.
                                        </p>
                                        <div class="button-group">
                                            <a href="{{ route('auth.register') }} " class="rts-btn btn-primary-2">
                                                Sign Up</a>
                                            <a href="{{ route('auth.index') }}"
                                                class="rts-btn btn-primary-2 transparent">Sign In
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">

                    <div class=" bg_banner-three eight-8 bg_image rts-section-gap">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="banner-three-inner">
                                        <span class="subtitle-banner">A Smarter Way to Manage Revenue</span>
                                        <!-- type headline start-->
                                        <h1 class="title cd-headline clip is-full-width">
                                            Empowering Local Governments
                                        </h1>
                                        <p class="disc">
                                            The Integrated Property Rate System equips local governments with tools to
                                            maximize revenue collection, reduce inefficiencies, and ensure
                                            accountability. Drive sustainable development with data-driven insights and
                                            seamless processes.
                                        </p>
                                        <div class="button-group">
                                            <a href="{{ route('auth.register') }} " class="rts-btn btn-primary-2">
                                                Sign Up</a>
                                            <a href="{{ route('auth.index') }}"
                                                class="rts-btn btn-primary-2 transparent">Sign In
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class=" bg_banner-three eight-9 bg_image rts-section-gap">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="banner-three-inner">
                                        <span class="subtitle-banner">A Smarter Way to Manage Revenue</span>
                                        <!-- type headline start-->
                                        <h1 class="title cd-headline clip is-full-width">
                                            Empowering Local Governments
                                        </h1>
                                        <p class="disc">
                                            The Integrated Property Rate System equips local governments with tools to
                                            maximize revenue collection, reduce inefficiencies, and ensure
                                            accountability. Drive sustainable development with data-driven insights and
                                            seamless processes.
                                        </p>
                                        <div class="button-group">
                                            <a href="{{ route('auth.register') }} " class="rts-btn btn-primary-2">
                                                Sign Up</a>
                                            <a href="{{ route('auth.index') }}"
                                                class="rts-btn btn-primary-2 transparent">Sign In
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="swiper-slide">
                    <div class=" bg_banner-three eight-10 bg_image rts-section-gap">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="banner-three-inner">
                                        <span class="subtitle-banner">A Smarter Way to Manage Revenue</span>
                                        <!-- type headline start-->
                                        <h1 class="title cd-headline clip is-full-width">
                                            Empowering Local Governments
                                        </h1>
                                        <p class="disc">
                                            The Integrated Property Rate System equips local governments with tools to
                                            maximize revenue collection, reduce inefficiencies, and ensure
                                            accountability. Drive sustainable development with data-driven insights and
                                            seamless processes.
                                        </p>
                                        <div class="button-group">
                                            <a href="{{ route('auth.register') }} " class="rts-btn btn-primary-2">
                                                Sign Up</a>
                                            <a href="{{ route('auth.index') }}"
                                                class="rts-btn btn-primary-2 transparent">Sign In
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <div class="swiper-navigation">
            <span class="swiper-button-prev"></span>
            <span class="swiper-button-next"></span>
        </div>
    </div>
    <!-- rts banner area end -->

    <!-- start service area -->
    <div id="services" class="rts-service-areah2-im-3 eight rts-section-gap">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="image-area">
                        <img src="{{ asset('assets/assetsfront/images/service/h2/03.jpg') }}" alt="Service_Image">
                        <img class="two" src="{{ asset('assets/assetsfront/images/service/h2/02.jpg') }}"
                            alt="Service_Image">
                        <img class="three" src="{{ asset('assets/assetsfront/images/service/h2/01.jpg') }}"
                            alt="Service_Image">
                        <div class="ratio-area">
                            <h3 class="ratio">99%</h3>
                            <span>Collection Ratio</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="service-h2-content pl--30">
                        <div class="title-area  service-h2 service">
                            <span>Why Choose Us</span>
                            <h2 class="title">Transforming Revenue Management Across Africa</h2>
                        </div>
                        <div class="content-wrapper">
                            <p class="disc">
                                IPRS is redefining how communities across Africa collect and manage revenue. By
                                leveraging technology and promoting transparency, it ensures efficient processes that
                                empower citizens and drive sustainable development.
                            </p>
                            <div class="feature-one-wrapper mt--40">
                                <div class="single-feature-one eight">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="639pt"
                                        viewBox="-19 -19 639 639.99991" width="639pt">
                                        <path
                                            d="m540.5 330v-20c5.519531 0 10-4.476562 10-10v-60c0-5.523438-4.480469-10-10-10h-54l-1.9375-7.511719c-4.421875-17.179687-11.289062-33.636719-20.402344-48.859375l-4-6.691406 38.339844-38.40625c3.859375-3.867188 3.859375-10.132812 0-14l-42.46875-42.53125c-3.921875-3.738281-10.082031-3.738281-14.003906 0l-38.410156 38.410156-6.6875-4c-15.246094-9.117187-31.71875-15.984375-48.921876-20.410156l-7.507812-1.953125v-54.046875c0-5.523438-4.476562-10-10-10h-60c-5.523438 0-10 4.476562-10 10v54l-7.511719 1.941406c-17.179687 4.417969-33.636719 11.285156-48.867187 20.386719l-6.691406 4.003906-38.398438-38.332031c-3.925781-3.769531-10.125-3.769531-14.050781 0l-42.480469 42.46875c-1.867188 1.863281-2.917969 4.394531-2.917969 7.03125 0 2.640625 1.050781 5.167969 2.917969 7.03125l38.410156 38.40625-4 6.691406c-9.109375 15.222656-15.980468 31.679688-20.398437 48.859375l-1.960938 7.511719h-54.050781c-5.523438 0-10 4.476562-10 10v60c0 5.523438 4.476562 10 10 10v20c-16.566406 0-30-13.433594-30-30v-60c0-16.566406 13.433594-30 30-30h38.691406c4.125-13.828125 9.703125-27.179688 16.640625-39.828125l-27.5-27.5c-5.625-5.613281-8.785156-13.230469-8.785156-21.171875s3.160156-15.558594 8.785156-21.167969l42.5-42.5c11.839844-11.332031 30.507813-11.332031 42.347657 0l27.492187 27.488281c12.648437-6.933593 26.003906-12.5 39.828125-16.621093v-38.699219c0-16.566406 13.433594-30 30-30h60c16.566406 0 30 13.433594 30 30v38.699219c13.828125 4.121093 27.175781 9.699219 39.828125 16.632812l27.5-27.5c11.84375-11.332031 30.507813-11.332031 42.351563 0l42.492187 42.488281c5.621094 5.613282 8.777344 13.222657 8.777344 21.167969 0 7.945313-3.15625 15.5625-8.777344 21.171875l-27.5 27.5c6.933594 12.65625 12.507813 26.011719 16.636719 39.839844h38.691406c16.566406 0 30 13.433594 30 30v60c0 16.566406-13.433594 30-30 30zm0 0">
                                        </path>
                                        <path
                                            d="m440.5 270h-20c0-66.273438-53.726562-120-120-120s-120 53.726562-120 120h-20c0-77.320312 62.679688-140 140-140 77.316406 0 140 62.679688 140 140zm0 0">
                                        </path>
                                        <path
                                            d="m300.5 350c-33.136719 0-60-26.867188-60-60 0-33.136719 26.863281-60 60-60 33.140625 0 60 26.863281 60 60-.035156 33.125-26.875 59.964844-60 60zm0-100c-22.089844 0-40 17.910156-40 40s17.910156 40 40 40 40-17.910156 40-40-17.910156-40-40-40zm0 0">
                                        </path>
                                        <path
                                            d="m470.5 390c-33.136719 0-60-26.867188-60-60 0-33.136719 26.863281-60 60-60s60 26.863281 60 60c-.035156 33.125-26.875 59.964844-60 60zm0-100c-22.089844 0-40 17.910156-40 40s17.910156 40 40 40 40-17.910156 40-40-17.910156-40-40-40zm0 0">
                                        </path>
                                        <path
                                            d="m130.5 390c-33.136719 0-60-26.867188-60-60 0-33.136719 26.863281-60 60-60 33.140625 0 60 26.863281 60 60-.035156 33.125-26.875 59.964844-60 60zm0-100c-22.089844 0-40 17.910156-40 40s17.910156 40 40 40 40-17.910156 40-40-17.910156-40-40-40zm0 0">
                                        </path>
                                        <path
                                            d="m561.15625 415.65625c-14.902344-10.242188-32.570312-15.707031-50.65625-15.65625h-80c-4.863281.042969-9.71875.484375-14.511719 1.316406-6.398437-10.007812-14.722656-18.640625-24.488281-25.410156-1.464844-1.105469-3.003906-2.109375-4.609375-2.996094-13.988281-8.472656-30.039063-12.933594-46.390625-12.910156h-80c-30.582031-.109375-59.078125 15.496094-75.460938 41.316406-4.804687-.832031-9.667968-1.273437-14.539062-1.316406h-80c-18.070312-.050781-35.734375 5.40625-50.628906 15.636719-24.617188 16.75-39.355469 44.589843-39.371094 74.363281v40c.035156 27.304688 15.921875 52.101562 40.722656 63.539062 9.15625 4.3125 19.160156 6.519532 29.277344 6.460938h100v-20h-90v-110h-20v108.996094c-3.765625-.753906-7.421875-1.972656-10.890625-3.617188-17.714844-8.167968-29.070313-25.875-29.109375-45.378906v-40c.023438-23.171875 11.503906-44.832031 30.660156-57.863281 11.574219-7.945313 25.296875-12.175781 39.339844-12.136719h80c1.761719 0 3.460938.1875 5.179688.304688-3.382813 9.539062-5.136719 19.578124-5.179688 29.695312v100c.035156 27.601562 22.402344 49.964844 50 50h30v-160h-20v140h-10c-16.566406 0-30-13.433594-30-30v-100c0-38.664062 31.34375-70 70-70h80c12.867188.007812 25.476562 3.570312 36.449219 10.289062 1.015625.554688 1.992187 1.179688 2.917969 1.867188 9.371093 6.417969 17.0625 14.992188 22.421874 25 5.386719 10.109375 8.210938 21.386719 8.210938 32.84375v100c0 16.566406-13.433594 30-30 30h-10v-140h-20v160h30c27.601562-.035156 49.964844-22.398438 50-50v-100c-.046875-10.117188-1.792969-20.152344-5.171875-29.695312 1.722656-.117188 3.421875-.304688 5.171875-.304688h80c14.050781-.039062 27.785156 4.203125 39.367188 12.15625 19.140624 13.039062 30.605468 34.683594 30.632812 57.84375v40c-.035156 19.574219-11.476562 37.332031-29.277344 45.457031-3.417968 1.617188-7.019531 2.804688-10.722656 3.539063v-108.996094h-20v110h-90v20h100c10.058594.0625 20.003906-2.117188 29.109375-6.382812 24.882813-11.402344 40.851563-36.246094 40.890625-63.617188v-40c-.015625-29.761719-14.742188-57.589844-39.34375-74.34375zm0 0">
                                        </path>
                                        <path d="m260.5 580h80v20h-80zm0 0"></path>
                                    </svg>
                                    <p>Rate Management</p>
                                </div>
                                <div class="single-feature-one eight">
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Global_Message" height="512"
                                        viewBox="0 0 512 512" width="512">
                                        <g>
                                            <path
                                                d="m232.421 424.651c-1.737-1.962-4.169-3.141-6.76-3.279-8.707-.462-13.615 10.017-7.778 16.606l4.642 5.239c-54.563-9.31-103.233-38.3-138.428-82.825-32.122-40.625-49.812-91.762-49.812-143.99 0-5.523-4.477-10-10-10s-10 4.477-10 10c0 56.71 19.221 112.252 54.122 156.393 48.696 61.588 111.449 84.16 155.1 90.82l-7.282 6.67c-6.48 5.935-2.67 16.878 6.037 17.34 2.592.138 5.131-.778 7.059-2.545l23.979-21.976c4.015-3.68 4.338-9.974.721-14.059z">
                                            </path>
                                            <path
                                                d="m494.36 378.78-33.005-72.009c-3.427-7.478-9.109-13.416-16.241-17.1 8.937-23.207 13.655-48.081 13.658-73.246 0-.008.001-.016.001-.024s-.001-.017-.001-.025c-.006-45.588-15.458-90.231-43.508-125.707-37.214-47.091-92.393-74.452-151.734-75.359-1.853-.549-3.836-.549-5.698-.004-59.406.82-114.683 28.187-151.957 75.361-28.054 35.481-43.506 80.129-43.51 125.721 0 .004-.001.009-.001.013s.001.008.001.012c.002 45.594 15.454 90.245 43.509 125.727 37.29 47.174 92.763 74.644 152.211 75.37.696.015 1.407.016 2.119.016h.13l27.101 59.132c8.728 19.041 26.871 21.144 32.706 21.144 5.213 0 10.407-1.16 15.266-3.465l141.555-67.157c17.608-8.354 25.857-29.942 17.398-48.4zm-188.741 89.544-33.004-72.011c-.769-1.677-1.225-3.419-1.419-5.163l65.384 15.411-28.936 65.067c-.786-1.007-1.475-2.105-2.025-3.304zm137.555-153.22 33.005 72.009c.607 1.326 1.009 2.693 1.254 4.068l-67.482-19.242 30.493-61.002c1.096 1.223 2.02 2.619 2.73 4.167zm-90.405 74.726c-.004-.001-.009-.002-.013-.003l-70.174-16.54 136.772-64.888-32.606 65.228c0 .001 0 .001-.001.002l-9.749 19.503c-.635 1.271-1.988 1.943-3.301 1.632zm-236.87-67.661c-20.054-28.197-31.433-60.964-33.261-95.767h82.808c.815 22.33 4.363 44.306 10.587 65.567-21.252 7.381-41.407 17.504-60.134 30.2zm.003-211.533c18.725 12.694 38.878 22.816 60.131 30.198-6.224 21.253-9.771 43.228-10.587 65.568h-82.808c1.829-34.803 13.209-67.569 33.264-95.766zm289.335-.001c20.055 28.196 31.435 60.963 33.264 95.767h-84.021c-.709-22.232-4.119-44.119-10.185-65.289 21.541-7.4 41.968-17.617 60.942-30.478zm-210.072 175.511c-5.653-19.374-8.909-39.395-9.711-59.744h65.117v51.55c-18.777.718-37.338 3.464-55.406 8.194zm55.405-131.294v51.55h-65.117c.802-20.358 4.059-40.378 9.712-59.744 18.067 4.729 36.629 7.474 55.405 8.194zm74.572-7.982c5.509 19.305 8.632 39.259 9.33 59.531h-63.902v-51.551c18.477-.708 36.758-3.382 54.572-7.98zm-53.312 209.38c-.427.203-.844.42-1.26.638v-58.916c16.051.666 31.922 2.937 47.399 6.78-5.205 13.431-11.581 26.362-19.026 38.636zm65.389-45.843c5.841 1.988 11.59 4.206 17.245 6.639l-25.117 11.916c2.856-6.075 5.481-12.266 7.872-18.555zm-12.767-24.653c-17.576-4.49-35.626-7.103-53.881-7.802v-51.551h63.806c-.89 20.203-4.219 40.091-9.925 59.353zm100.843.036c-4.075.391-8.096 1.493-11.91 3.303l-35.54 16.861c-11.04-5.648-22.465-10.492-34.225-14.493 6.258-21.112 9.864-42.916 10.766-65.059h84.115c-1.087 20.693-5.55 40.666-13.206 59.388zm-154.725-150.959v-85.943c20.91 22.967 37.343 49.801 48.287 78.939-15.775 3.978-31.944 6.325-48.287 7.004zm-20 .001c-16.6-.689-33.009-3.096-49-7.182 11.174-29.165 27.849-55.973 49-78.86zm0 163.139v86.037c-21.151-22.882-37.834-49.699-49.003-78.854 15.994-4.087 32.403-6.494 49.003-7.183zm87.498-175.863c-11.542-31.229-28.908-60.049-51.061-84.845 40.657 6.145 77.897 26.341 105.743 57.675-17.07 11.414-35.395 20.518-54.682 27.17zm-104.066-84.83c-22.308 24.655-39.874 53.393-51.628 84.589-19.042-6.635-37.131-15.659-53.985-26.93 27.823-31.315 65.014-51.5 105.613-57.659zm-51.631 273.659c11.75 31.189 29.321 59.941 51.621 84.584-40.58-6.167-77.803-26.389-105.603-57.656 16.855-11.271 34.944-20.295 53.982-26.928zm174.021 100.294 12.712 2.996c1.767.418 3.539.619 5.288.619 8.536 0 16.518-4.802 20.498-12.778l5.962-11.927 67.156 19.149-140.368 66.594z">
                                            </path>
                                        </g>
                                    </svg>
                                    <p>24/7 Support:</p>
                                </div>
                                <div class="button-group">
                                    <a href="{{ route('auth.register') }} " class="rts-btn btn-primary-2">Get
                                        Started</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- start service area End -->

    <!-- cta section start -->
    <div class="rts-cta-section-start rts-section-gap cta-bg-h2">
        <div class="container">
            <div class="row">
                <div class="cta-h2-wrapper text-center">
                    <div class="icon">
                        <a href="#"><i class="fas fa-phone-alt"></i></a>
                    </div>
                    <div class="body">
                        <p class="info">Reach out to our dedicated support team for help with property rate inquiries
                            or system navigation. <span>(24/7 Available)</span></p>
                        <a href="tel:+233500503599" class="number">+233 50 050 3599</a>
                        <a href="#" class="rts-btn btn-primary-2">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- cta section end -->

    <!-- Working Process section start -->
    <div id="about" class="rts-working-process-section eight rts-section-gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="title-area">
                        <span>Seamless Automation</span>
                        <h2 class="title">Our Commitment to Revenue Assurance</h2>
                    </div>
                </div>
                <div class="col-lg-6">
                    <p class="decs">Through intelligent automation and real-time monitoring, IPRS ensures accurate,
                        transparent, and secure revenue collection, strengthening accountability between citizens and
                        authorities.</p>
                </div>

            </div>
            <div class="rts-working-process-inner">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                        <div class="wrapper">
                            <a href="#" class="icon">
                                <img src="{{ asset('assets/assetsfront/images/working-step/icon/goal.svg') }}"
                                    alt="Icon">
                            </a>
                            <div class="content">
                                <h5 class="title">Transparency</h5>
                                <p class="disc">We promote transparency in fund management to build trust and
                                    accountability.</p>
                            </div>

                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                        <div class="wrapper">
                            <a href="#" class="icon">
                                <img src="{{ asset('assets/assetsfront/images/working-step/icon/save.svg') }}"
                                    alt="Icon">
                            </a>
                            <div class="content">
                                <h5 class="title">Innovation</h5>
                                <p class="disc">We use modern technology to ensure fast, efficient revenue collection
                                    .</p>
                            </div>

                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                        <div class="wrapper">
                            <a href="#" class="icon">
                                <img src="{{ asset('assets/assetsfront/images/working-step/icon/user.svg') }}"
                                    alt="Icon">
                            </a>
                            <div class="content">
                                <h5 class="title">Investment</h5>
                                <p class="disc">We reinvest funds to
                                    promoting sustainable development in every community.</p>
                            </div>

                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                        <div class="wrapper">
                            <a href="#" class="icon">
                                <img src="{{ asset('assets/assetsfront/images/working-step/icon/target.svg') }}"
                                    alt="Icon">
                            </a>
                            <div class="content">
                                <h5 class="title">Experience</h5>
                                <p class="disc">We offer platforms to deliver seamless experiences
                                    tailored to the organizational needs .</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Working Process section end -->
    <!-- Start:: Section-8 -->
    <div id="faq" class="rts-working-process-section eight rts-section-gap">
        <div class="container text-center">
            <p class="fs-14 fw-medium text-success mb-1">
                <span class="landing-section-heading text-success"><i
                        class="ti ti-inner-shadow-top-right-filled text-secondary me-1 fs-10"></i>FAQs</span>
            </p>
            <h4 class="fw-semibold mt-3 mb-2">Common Questions and Answers</h4>
            <div class="row justify-content-center">
                <div class="col-xl-7">
                    <p class="text-muted fs-14 mb-5 fw-normal">Weve compiled a list of the most common
                        questions to assist you in finding the information you need.</p>
                </div>
            </div>
            <div class="row text-start">
                <div class="col-xl-12">
                    <div class="row gy-2">
                        <div class="col-xl-6">
                            <div class="accordion accordion-customicon1 faq-accordion accordion-primary accordions-items-separate"
                                id="accordionFAQ5">
                                <!-- FAQ 1 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingGen1">
                                        <button class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGen1"
                                            aria-expanded="true" aria-controls="collapseGen1">
                                            <p> What is the Property Rate Collection Platform?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseGen1" class="accordion-collapse collapse show"
                                        aria-labelledby="headingGen1" data-bs-parent="#accordionFAQ-General">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            The platform is a digital system designed by the Government of Ghana to
                                            streamline property rate collection, manage taxpayer information and ensure
                                            easy access to payment services.
                                        </div>
                                    </div>
                                </div>
                                <!-- FAQ 8 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingProp8">
                                        <button class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseProp8"
                                            aria-expanded="false" aria-controls="collapseProp8">
                                            <p> How do I add my property to the platform?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseProp8" class="accordion-collapse collapse"
                                        aria-labelledby="headingProp8" data-bs-parent="#accordionFAQ-Property">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">


                                            • Log in to your account.<br>
                                            • Select "Add Property."<br>
                                            • Enter your property details, such as location, size and valuation
                                            information.<br>
                                            • Upload supporting documents.
                                        </div>
                                    </div>
                                </div>


                                <!-- FAQ 2 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingGen2">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGen2"
                                            aria-expanded="false" aria-controls="collapseGen2">
                                            <p> Who is required to pay property rates?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseGen2" class="accordion-collapse collapse"
                                        aria-labelledby="headingGen2" data-bs-parent="#accordionFAQ-General">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Property owners, including residential, commercial and industrial property
                                            holders are required to pay property rates.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 3 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingGen3">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGen3"
                                            aria-expanded="false" aria-controls="collapseGen3">
                                            <p>Why do I need to pay property rates?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseGen3" class="accordion-collapse collapse"
                                        aria-labelledby="headingGen3" data-bs-parent="#accordionFAQ-General">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Property rates contribute to local development, including infrastructure,
                                            healthcare, education and other community services.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 4 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingGen4">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGen4"
                                            aria-expanded="false" aria-controls="collapseGen4">
                                            <p>How do I access the Property Rate Collection Platform?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseGen4" class="accordion-collapse collapse"
                                        aria-labelledby="headingGen4" data-bs-parent="#accordionFAQ-General">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            You can access the platform via the mobile app, USSD code or web portal.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 5 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingGen5">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGen5"
                                            aria-expanded="false" aria-controls="collapseGen5">
                                            <p> How do I create an account / register on the platform?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseGen5" class="accordion-collapse collapse"
                                        aria-labelledby="headingGen5" data-bs-parent="#accordionFAQ-General">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            • Visit the web portal or mobile app.<br>
                                            • Select "Create Account."<br>
                                            • Enter your details such as phone number and email.<br>
                                            • Verify your account through a code sent to your phone/email.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 6 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingGen6">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGen6"
                                            aria-expanded="false" aria-controls="collapseGen6">
                                            <p>What documents do I need to register?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseGen6" class="accordion-collapse collapse"
                                        aria-labelledby="headingGen6" data-bs-parent="#accordionFAQ-General">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            You need your valid national ID, property documents and contact details.
                                        </div>
                                    </div>
                                </div>
                                <!-- FAQ 23 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingSupport23">
                                        <button class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseSupport23" aria-expanded="false"
                                            aria-controls="collapseSupport23">
                                            <p> Can I request a bill statement?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseSupport23" class="accordion-collapse collapse"
                                        aria-labelledby="headingSupport23" data-bs-parent="#accordionFAQ-Support2">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Yes, you can request a bill statement under the "Billing" section of your
                                            account.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 25 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingAgent25">
                                        <button class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseAgent25" aria-expanded="false"
                                            aria-controls="collapseAgent25">
                                            <p>Who are the data collection agents?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseAgent25" class="accordion-collapse collapse"
                                        aria-labelledby="headingAgent25" data-bs-parent="#accordionFAQ-Agents1">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            They are authorized personnel assigned by the government to collect and
                                            verify property details.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 26 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingAgent26">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseAgent26" aria-expanded="false"
                                            aria-controls="collapseAgent26">
                                            <p>How do I know if an agent is legitimate?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseAgent26" class="accordion-collapse collapse"
                                        aria-labelledby="headingAgent26" data-bs-parent="#accordionFAQ-Agents1">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            All agents carry government-issued IDs and authorization letters. You can
                                            also verify their
                                            credentials on the platform.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 24 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingSupport24">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseSupport24" aria-expanded="false"
                                            aria-controls="collapseSupport24">
                                            <p>What should I do if I notice errors in my bill?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseSupport24" class="accordion-collapse collapse"
                                        aria-labelledby="headingSupport24" data-bs-parent="#accordionFAQ-Support2">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Submit a complaint through the "Support" section, detailing the errors for
                                            correction.
                                        </div>
                                    </div>
                                </div>
                                <!-- FAQ 31 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingGIS31">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGIS31"
                                            aria-expanded="false" aria-controls="collapseGIS31">
                                            <p>What if the map doesn’t show my property?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseGIS31" class="accordion-collapse collapse"
                                        aria-labelledby="headingGIS31" data-bs-parent="#accordionFAQ-GIS">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Contact support to investigate and update the property database accordingly.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 7 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingGen7">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGen7"
                                            aria-expanded="false" aria-controls="collapseGen7">
                                            <p> Can I register multiple properties under one account?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseGen7" class="accordion-collapse collapse"
                                        aria-labelledby="headingGen7" data-bs-parent="#accordionFAQ-General">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Yes, you can register multiple properties under your account using the "Add
                                            Property" feature.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="accordion accordion-customicon1 faq-accordion accordion-primary accordions-items-separate"
                                id="accordionFAQ1">
                                <!-- FAQ 30 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingGIS30">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGIS30"
                                            aria-expanded="false" aria-controls="collapseGIS30">
                                            <p>How do I locate my property using GIS?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseGIS30" class="accordion-collapse collapse"
                                        aria-labelledby="headingGIS30" data-bs-parent="#accordionFAQ-GIS">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Enter your property ID or coordinates in the GIS search bar to locate your
                                            property.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 9 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingProp9">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseProp9"
                                            aria-expanded="false" aria-controls="collapseProp9">
                                            <p>How do I find my property on the map?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseProp9" class="accordion-collapse collapse"
                                        aria-labelledby="headingProp9" data-bs-parent="#accordionFAQ-Property">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Use the platform’s GIS map feature. Enter your property ID or location to
                                            locate it on the map.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 10 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingProp10">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseProp10"
                                            aria-expanded="false" aria-controls="collapseProp10">
                                            <p>What should I do if my property is not listed?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseProp10" class="accordion-collapse collapse"
                                        aria-labelledby="headingProp10" data-bs-parent="#accordionFAQ-Property">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Register your property using the "Add Property" option or contact the
                                            support center for assistance.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 12 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingBill12">
                                        <button class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseBill12"
                                            aria-expanded="false" aria-controls="collapseBill12">
                                            <p>How do I check how much I owe?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseBill12" class="accordion-collapse collapse"
                                        aria-labelledby="headingBill12" data-bs-parent="#accordionFAQ-Billing1">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Log in to your account and navigate to the "Billing" section to view your
                                            outstanding property rate
                                            bills.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 13 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingBill13">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseBill13"
                                            aria-expanded="false" aria-controls="collapseBill13">
                                            <p>How do I pay my property rate?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseBill13" class="accordion-collapse collapse"
                                        aria-labelledby="headingBill13" data-bs-parent="#accordionFAQ-Billing1">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            1. Access the payment section on the platform.<br>
                                            2. Choose your preferred payment method (mobile money, online card payment,
                                            bank).<br>
                                            3. Confirm payment and download your receipt.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 14 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingBill14">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseBill14"
                                            aria-expanded="false" aria-controls="collapseBill14">
                                            <p>Can I pay for multiple properties at once?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseBill14" class="accordion-collapse collapse"
                                        aria-labelledby="headingBill14" data-bs-parent="#accordionFAQ-Billing1">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Yes, you can select multiple properties in the payment section and pay the
                                            total amount due.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 15 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingBill15">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseBill15"
                                            aria-expanded="false" aria-controls="collapseBill15">
                                            <p>What payment methods are supported?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseBill15" class="accordion-collapse collapse"
                                        aria-labelledby="headingBill15" data-bs-parent="#accordionFAQ-Billing1">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Supported methods include mobile money, credit/debit cards, bank transfers
                                            and cash payments at
                                            designated banks.
                                        </div>
                                    </div>
                                </div>
                                <!-- FAQ 16 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingBill16">
                                        <button class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseBill16"
                                            aria-expanded="false" aria-controls="collapseBill16">
                                            <p>How do I get a receipt after payment?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseBill16" class="accordion-collapse collapse"
                                        aria-labelledby="headingBill16" data-bs-parent="#accordionFAQ-Billing2">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Receipts are automatically generated after payment. You can download them
                                            from the "Payment History"
                                            section. In addition, you will receive an SMS and email notification on any
                                            payments made.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 17 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingBill17">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseBill17"
                                            aria-expanded="false" aria-controls="collapseBill17">
                                            <p>What happens if I miss a payment deadline?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseBill17" class="accordion-collapse collapse"
                                        aria-labelledby="headingBill17" data-bs-parent="#accordionFAQ-Billing2">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Late payments will incur penalties. Ensure you pay on time to avoid extra
                                            charges.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 18 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingBill18">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseBill18"
                                            aria-expanded="false" aria-controls="collapseBill18">
                                            <p>Can I set up recurring payments?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseBill18" class="accordion-collapse collapse"
                                        aria-labelledby="headingBill18" data-bs-parent="#accordionFAQ-Billing2">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Yes, you can schedule recurring payments for convenience.
                                        </div>
                                    </div>
                                </div>
                                <!-- FAQ 20 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingSupport20">
                                        <button class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseSupport20" aria-expanded="false"
                                            aria-controls="collapseSupport20">
                                            <p>How do I log a complaint?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseSupport20" class="accordion-collapse collapse"
                                        aria-labelledby="headingSupport20" data-bs-parent="#accordionFAQ-Support1">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Use the "Support" section on the platform to submit a ticket describing your
                                            issue.
                                            You will receive a reference number and SMS notification as well.
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 21 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingSupport21">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseSupport21" aria-expanded="false"
                                            aria-controls="collapseSupport21">
                                            <p>How do I contact customer support?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseSupport21" class="accordion-collapse collapse"
                                        aria-labelledby="headingSupport21" data-bs-parent="#accordionFAQ-Support1">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            You can reach support via the hotline, email or live chat on the platform.
                                        </div>
                                    </div>
                                </div>
                                <!-- FAQ 29 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingGIS29">
                                        <button class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGIS29"
                                            aria-expanded="false" aria-controls="collapseGIS29">
                                            <p>What is the GIS feature on the platform?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseGIS29" class="accordion-collapse collapse"
                                        aria-labelledby="headingGIS29" data-bs-parent="#accordionFAQ-GIS">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            The GIS feature visually maps properties and displays payment statuses,
                                            property locations, and
                                            valuations.
                                        </div>
                                    </div>
                                </div>





                                <!-- FAQ 22 -->
                                <div class="accordion-item shadow-sm">
                                    <h2 class="accordion-header" id="headingSupport22">
                                        <button
                                            class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseSupport22" aria-expanded="false"
                                            aria-controls="collapseSupport22">
                                            <p> What should I do if my payment doesn’t reflect?</p>
                                        </button>
                                    </h2>
                                    <div id="collapseSupport22" class="accordion-collapse collapse"
                                        aria-labelledby="headingSupport22" data-bs-parent="#accordionFAQ-Support1">
                                        <div class="accordion-body ps-3 m-3 my-3 border-white border-3 border-start text-default"
                                            style="background-color: #f374291a;">
                                            Contact support with your payment reference and transaction number for
                                            assistance.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- End:: Section-8 -->
            <!-- rts service post area  Start-->
            <div id="features" class="rts-service-area eight rts-section-gap">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="rts-title-area service text-center">
                                <p class="pre-title">
                                    FEATURES
                                </p>
                                <h2 class="title">Key Features of the IPRS</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container-fluid service-main plr--120-service mt--50 plr_md--0 pl_sm--0 pr_sm--0">
                    <div class="background-service row">
                        <!-- Feature 1 -->
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="service-one-inner one">
                                <div class="thumbnail">
                                    <img src="{{ asset('assets/assetsfront/images/service/icon/service-logo11.svg') }}"
                                        alt="finbiz_service">
                                </div>
                                <div class="service-details">
                                    <a href="#">
                                        <h5 class="title">Revenue Tracking</h5>
                                    </a>
                                    <p class="disc">Track collections in real time for full financial visibility.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Feature 2 -->
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="service-one-inner two">
                                <div class="thumbnail">
                                    <img src="{{ asset('assets/assetsfront/images/service/icon/service-logo4.svg') }}"
                                        alt="finbiz_service">
                                </div>
                                <div class="service-details">
                                    <a href="#">
                                        <h5 class="title">Intuitive Interface</h5>
                                    </a>
                                    <p class="disc">Easy-to-use platform for both citizens and authorities.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Feature 3 -->
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="service-one-inner three">
                                <div class="thumbnail">
                                    <img src="{{ asset('assets/assetsfront/images/service/icon/service-logo5.svg') }}"
                                        alt="finbiz_service">
                                </div>
                                <div class="service-details">
                                    <a href="#">
                                        <h5 class="title">Data Security</h5>
                                    </a>
                                    <p class="disc">Advanced encryption keeps your data protected.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Feature 4 -->
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="service-one-inner four">
                                <div class="thumbnail">
                                    <img src="{{ asset('assets/assetsfront/images/service/icon/service-logo6.svg') }}"
                                        alt="finbiz_service">
                                </div>
                                <div class="service-details">
                                    <a href="#">
                                        <h5 class="title">Smart Notifications</h5>
                                    </a>
                                    <p class="disc">Get alerts on deadlines and important updates instantly.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Feature 5 -->
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="service-one-inner five">
                                <div class="thumbnail">
                                    <img src="{{ asset('assets/assetsfront/images/service/icon/service-logo22.svg') }}"
                                        alt="finbiz_service">
                                </div>
                                <div class="service-details">
                                    <a href="#">
                                        <h5 class="title">Flexible Payments</h5>
                                    </a>
                                    <p class="disc">Multiple secure channels for convenient payments.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Feature 6 -->
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="service-one-inner six">
                                <div class="thumbnail">
                                    <img src="{{ asset('assets/assetsfront/images/service/icon/service-logo33.svg') }}"
                                        alt="finbiz_service">
                                </div>
                                <div class="service-details">
                                    <a href="#">
                                        <h5 class="title">Local Impact</h5>
                                    </a>
                                    <p class="disc">Funds directly support community development projects.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- rts service post area ENd -->

            <!-- start about our company -->
            <div id="about-us" class="rts-about-our-company-h2 eight rts-section-gap">
                <div class="container">
                    <div class="row">
                        <div class="col-12 mt_sm--30">
                            <div class="title-area about-company">
                                <span>About IPRS</span>
                                <h2 class="title">Empowering Communities with Efficient Rate Solutions</h2>
                            </div>
                            <div class="about-company-wrapper">
                                <p class="disc">
                                    The Integrated Property Rate System (IPRS) is designed to revolutionize the way
                                    property
                                    rates are managed and collected across Africa. Our platform offers a seamless,
                                    transparent,
                                    and efficient solution for both citizens and local governments.
                                </p>
                                <div class="rts-tab-style-one">
                                    <div class="d-flex align-items-start contoler-company">
                                        <div class="nav flex-column nav-pills me-3 button-area" id="v-pills-tab"
                                            role="tablist" aria-orientation="vertical">
                                            <button class="nav-link active" id="v-pills-home-tab"
                                                data-bs-toggle="pill" data-bs-target="#v-pills-home" type="button"
                                                role="tab" aria-controls="v-pills-home" aria-selected="true">01.
                                                Transparency</button>
                                            <button class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill"
                                                data-bs-target="#v-pills-profile" type="button" role="tab"
                                                aria-controls="v-pills-profile" aria-selected="false">02.
                                                Efficiency</button>
                                            <button class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill"
                                                data-bs-target="#v-pills-messages" type="button" role="tab"
                                                aria-controls="v-pills-messages" aria-selected="false">03.
                                                Security</button>
                                            <button class="nav-link" id="v-pills-manufacture-tab"
                                                data-bs-toggle="pill" data-bs-target="#v-pills-manufacture"
                                                type="button" role="tab" aria-controls="v-pills-manufacture"
                                                aria-selected="false">04. Community
                                                Impact</button>
                                        </div>
                                        <div class="tab-content" id="v-pills-tabContent">
                                            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
                                                aria-labelledby="v-pills-home-tab">
                                                <!-- start tab content -->
                                                <div class="rts-tab-content-one">

                                                    <p class="disc">
                                                        IPRS ensures complete transparency in the rate collection
                                                        process.
                                                        Citizens can easily track their payments and see how their
                                                        contributions
                                                        are being utilized for community development.
                                                    </p>
                                                    <div class="check-area">
                                                        <i class="fas fa-check-circle"></i>
                                                        <p class="disc">
                                                            Real-time revenue tracking for accountability.
                                                        </p>
                                                    </div>
                                                    <div class="check-area">
                                                        <i class="fas fa-check-circle"></i>
                                                        <p class="disc">
                                                            Detailed reports and analytics for informed decision-making.
                                                        </p>
                                                    </div>
                                                    <a class="rts-btn btn-primary-2 color-h-black"
                                                        href="{{ route('auth.register') }}">Sign Up
                                                    </a>
                                                </div>
                                                <!-- start tab content End -->
                                            </div>
                                            <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                                aria-labelledby="v-pills-profile-tab">
                                                <!-- start tab content -->
                                                <div class="rts-tab-content-one">

                                                    <p class="disc">
                                                        Our system streamlines the entire rate collection process,
                                                        reducing
                                                        administrative burdens and ensuring timely payments. This
                                                        efficiency
                                                        translates to better services for the community.
                                                    </p>
                                                    <div class="check-area">
                                                        <i class="fas fa-check-circle"></i>
                                                        <p class="disc">
                                                            Automated notifications and reminders.
                                                        </p>
                                                    </div>
                                                    <div class="check-area">
                                                        <i class="fas fa-check-circle"></i>
                                                        <p class="disc">
                                                            User-friendly interface for easy navigation.
                                                        </p>
                                                    </div>
                                                    <a class="rts-btn btn-primary-2 color-h-black" href="">
                                                        Sign Up</a>
                                                </div>
                                                <!-- start tab content End -->
                                            </div>
                                            <div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
                                                aria-labelledby="v-pills-messages-tab">
                                                <!-- start tab content -->
                                                <div class="rts-tab-content-one">

                                                    <p class="disc">
                                                        Security is at the core of IPRS. We employ state-of-the-art
                                                        security
                                                        measures to protect your personal and financial data, ensuring
                                                        peace of
                                                        mind for all users.
                                                    </p>
                                                    <div class="check-area">
                                                        <i class="fas fa-check-circle"></i>
                                                        <p class="disc">
                                                            Advanced encryption and data protection.
                                                        </p>
                                                    </div>
                                                    <div class="check-area">
                                                        <i class="fas fa-check-circle"></i>
                                                        <p class="disc">
                                                            Regular security audits and updates.
                                                        </p>
                                                    </div>
                                                    <a class="rts-btn btn-primary-2 color-h-black"
                                                        href="{{ route('auth.register') }}">Sign Up
                                                    </a>
                                                </div>
                                                <!-- start tab content End -->
                                            </div>
                                            <div class="tab-pane fade" id="v-pills-manufacture" role="tabpanel"
                                                aria-labelledby="v-pills-manufacture-tab">
                                                <!-- start tab content -->
                                                <div class="rts-tab-content-one">

                                                    <p class="disc">
                                                        Every rate payment made through IPRS directly contributes to the
                                                        development of local infrastructure and public services,
                                                        fostering
                                                        growth and improving the quality of life in communities.
                                                    </p>
                                                    <div class="check-area">
                                                        <i class="fas fa-check-circle"></i>
                                                        <p class="disc">
                                                            Investment in local projects and services.
                                                        </p>
                                                    </div>
                                                    <div class="check-area">
                                                        <i class="fas fa-check-circle"></i>
                                                        <p class="disc">
                                                            Empowering local governments with better resources.
                                                        </p>
                                                    </div>
                                                    <a class="rts-btn btn-primary-2 color-h-black"
                                                        href="{{ route('auth.register') }}">
                                                        Sign Up</a>
                                                </div>
                                                <!-- start tab content End -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- start header area -->
            <!-- footer area start -->
            <div id="contact"
                class="rts-footer-area rts-section-gap footer-two footer-bg-two mt--120 mt_md--80 mt_sm--60">
                <div class="container">
                    <div class="row">
                        <!-- single wized -->
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="footer-two-single-wized left">
                                <h3 class="title">
                                    <span>Ready To</span> <br>
                                    Get Started?
                                </h3>
                                <p class="disc">
                                    Discover a smarter, simpler way to manage property rates with IPRS. Join thousands
                                    already
                                    transforming their experience today!
                                </p>
                                <a class="rts-btn btn-primary-2 color-h-black"
                                    href="{{ route('auth.register') }}">Sign
                                    Up</a>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mt_sm--50">
                            <div class="footer-two-single-wized two">
                                <div class="wized-title-area">
                                    <h5 class="wized-title">IPRS Features</h5>
                                    <img src="{{ asset('assets/assetsfront/images/footer/under-title-2.png') }}"
                                        alt="finbiz_footer">
                                </div>
                                <div class="wized-2-body">
                                    <ul>
                                        <li><a href="#"><i class="fal fa-chevron-double-right"></i>Real-Time
                                                Revenue
                                                Tracking</a></li>
                                        <li><a href="#"><i class="fal fa-chevron-double-right"></i>User-Friendly
                                                Interface</a></li>
                                        <li><a href="#"><i class="fal fa-chevron-double-right"></i>Innovative
                                                Solutions</a></li>
                                        <li><a href="#"><i class="fal fa-chevron-double-right"></i>Automated
                                                Notifications</a></li>
                                        <li><a href="#"><i class="fal fa-chevron-double-right"></i>Enhanced
                                                Data Security</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- single wized -->
                        <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 mt_sm--30 mt_md--30">
                            <div class="footer-two-single-wized">
                                <div class="wized-title-area">
                                    <h5 class="wized-title">Contact Us</h5>
                                    <img src="{{ asset('assets/assetsfront/images/footer/under-title-2.png') }}"
                                        alt="finbiz_footer">
                                </div>
                                <div class="wized-2-body">
                                    <div class="contact-info-1">
                                        <div class="icon">
                                            <i class="fas fa-phone-alt"></i>
                                        </div>
                                        <div class="disc">
                                            <span>Call Us 24/7</span>
                                            <a href="+233500503599">+233 50 050 3599</a>
                                        </div>
                                    </div>
                                    <div class="contact-info-1">
                                        <div class="icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="disc">
                                            <span>Work with us</span>
                                            <a href="mailto:info@level10gh.com">info@level10gh.com</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- single wized -->
                        <!-- single wized -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="footer-two-single-wized right">
                                <div class="wized-2-body">
                                    <div class="contact-info-1">
                                        <div class="icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="disc">
                                            <span>Our Location</span>
                                            <a
                                                href="https://www.google.com/maps/dir/5.7864728,-0.1258667/29+Senchi+St,+Accra/@5.7023691,-0.235622,12z/data=!3m1!4b1!4m9!4m8!1m1!4e1!1m5!1m1!1s0xfdf9b0fa4eb345b:0x2f32d290edf99916!2m2!1d-0.1794308!2d5.6159487?entry=tts&g_ep=EgoyMDI1MDEwMi4wIPu8ASoASAFQAw%3D%3D">
                                                29 Senchi Street,Airport Residential, Accra Ghana <br>
                                                United State</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- single wized -->
                    </div>
                </div>
            </div>
            <!-- footer area end -->

            <!-- copyright-area start -->
            <div class="rts-copy-right ptb--30">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="copyright-h-2-wrapper">
                                <p class="disc">
                                    IPRS - Copyright 2025. All rights reserved.
                                </p>
                                <div class="right">
                                    <ul>
                                        <li><a href="https://level10gh.com/">Powered By Level 10 </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- copyright-area end -->
            <!-- ENd Header Area -->


            <!-- start loader -->
            <div class="loader-wrapper">
                <div class="loader">
                </div>
                <div class="loader-section section-left"></div>
                <div class="loader-section section-right"></div>
            </div>
            <!-- End loader -->

            <!-- progress Back to top -->
            <div class="progress-wrap">
                <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                    <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
                </svg>
            </div>
            <!-- progress Back to top End -->


            <!-- scripts start form hear -->
            <script src="{{ asset('assets/assetsfront/js/vendor/jquery.min.js') }}"></script>
            <script src="{{ asset('assets/assetsfront/js/vendor/jqueryui.js') }}"></script>
            <script src="{{ asset('assets/assetsfront/js/vendor/waypoint.js') }}"></script>
            <script src="{{ asset('assets/assetsfront/js/plugins/swiper.js') }}"></script>
            <script src="{{ asset('assets/assetsfront/js/plugins/counterup.js') }}"></script>
            <script src="{{ asset('assets/assetsfront/js/plugins/sal.min.js') }}"></script>
            <script src="{{ asset('assets/assetsfront/js/vendor/bootstrap.min.js') }}"></script>
            <script src="{{ asset('assets/assetsfront/js/vendor/waw.js') }}"></script>
            <script src="{{ asset('assets/assetsfront/js/plugins/contact.form.js') }}"></script>
            <!-- main Js -->
            <script src="{{ asset('assets/assetsfront/js/main.js') }}"></script>
            <!-- scripts end form hear -->
</body>



</html>
