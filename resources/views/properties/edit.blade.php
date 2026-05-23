@extends('layout.base')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('assets/css/autocomplete.css') }}">
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="card">

            <!-- HEADER SECTION -->
            <div class="card-body border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="ri-building-line me-2"></i> Property Management
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            You are Editing A Property Record from your
                            central database repository.
                        </p>

                    </div>
                    @can('properties.create')
                        <a href="{{ route('properties.index') }}" class="btn btn-sm btn-primary">
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
                            action="{{ route('properties.update', $property) }}">
                            @csrf

                            <input type="hidden" name="property_use_url" url="{{ route('rates.property-use') }}">
                            <input type="hidden" name="division_url" url="{{ route('ajax.division') }}">
                            <input type="hidden" name="block_url" url="{{ route('ajax.block') }}">
                            <input type="hidden" name="customer_name" id="customer_name"
                                value="{{ $property->customer_name }}">

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="entity_type" class="form-label">Entity Type</label>
                                    <select class="form-control @error('entity_type') is-invalid @enderror" id="entity_type"
                                        name="entity_type">
                                        <option disabled selected>Select Entity Type</option>
                                        @foreach ($businessClassTypes as $businessClassType)
                                            <option value="{{ $businessClassType->id }}"
                                                {{ old('entity_type', $property->entity_type) == $businessClassType->id ? 'selected' : '' }}>
                                                {{ $businessClassType->name }} -
                                                {{ $businessClassType->category }} - GHS
                                                {{ $businessClassType->rate }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('entity_type')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="digital_address" class="form-label">Digital Address</label>
                                    <input type="text"
                                        class="form-control @error('digital_address') is-invalid @enderror"
                                        id="digital_address" name="digital_address" placeholder="Digital address"
                                        value="{{ $property->digital_address }}">

                                    @error('digital_address')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                                        id="location" name="location" placeholder="Location"
                                        value="{{ $property->location }}">

                                    @error('location')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="street_name" class="form-label">Street Name</label>
                                    <input type="text" class="form-control" id="street_name" name="street_name"
                                        placeholder="Street name" value="{{ $property->street_name }}">
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="rated" class="form-label">Rated</label>
                                    <select class="form-control" id="rated" name="rated">
                                        <option disabled selected>Select If Rated</option>
                                        <option value="Yes"
                                            {{ old('rated', $property->rated) == 'Yes' ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="No"
                                            {{ old('rated', $property->rated) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="validated" class="form-label">Validated</label>
                                    <select class="form-control" id="validated" name="validated">
                                        <option disabled selected>Select If Validated</option>
                                        <option value="Yes"
                                            {{ old('validated', $property->validated) == 'Yes' ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="No"
                                            {{ old('validated', $property->validated) == 'No' ? 'selected' : '' }}>No
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="entity_type" class="form-label">Associate Owner</label>
                                @php
                                    $firstname = $property->customer->first_name ?? '';
                                    $lastname = $property->customer->last_name ?? '';
                                    $name = $firstname . ' ' . $lastname;
                                @endphp
                                <div class="autocomplete">
                                    <input type="text" class="form-control" id="customer" name="customer"
                                        placeholder="Enter associate owner name" value="{{ $name }}">
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="ratable_value" class="form-label">Ratable Value</label>
                                    <input type="text" class="form-control" id="ratable_value" name="ratable_value"
                                        placeholder="Ratable value" value="{{ $property->ratable_value }}">
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="text" class="form-control" id="longitude" name="longitude"
                                        placeholder="Longitude" value="{{ $property->longitude }}">
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude"
                                        placeholder="Latitude" value="{{ $property->latitude }}">
                                </div>
                            </div>

                            <!-- Add Assembly field here -->
                            <div class="col-sm-6 mb-3">
                                <label for="assembly" class="form-label">Select An Assembly Where Property is
                                    Located</label>
                                <select class="form-control @error('assembly_code') is-invalid @enderror" id="assembly"
                                    name="assembly_code">
                                    <option disabled selected>Select Assembly</option>
                                    @foreach ($districtAssemblies as $assembly)
                                        <option value="{{ $assembly->assembly_code }}"
                                            {{ old('assembly_code', $property->assembly_code) == $assembly->assembly_code ? 'selected' : '' }}>
                                            {{ $assembly->name }}
                                            Assembly
                                        </option>
                                    @endforeach
                                </select>

                                @error('assembly_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="division_id" class="form-label">Property Division</label>
                                <select class="form-control @error('division_id') is-invalid @enderror" id="division_id"
                                    name="division_id" required>
                                    <option disabled selected>Select Division</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            {{ old('division_id', $property->division_id) == $division->id ? 'selected' : '' }}>
                                            {{ $division->division_name }}</option>
                                    @endforeach
                                </select>

                                @error('division_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="block_id" class="form-label">Property Block</label>
                                <select class="form-control @error('block_id') is-invalid @enderror" id="block_id"
                                    name="block_id" required>
                                    <option disabled selected>Select Block</option>
                                    @foreach ($blocks as $block)
                                        <option value="{{ $block->id }}"
                                            {{ old('block_id', $property->block_id) == $block->id ? 'selected' : '' }}>
                                            {{ $block->block_name }}</option>
                                    @endforeach
                                </select>

                                @error('block_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="zone_id" class="form-label">Property Zone</label>
                                <select class="form-control @error('zone_id') is-invalid @enderror" id="zone_id"
                                    name="zone_id" required>
                                    <option disabled selected>Select Zone</option>
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}"
                                            {{ old('zone_id', $property->zone_id) == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->name }}</option>
                                    @endforeach
                                </select>

                                @error('zone_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-sm-6 mb-3">
                                <label for="property_use_id" class="form-label">Property use</label>
                                <select class="form-control @error('property_use_id') is-invalid @enderror"
                                    id="property_use_id" name="property_use_id" required>
                                    <option disabled selected>Select Property Use</option>
                                    @foreach ($propertyUsers as $propertyUser)
                                        <option value="{{ $propertyUser->id }}"
                                            {{ old('property_use_id', $property->property_use_id) == $propertyUser->id ? 'selected' : '' }}>
                                            {{ $propertyUser->name }}</option>
                                    @endforeach
                                </select>

                                @error('property_use_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/js/general.js?v1=1234') }}"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function autocomplete(inp, arr) {
                /*the autocomplete function takes two arguments,
                the text field element and an array of possible autocompleted values:*/
                var currentFocus;
                /*execute a function when someone writes in the text field:*/
                inp.addEventListener("input", function(e) {
                    var a, b, i, val = this.value;
                    /*close any already open lists of autocompleted values*/
                    closeAllLists();
                    if (!val) {
                        return false;
                    }
                    currentFocus = -1;
                    /*create a DIV element that will contain the items (values):*/
                    a = document.createElement("DIV");
                    a.setAttribute("id", this.id + "autocomplete-list");
                    a.setAttribute("class", "autocomplete-items");
                    /*append the DIV element as a child of the autocomplete container:*/
                    this.parentNode.appendChild(a);
                    /*for each item in the array...*/
                    for (i = 0; i < arr.length; i++) {
                        /*check if the item starts with the same letters as the text field value:*/
                        if (arr[i].first_name.substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                            /*create a DIV element for each matching element:*/
                            b = document.createElement("DIV");
                            /*make the matching letters bold:*/
                            b.innerHTML = "<strong>" + arr[i].first_name.substr(0, val.length) +
                                "</strong>";
                            b.innerHTML += arr[i].first_name.substr(val.length);
                            /*insert a input field that will hold the current array item's value:*/
                            b.innerHTML += "<input type='hidden' value='" + arr[i].first_name + "'>";
                            b.innerHTML += "<input type='hidden' value='" + arr[i].id + "'>";
                            /*execute a function when someone clicks on the item value (DIV element):*/
                            b.addEventListener("click", function(e) {
                                /*insert the value for the autocomplete text field:*/
                                inp.value = this.getElementsByTagName("input")[0].value;
                                var customerId = this.getElementsByTagName("input")[1].value;

                                document.getElementById('customer_name').value = customerId;
                                /*close the list of autocompleted values,
                                (or any other open lists of autocompleted values:*/
                                closeAllLists();
                            });
                            a.appendChild(b);
                        }
                    }
                });

                /*execute a function presses a key on the keyboard:*/
                inp.addEventListener("keydown", function(e) {
                    var x = document.getElementById(this.id + "autocomplete-list");
                    if (x) x = x.getElementsByTagName("div");
                    if (e.keyCode == 40) {
                        /*If the arrow DOWN key is pressed,
                        increase the currentFocus variable:*/
                        currentFocus++;
                        /*and and make the current item more visible:*/
                        addActive(x);
                    } else if (e.keyCode == 38) { //up
                        /*If the arrow UP key is pressed,
                        decrease the currentFocus variable:*/
                        currentFocus--;
                        /*and and make the current item more visible:*/
                        addActive(x);
                    } else if (e.keyCode == 13) {
                        /*If the ENTER key is pressed, prevent the form from being submitted,*/
                        e.preventDefault();
                        if (currentFocus > -1) {
                            /*and simulate a click on the "active" item:*/
                            if (x) x[currentFocus].click();
                        }
                    }
                });

                function addActive(x) {
                    /*a function to classify an item as "active":*/
                    if (!x) return false;
                    /*start by removing the "active" class on all items:*/
                    removeActive(x);
                    if (currentFocus >= x.length) currentFocus = 0;
                    if (currentFocus < 0) currentFocus = (x.length - 1);
                    /*add class "autocomplete-active":*/
                    x[currentFocus].classList.add("autocomplete-active");
                }

                function removeActive(x) {
                    /*a function to remove the "active" class from all autocomplete items:*/
                    for (var i = 0; i < x.length; i++) {
                        x[i].classList.remove("autocomplete-active");
                    }
                }

                function closeAllLists(elmnt) {
                    /*close all autocomplete lists in the document,
                    except the one passed as an argument:*/
                    var x = document.getElementsByClassName("autocomplete-items");
                    for (var i = 0; i < x.length; i++) {
                        if (elmnt != x[i] && elmnt != inp) {
                            x[i].parentNode.removeChild(x[i]);
                        }
                    }
                }

                /*execute a function when someone clicks in the document:*/
                document.addEventListener("click", function(e) {
                    closeAllLists(e.target);
                });
            }

            /*An array containing all the customer ids in the customer table:*/
            var customers = @json($customers);

            autocomplete(document.getElementById("customer"), customers);

        });
    </script>
@endsection
