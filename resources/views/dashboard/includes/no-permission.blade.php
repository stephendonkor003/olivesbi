<div class="row justify-content-center mt-5">
    <div class="col-xl-8 col-lg-10">
        <div class="card custom-card shadow-lg border-0">
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <i class="ri-dashboard-line text-primary" style="font-size: 4rem;"></i>
                </div>
                <h3 class="fw-bold text-dark mb-3">Welcome, {{ Auth::user()->name }}!</h3>
                <p class="text-muted fs-5 mb-4">
                    We're glad to have you here.
                </p>
                <div class="alert alert-info border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ri-information-line fs-5 me-3 mt-1"></i>
                        <div class="text-start">
                            <h6 class="fw-semibold mb-2">Dashboard Access</h6>
                            <p class="mb-0">
                                Your account currently has limited access to the operational dashboard.
                                If you believe you should have access to additional features, please contact your system administrator.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
