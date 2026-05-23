@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">FAQs</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Pages</a></li>
                        <li class="breadcrumb-item active" aria-current="page">FAQs</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Page Header Close -->

        <!-- Start:: row-2 -->
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div
                            class="row justify-content-center border-bottom border-block-end-dashed mb-4 p-3 mx-0 bg-primary rounded text-fixed-white">
                            <div class="col-xxl-12">
                                <div class="py-2">
                                    <div class="text-center">
                                        <h3 class="text-fixed-white mb-3">Frequently Asked </h3>
                                        <h5 class="d-block text-fixed-white op-8">Require assistance? Here are some of our
                                            commonly asked questions!</h5>
                                        <p class="op-7 mb-2 px-4 text-fixed-white">Discover answers to common queries and
                                            find solutions to your concerns with our comprehensive list of frequently asked
                                            questions.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row gy-3 gy-xxl-0">
                            <div class="col-xxl-12">
                                <ul class="nav nav-tabs tab-style-1 d-sm-flex d-block bg-transparent border border-dashed"
                                    role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page"
                                            href="#account-settings" aria-selected="false" tabindex="-1">General
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                            href="#privacy-and-security" aria-selected="true">Account Registration</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                            href="#billing-and-payments" aria-selected="false" tabindex="-1">Property
                                            Management

                                        </a>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                            href="#bills-and-payment" aria-selected="false" tabindex="-1">Bills and
                                            Payments


                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                            href="#support-and-complains" aria-selected="false" tabindex="-1">Support and
                                            Complains

                                        </a>
                                    </li>


                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane border-0 bg-primary-transparent show active" id="account-settings"
                                        role="tabpanel">
                                        <div class="accordion accordion-customicon1 faq-accordion accordion-primary accordions-items-separate"
                                            id="accordionFAQ5">
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2TwentyOne">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2TwentyOne" aria-expanded="true"
                                                        aria-controls="collapsecustomicon2TwentyOne">
                                                        1. What is the Property Rate Collection Platform?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2TwentyOne"
                                                    class="accordion-collapse collapse show"
                                                    aria-labelledby="headingcustomicon2TwentyOne"
                                                    data-bs-parent="#accordionFAQ5">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        The platform is a digital system designed by the Government of Ghana
                                                        to streamline property rate collection, manage taxpayer information
                                                        and ensure easy access to payment services.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2TwentyTwo">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2TwentyTwo" aria-expanded="false"
                                                        aria-controls="collapsecustomicon2TwentyTwo">
                                                        2. Who is required to pay property rates?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2TwentyTwo" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2TwentyTwo"
                                                    data-bs-parent="#accordionFAQ5">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        Property owners, including residential, commercial and industrial
                                                        property holders are required to pay property rates.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2TwentyThree">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2TwentyThree"
                                                        aria-expanded="false"
                                                        aria-controls="collapsecustomicon2TwentyThree">
                                                        3. Why do I need to pay property rates?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2TwentyThree"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2TwentyThree"
                                                    data-bs-parent="#accordionFAQ5">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        Property rates contribute to local development, including
                                                        infrastructure, healthcare, education and other community services.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2TwentyFour">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2TwentyFour"
                                                        aria-expanded="false"
                                                        aria-controls="collapsecustomicon2TwentyFour">
                                                        4. How do I access the Property Rate Collection Platform?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2TwentyFour"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2TwentyFour"
                                                    data-bs-parent="#accordionFAQ5">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        You can access the platform via the mobile app, USSD code or web
                                                        portal.
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="tab-pane border-0 bg-primary-transparent" id="privacy-and-security"
                                        role="tabpanel">
                                        <div class="accordion accordion-customicon1 faq-accordion accordion-primary accordions-items-separate"
                                            id="accordionFAQ1">
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2One">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2One" aria-expanded="true"
                                                        aria-controls="collapsecustomicon2One">
                                                        1. How do I create an account / register on the platform?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2One" class="accordion-collapse collapse show"
                                                    aria-labelledby="headingcustomicon2One"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        o Visit the web portal or mobile app.</br>
                                                        o Select "Create Account."</br>
                                                        o Enter your details such as phone number and email.</br>
                                                        o Verify your account through a code sent to your phone/email.</br>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Two">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Two" aria-expanded="false"
                                                        aria-controls="collapsecustomicon2Two">
                                                        2. What documents do I need to register?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Two" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2Two"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        You need your valid national ID, property documents and contact
                                                        details.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Three">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Three" aria-expanded="false"
                                                        aria-controls="collapsecustomicon2Three">
                                                        3. Can I register multiple properties under one account?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Three" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2Three"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        Yes, you can register multiple properties under your account using
                                                        the "Add Property" feature.
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="tab-pane border-0 bg-primary-transparent" id="billing-and-payments"
                                        role="tabpanel">
                                        <div class="accordion accordion-customicon1 faq-accordion accordion-primary accordions-items-separate"
                                            id="accordionFAQ3">
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Eleven">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Eleven" aria-expanded="true"
                                                        aria-controls="collapsecustomicon2Eleven">
                                                        1.How do I add my property to the platform?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Eleven"
                                                    class="accordion-collapse collapse show"
                                                    aria-labelledby="headingcustomicon2Eleven"
                                                    data-bs-parent="#accordionFAQ3">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        o Log in to your account.</br>
                                                        o Select "Add Property."</br>
                                                        o Enter your property details, such as location, size and valuation
                                                        information.</br>
                                                        o Upload supporting documents.</br>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Twelve">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Twelve" aria-expanded="false"
                                                        aria-controls="collapsecustomicon2Twelve">
                                                        2. How do I find my property on the map?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Twelve" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2Twelve"
                                                    data-bs-parent="#accordionFAQ3">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        Use the platform’s GIS map feature. Enter your property ID or
                                                        location to locate it on the map.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Thirteen">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Thirteen"
                                                        aria-expanded="false" aria-controls="collapsecustomicon2Thirteen">
                                                        3. What should I do if my property is not listed?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Thirteen" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2Thirteen"
                                                    data-bs-parent="#accordionFAQ3">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        Register your property using the "Add Property" option or contact
                                                        the support center for assistance.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Thirteen">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Thirteen1"
                                                        aria-expanded="false"
                                                        aria-controls="collapsecustomicon2Thirteen1">
                                                        4. Can I update my property details?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Thirteen1" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2Thirteen"
                                                    data-bs-parent="#accordionFAQ3">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        Yes, log in to your account and select the property you want to
                                                        update. Submit the changes for approval.
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="tab-pane border-0 bg-primary-transparent" id="bills-and-payment"
                                        role="tabpanel">
                                        <div class="accordion accordion-customicon1 faq-accordion accordion-primary accordions-items-separate"
                                            id="accordionFAQ1">
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2One">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2One" aria-expanded="true"
                                                        aria-controls="collapsecustomicon2One">
                                                        1. How do I check how much I owe?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2One" class="accordion-collapse collapse show"
                                                    aria-labelledby="headingcustomicon2One"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        Log in to your account and navigate to the "Billing" section to view
                                                        your outstanding property rate bills.

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Two">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Two" aria-expanded="false"
                                                        aria-controls="collapsecustomicon2Two">
                                                        2. How do I pay my property rate?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Two" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2Two"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        1. Access the payment section on the platform.</br>
                                                        2. Choose your preferred payment method (mobile money (MTN, Telecel
                                                        & AT), online card payment (Visa/MasterCard, bank).</br>
                                                        3. Confirm payment and download your receipt.</br>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Three">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Three" aria-expanded="false"
                                                        aria-controls="collapsecustomicon2Three">
                                                        3. Can I pay for multiple properties at once?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Three" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2Three"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        Yes, you can select multiple properties in the payment section and
                                                        pay the total amount due.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Three3">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Three3" aria-expanded="false"
                                                        aria-controls="collapsecustomicon2Three">
                                                        4. What payment methods are supported?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Three3" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2Three3"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        Supported methods include mobile money, credit/debit cards, bank
                                                        transfers and cash payments at designated banks.
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Question 16 -->
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Sixteen">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Sixteen" aria-expanded="false"
                                                        aria-controls="collapsecustomicon2Sixteen">
                                                        5. How do I get a receipt after payment?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Sixteen" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2Sixteen"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-success border-3 border-start text-default bg-success bg-opacity-10">
                                                        Receipts are automatically generated after payment. You can download
                                                        them from the "Payment History" section.
                                                        In addition, you will receive an SMS and email notification on any
                                                        payments made.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Question 17 -->
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Seventeen">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Seventeen"
                                                        aria-expanded="false"
                                                        aria-controls="collapsecustomicon2Seventeen">
                                                        6. What happens if I miss a payment deadline?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Seventeen" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2Seventeen"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-danger border-3 border-start text-default bg-danger bg-opacity-10">
                                                        Late payments will incur penalties. Ensure you pay on time to avoid
                                                        extra charges.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Question 18 -->
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Eighteen">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Eighteen"
                                                        aria-expanded="false" aria-controls="collapsecustomicon2Eighteen">
                                                        7. Can I set up recurring payments?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Eighteen" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2Eighteen"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-info border-3 border-start text-default bg-info bg-opacity-10">
                                                        Yes, you can schedule recurring payments for convenience.
                                                    </div>
                                                </div>
                                            </div>



                                        </div>
                                    </div>
                                    <div class="tab-pane border-0 bg-primary-transparent" id="support-and-complains"
                                        role="tabpanel">
                                        <div class="accordion accordion-customicon1 faq-accordion accordion-primary accordions-items-separate"
                                            id="accordionFAQ1">
                                            <!-- Question 20 -->
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2Twenty">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2Twenty" aria-expanded="false"
                                                        aria-controls="collapsecustomicon2Twenty">
                                                        1. How do I log a complaint?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2Twenty" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2Twenty"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-warning border-3 border-start text-default bg-warning bg-opacity-10">
                                                        Use the "Support" section on the platform to submit a ticket
                                                        describing your issue.
                                                        You will receive a reference number and SMS notification as well.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Question 21 -->
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2TwentyOne">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2TwentyOne"
                                                        aria-expanded="false"
                                                        aria-controls="collapsecustomicon2TwentyOne">
                                                        2. How do I contact customer support?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2TwentyOne" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2TwentyOne"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-primary border-3 border-start text-default bg-primary bg-opacity-10">
                                                        You can reach support via the hotline, email, or live chat on the
                                                        platform.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Question 22 -->
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2TwentyTwo">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2TwentyTwo"
                                                        aria-expanded="false"
                                                        aria-controls="collapsecustomicon2TwentyTwo">
                                                        3. What should I do if my payment doesn’t reflect?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2TwentyTwo" class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2TwentyTwo"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-danger border-3 border-start text-default bg-danger bg-opacity-10">
                                                        Contact support with your payment reference and transaction number
                                                        for assistance.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Question 23 -->
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2TwentyThree">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2TwentyThree"
                                                        aria-expanded="false"
                                                        aria-controls="collapsecustomicon2TwentyThree">
                                                        4. Can I request a bill statement?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2TwentyThree"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2TwentyThree"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-info border-3 border-start text-default bg-info bg-opacity-10">
                                                        Yes, you can request a bill statement under the "Billing" section of
                                                        your account.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Question 24 -->
                                            <div class="accordion-item shadow-sm">
                                                <h2 class="accordion-header" id="headingcustomicon2TwentyFour">
                                                    <button
                                                        class="accordion-button fs-14 fw-medium pt-3 pb-2 bg-transparent collapsed"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapsecustomicon2TwentyFour"
                                                        aria-expanded="false"
                                                        aria-controls="collapsecustomicon2TwentyFour">
                                                        5. What should I do if I notice errors in my bill?
                                                    </button>
                                                </h2>
                                                <div id="collapsecustomicon2TwentyFour"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="headingcustomicon2TwentyFour"
                                                    data-bs-parent="#accordionFAQ1">
                                                    <div
                                                        class="accordion-body ps-3 m-3 my-3 border-warning border-3 border-start text-default bg-warning bg-opacity-10">
                                                        Submit a complaint through the "Support" section, detailing the
                                                        errors for correction.
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End:: row-2 -->

    </div>
@endsection

@section('page-scripts')
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/663e4f9b9a809f19fb2fa32d/1hthme206';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    <!--End of Tawk.to Script-->
    <script src="{{ asset('assets/js/general/app.js?t=1234') }}"></script>
@endsection
