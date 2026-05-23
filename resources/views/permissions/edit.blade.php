@extends('layout.base')

@section('page-content')
    <div class="container-fluid">
        <div class="row">

            <div class="card">
                <div class="card-body border-bottom pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h4 class="fw-bold text-primary mb-1">
                                <i class="ri-lock-line me-1"></i> Permission Management
                            </h4>
                            <p class="mb-0 text-muted fs-14">
                                Easily manage user access by creating, editing, and viewing all defined system roles.
                            </p>
                        </div>
                        @can('roles.create')
                            <a href="{{ route('permissions.index') }}" class="btn btn-primary">
                                <i class="fa fa-plus me-1"></i> Back To Permission Management
                            </a>
                        @endcan
                    </div>
                </div>

                @if (session()->has('status'))
                    <div class="alert alert-success alert-dismissible fade show m-4">
                        <strong>{{ session('status') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>


            <div class="card">
                <div class="card-body">
                    <form class="row g-3 needs-validation" method="POST"
                        action="{{ route('permissions.update', $role->id) }}">
                        @csrf

                        <div class="col-md-12 mb-4">
                            <label for="role_name" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="role_name" name="name"
                                value="{{ $role->name }}" readonly>
                        </div>

                        <div class="my-4">
                            <h5 class="text-success fw-bold mb-2">
                                <i class="ri-lock-line me-1"></i> Assign Permissions To: <span
                                    class="text-dark">{{ $role->name }}</span>
                            </h5>
                            <p class="text-muted mb-3">Check the boxes below to enable specific features for this
                                role.
                            </p>
                            <hr />
                        </div>

                        <!-- Dashboards Group -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-dashboard-line me-2"></i> @lang('role.dashboards')
                                </h5>
                                <label class="form-check-label d-flex align-items-center">
                                    <input class="form-check-input check_all me-2" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input me-2" name="permissions[]" type="checkbox"
                                            value="dashboards.operational" @if (in_array('dashboards.operational', $role_permissions)) checked @endif>
                                        <label class="form-check-label">
                                            <i class="ri-bar-chart-box-line me-1 text-success"></i>
                                            @lang('role.dashboards.operational')
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input me-2" name="permissions[]" type="checkbox"
                                            value="dashboards.financial" @if (in_array('dashboards.financial', $role_permissions)) checked @endif>
                                        <label class="form-check-label">
                                            <i class="ri-bar-chart-box-line me-1 text-success"></i>
                                            @lang('role.dashboards.financial')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- USERS PERMISSION GROUP -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-user-3-line me-2"></i> @lang('role.users')
                                </h5>
                                <label class="d-block mb-0">
                                    <input class="form-check-input check_all" type="checkbox"> @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]" type="checkbox"
                                            value="@lang('users.view')" @if (in_array('users.view', $role_permissions)) checked @endif>
                                        @lang('role.users.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-user-add-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]" type="checkbox"
                                            value="@lang('users.create')" @if (in_array('users.create', $role_permissions)) checked @endif>
                                        @lang('role.users.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]" type="checkbox"
                                            value="@lang('users.update')" @if (in_array('users.update', $role_permissions)) checked @endif>
                                        @lang('role.users.update')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- roles -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-admin-line me-2"></i> @lang('role.user_roles')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('roles.view')" @if (in_array('roles.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.roles.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-add-circle-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('roles.create')" @if (in_array('roles.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.roles.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('roles.update')" @if (in_array('roles.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.roles.update')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- permissions -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-key-line me-2"></i> @lang('role.permissions')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('permissions.view')" @if (in_array('permissions.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.permissions.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-add-circle-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('permissions.create')" @if (in_array('permissions.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.permissions.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('permissions.update')" @if (in_array('permissions.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.permissions.update')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- customers -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-user-line me-2"></i> @lang('role.customers')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('customers.view')" @if (in_array('customers.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.customers.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-user-add-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('customers.create')" @if (in_array('customers.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.customers.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('customers.update')" @if (in_array('customers.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.customers.update')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- customer types -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-group-line me-2"></i> @lang('role.customer-types')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('customer-types.view')" @if (in_array('customer-types.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.customer-types.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-user-add-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('customer-types.create')" @if (in_array('customer-types.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.customer-types.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('customer-types.update')" @if (in_array('customer-types.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.customer-types.update')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- properties -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-home-gear-line me-2"></i> @lang('role.properties')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('properties.view')" @if (in_array('properties.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.properties.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-add-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('properties.create')" @if (in_array('properties.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.properties.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('properties.update')" @if (in_array('properties.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.properties.update')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- property types -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-building-line me-2"></i> @lang('role.property-types')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('property-types.view')" @if (in_array('property-types.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.property-types.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-add-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('property-types.create')" @if (in_array('property-types.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.property-types.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('property-types.update')" @if (in_array('property-types.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.property-types.update')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- business types -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-briefcase-line me-2"></i> @lang('role.business-types')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('business-types.view')" @if (in_array('business-types.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.business-types.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-add-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('business-types.create')" @if (in_array('business-types.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.business-types.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('business-types.update')" @if (in_array('business-types.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.business-types.update')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- businesses -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-store-2-line me-2"></i> @lang('role.businesses')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('businesses.view')" @if (in_array('businesses.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.businesses.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-add-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('businesses.create')" @if (in_array('businesses.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.businesses.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('businesses.update')" @if (in_array('businesses.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.businesses.update')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- bills -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-file-list-2-line me-2"></i> @lang('role.bills')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('bills.view')" @if (in_array('bills.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.bills.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-add-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('bills.create')" @if (in_array('bills.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.bills.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('bills.update')" @if (in_array('bills.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.bills.update')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- payments -->
                        <!-- payments -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-money-dollar-box-line me-2"></i> @lang('role.payments')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('payments.view')" @if (in_array('payments.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.payments.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-add-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('payments.create')" @if (in_array('payments.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.payments.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('payments.update')" @if (in_array('payments.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.payments.update')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- assemblies -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-building-line me-2"></i> @lang('role.assemblies')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('assemblies.view')" @if (in_array('assemblies.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.assemblies.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-add-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('assemblies.create')" @if (in_array('assemblies.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.assemblies.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('assemblies.update')" @if (in_array('assemblies.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.assemblies.update')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- divisions -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-community-line me-2"></i> @lang('role.divisions')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('divisions.view')" @if (in_array('divisions.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.divisions.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-add-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('divisions.create')" @if (in_array('divisions.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.divisions.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('divisions.update')" @if (in_array('divisions.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.divisions.update')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- blocks -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-building-2-line me-2"></i> @lang('role.blocks')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('blocks.view')" @if (in_array('blocks.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.blocks.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-add-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('blocks.create')" @if (in_array('blocks.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.blocks.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('blocks.update')" @if (in_array('blocks.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.blocks.update')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- zones -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-map-pin-line me-2"></i> @lang('role.zones')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-eye-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('zones.view')" @if (in_array('zones.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.zones.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-add-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('zones.create')" @if (in_array('zones.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.zones.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <i class="ri-edit-box-line me-2 text-success"></i>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('zones.update')" @if (in_array('zones.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.zones.update')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- property-uses -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-home-line me-2"></i> @lang('role.property-uses')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-eye-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('property-uses.view')" @if (in_array('property-uses.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.property-uses.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-add-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('property-uses.create')" @if (in_array('property-uses.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.property-uses.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-edit-2-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('property-uses.update')" @if (in_array('property-uses.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.property-uses.update')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- rates -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-money-dollar-circle-line me-2"></i> @lang('role.rates')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-eye-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('rates.view')" @if (in_array('rates.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.rates.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-add-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('rates.create')" @if (in_array('rates.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.rates.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-edit-2-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('rates.update')" @if (in_array('rates.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.rates.update')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- reports -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-file-chart-line me-2"></i> @lang('role.reports')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-eye-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('reports.view')" @if (in_array('reports.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.reports.view')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- agent-assignments -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-user-settings-line me-2"></i> @lang('role.agent-assignments')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-eye-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('agent-assignments.view')" @if (in_array('agent-assignments.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.agent-assignments.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-add-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('agent-assignments.create')" @if (in_array('agent-assignments.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.agent-assignments.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-edit-2-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('agent-assignments.update')" @if (in_array('agent-assignments.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.agent-assignments.update')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- task management -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-clipboard-line me-2"></i> @lang('role.task-assignments')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-eye-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('task-assignments.view')" @if (in_array('task-assignments.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.task-assignments.view')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-add-circle-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('task-assignments.create')" @if (in_array('task-assignments.create', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.task-assignments.create')
                                    </label>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-edit-2-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('task-assignments.update')" @if (in_array('task-assignments.update', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.task-assignments.update')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- analytics -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-pie-chart-2-line me-2"></i> @lang('role.analytics')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-eye-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('analytics.view')" @if (in_array('analytics.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.analytics.view')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- nationwide -->
                        <div class="card mb-4 permission-group">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <h5 class="mb-0 text-uppercase text-primary">
                                    <i class="ri-global-line me-2"></i> @lang('role.nationwide')
                                </h5>
                                <label class="d-block">
                                    <input class="form-check-input check_all" type="checkbox">
                                    @lang('role.select_all')
                                </label>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-check-label d-flex align-items-center">
                                        <span><i class="ri-eye-line me-1 text-success"></i></span>
                                        <input class="form-check-input me-2" name="permissions[]"
                                            value="@lang('nationwide.view')" @if (in_array('nationwide.view', $role_permissions)) checked @endif
                                            type="checkbox">
                                        @lang('role.nationwide.view')
                                    </label>
                                </div>
                            </div>
                        </div>














                        <!-- Submit Button -->
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">
                                <i class="ri-save-line me-1"></i> Update Permissions
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @section('page-scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const groups = document.querySelectorAll('.permission-group');
                groups.forEach(group => {
                    const selectAll = group.querySelector('.check_all');
                    const checkboxes = group.querySelectorAll('input[type="checkbox"]:not(.check_all)');
                    selectAll.addEventListener('change', function() {
                        checkboxes.forEach(cb => cb.checked = selectAll.checked);
                    });
                });
            });
        </script>
    @endsection
