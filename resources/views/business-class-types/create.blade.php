@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header flex-wrap d-flex justify-content-between">
                        <div class="card-header">
                            <div class="card-title">Property Management / Assembly Property Class Types</div>
                        </div>

                        <div>
                            <a href="{{ route('business-class-types.index') }}" class="btn btn-primary btn-sm ms-2">Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST"
                            action="{{ route('business-class-types.store') }}">
                            @csrf

                            <div class="col-sm-6 mb-3">
                                <label for="class_name" class="form-label"> Class Type</label>
                                <select class="form-control @error('name') is-invalid @enderror" id="class_name"
                                    name="name">
                                    <option disabled selected>Select Class</option>
                                    <option value="1st Class">1st Class </option>
                                    <option value="2nd Class">2nd Class </option>
                                    <option value="3rd Class">3rd Class </option>
                                    <option value="All Class">All Class </option>
                                </select>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="class_category" class="form-label"> Category</label>
                                <select class="form-control @error('category') is-invalid @enderror" id="class_category"
                                    name="category">
                                    <option disabled selected>Select Category</option>
                                    <option value="Residential">Residential </option>
                                    <option value="Commercial">Commercial </option>
                                    <option value="Mixed Use">Mixed Used </option>
                                </select>

                                @error('category')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="identifier" class="form-label">Identifier</label>
                                <select class="form-control @error('identifier') is-invalid @enderror" id="identifier"
                                    name="identifier" required>
                                    <option disabled selected>Select Identifier</option>
                                    <option value="Un-completed Building">Un-completed Building
                                    </option>
                                    <option value="Completed Building">Completed Building </option>
                                    <option value="Educational Properties">Educational Properties
                                    </option>
                                    <option value="Un-Assessed Propeerty">Un-Assessed Propeerty
                                    </option>
                                </select>

                                @error('identifier')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="class_rate" class="form-label"> Rate- GHS </label>
                                <input type="text" class="form-control @error('rate') is-invalid @enderror"
                                    id="rate" name="rate" placeholder="Enter Class Rate">

                                @error('rate')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="extra_class_identifier" class="form-label"> Extra class
                                    Identifier </label>
                                <input type="text"
                                    class="form-control @error('extra_class_identifier') is-invalid @enderror"
                                    id="extra_class_identifier" name="extra_class_identifier"
                                    placeholder="Extra class Identifier">

                                @error('extra_class_identifier')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Save</button>
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
