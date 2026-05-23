@extends('layout.base')

@section('page-styles')
@endsection

@section('page-content')
    @php use Illuminate\Support\Str; @endphp
    <div class="container-fluid">
        <div class="row">
            <div class="card">

                <!-- HEADER SECTION -->
                <div class="card-body border-bottom pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h4 class="fw-bold text-primary mb-1">
                                <i class="ri-settings-5-line me-2"></i> Revenue Stream Setup - Configurations
                            </h4>

                            <p class="mb-0 text-muted fs-14">
                                Manage and create your BOP revenue stream here.
                            </p>
                        </div>
                        @can('customer-types.create')
                            <a href="{{ route('revenue-streams.indexBOP') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left me-1"></i> Back
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form class="row g-3 needs-validation" method="POST" action="{{ route('revenue-streams.storeBOP') }}">
                        @csrf

                        <div class="col-sm-6 mb-3">
                            <div class="form-group">
                                <label for="assembly" class="form-label">Assembly</label>
                                @if(!empty(Auth::user()->assembly_code))
                                    <select class="form-control" id="assembly" disabled>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}" selected>
                                                {{ $assembly->name }} Assembly
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="assembly_code" value="{{ Auth::user()->assembly_code }}">
                                    <small class="text-muted d-block mt-1">Auto-assigned to your assembly</small>
                                @else
                                    <select class="form-control @error('assembly_code') is-invalid @enderror" id="assembly"
                                            name="assembly_code">
                                        <option disabled selected>Select Assembly</option>
                                        @foreach ($assemblies as $assembly)
                                            <option value="{{ $assembly->assembly_code }}">{{ $assembly->name }}
                                                Assembly
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('assembly_code')
                                    <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" name="code"
                                placeholder="Enter Code" value="{{old('code')}}">

                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="main_item_id" class="form-label">Main Item</label>
                            <select class="form-control @error('main_item_id') is-invalid @enderror" id="main_item_id"
                                name="main_item_id" data-assembly-dependent="true">
                                <option disabled selected>Select Main Item</option>
                                @foreach ($mainItems as $mainItem)
                                    <option value="{{ $mainItem->id }}" data-assembly-code="{{ $mainItem->assembly_code }}" @selected(old('main_item_id') == $mainItem->id)>
                                        {{ $mainItem->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('main_item_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="sub_item_id" class="form-label">Sub Item</label>
                            <select class="form-control @error('sub_item_id') is-invalid @enderror" id="sub_item_id"
                                name="sub_item_id" data-assembly-dependent="true">
                                <option disabled selected>Select Sub Item</option>
                                @foreach ($subItems as $subItem)
                                    <option value="{{ $subItem->id }}" data-assembly-code="{{ $subItem->assembly_code }}" @selected(old('sub_item_id') == $subItem->id)>
                                        {{ $subItem->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('sub_item_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="assembly" class="form-label">Item Description</label>
                            <select class="form-control @error('description_id') is-invalid @enderror" id="description_id"
                                name="description_id" data-assembly-dependent="true">
                                <option disabled selected>Select Item Description</option>
                                @foreach ($itemDescriptions as $itemDescription)
                                    <option value="{{ $itemDescription->id }}" data-assembly-code="{{ $itemDescription->assembly_code }}" @selected(old('description_id') == $itemDescription->id)>
                                        {{ $itemDescription->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('description_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="assembly" class="form-label">Category</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" id="category_id"
                                name="category_id" data-assembly-dependent="true">
                                <option disabled selected>Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" data-assembly-code="{{ $category->assembly_code }}" @selected(old('category_id') == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('category_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="flat_rate" class="form-label">Amount</label>
                            <input type="text" class="form-control @error('flat_rate') is-invalid @enderror"
                                name="flat_rate" placeholder="Flat Rate" value="{{old('flat_rate')}}">

                            @error('flat_rate')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="duration" class="form-label">Duration</label>
                            <select class="form-control" id="duration" name="duration">
                                <option disabled selected>Select Duration</option>
                                <option value="Per Annum" @selected(old('duration') == 'Per Annum')>Per Annum</option>
                                <option value="Per Square Meter" @selected(old('duration') == 'Per Square Meter')>Per Square Meter</option>
                            </select>

                            @error('duration')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2" id="input8">
                            <label for="basic_rate" class="form-label">Basic Rate</label>
                            <input type="text" class="form-control @error('basic_rate') is-invalid @enderror"
                                name="basic_rate" placeholder="Basic Rate" value="{{old('basic_rate')}}">

                            @error('basic_rate')
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
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/revenue-stream.js?v1=' . time()) }}"></script>
@endsection
