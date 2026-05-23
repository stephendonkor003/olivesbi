@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="card">

            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-building-line me-2"></i> Business Type Management
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are Editing A Business Type Record from your
                            central database repository.
                        </p>

                    </div>
                    @can('business-types.create')
                        <a href="{{ route('business-types.index') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-arrow-left me-1"></i> Back
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST"
                            action="{{ route('business-types.update', $businessType) }}">
                            @csrf

                            <div class="col-sm-6 mb-3">
                                <label for="class_name" class="form-label"> Class Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Class Name"
                                    value="{{ $businessType->name }}">

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="parent_category" class="form-label"> Category</label>
                                <input type="text" class="form-control @error('parent_category') is-invalid @enderror"
                                    id="parent_category" name="parent_category" placeholder="Parent category"
                                    value="{{ $businessType->parent_category }}">

                                @error('parent_category')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="sub_categories" class="form-label">Sub Categories</label>
                                <select multiple
                                    class="default-select form-control wide mb-3 @error('sub_categories') is-invalid @enderror"
                                    id="sub_categories" name="sub_categories[]" required>
                                    <option disabled selected>Select Sub categories</option>
                                    @foreach (['Category A', 'Category B', 'Category C', 'Category D', 'Category E', 'Category F', 'Category G', 'Category H'] as $category)
                                        <option value="{{ $category }}"
                                            @if (in_array($category, $businessType->sub_categories ?? [])) selected @endif>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('sub_categories')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="class_rate" class="form-label"> Rate - GHS </label>
                                <input type="text" class="form-control @error('rate') is-invalid @enderror"
                                    id="rate" name="rate" placeholder="Enter Rate"
                                    value="{{ $businessType->rate }}">

                                @error('rate')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>


                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
@endsection
