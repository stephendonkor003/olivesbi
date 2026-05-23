@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header flex-wrap d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">View Property Class Type</h4>
                        </div>

                        <div>
                            <a href="{{ route('business-class-types.index') }}" class="btn btn-primary btn-sm ms-2">Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST"
                            action="{{ route('business-class-types.update', $businessClassType) }}">
                            @csrf

                            <div class="col-sm-6 mb-3">
                                <label for="class_name" class="form-label"> Class Type</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Enter Class Name"
                                    value="{{ $businessClassType->name }}" readonly>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="class_category" class="form-label"> Category</label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror"
                                    id="category" name="category" placeholder="Enter Class Category"
                                    value="{{ $businessClassType->category }}" readonly>

                                @error('category')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="identifier" class="form-label">Identifier</label>
                                <input type="text" class="form-control @error('identifier') is-invalid @enderror"
                                    id="identifier" name="identifier" placeholder="Enter Class Identifier"
                                    value="{{ $businessClassType->identifier }}" readonly>

                                @error('identifier')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="class_rate" class="form-label"> Rate- GHS </label>
                                <input type="text" class="form-control @error('rate') is-invalid @enderror"
                                    id="rate" name="rate" placeholder="Enter Class Rate"
                                    value="{{ $businessClassType->rate }}" readonly>

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
                                    placeholder="Extra class Identifier"
                                    value="{{ $businessClassType->extra_class_identifier }}" readonly>

                                @error('extra_class_identifier')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
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
