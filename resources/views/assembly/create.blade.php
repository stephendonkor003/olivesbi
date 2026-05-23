@extends('layout.base')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('assets/css/lightgallery.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fileinput.min.css') }}">
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
                            <i class="ri-settings-5-line me-2"></i> Assembly Setup - Configurations
                        </h4>

                        <p class="mb-0 text-muted fs-14">
                            Create your assembly here.
                        </p>
                    </div>
                    @can('assemblies.create')
                        <div>
                            <a href="{{ route('assembly.index') }}" class="btn btn-primary btn-sm ms-2">
                                <i class="ri-arrow-go-back-line"></i> Back
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                @if ($errors->any())
                    <div class="alert alert-danger mt-2">
                        <strong>Please fix the following errors:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('assembly.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" id="supervisor" name="supervisor">
                            <input type="hidden" name="geo_coordinate" id="geo_coordinate">
                            <input type="hidden" name="assembly_url" url="{{ route('assembly.fetch') }}">
                            <input type="hidden" name="assembly_id">
                            <input type="hidden" name="fetch_assembly_url"
                                url="{{ route('assembly.getSelectedAssembly') }}">

                            <div class="mb-4 col-md-6">
                                <label for="role_access" class="form-label">Regional Name</label>
                                <select class="form-control @error('regional') is-invalid @enderror" id="regional"
                                    name="regional">
                                    <option value="">Select Region Name</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->regional_code }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>

                                @error('regional')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4 col-md-6">
                                <label class="form-label">Assembly Name</label>
                                <select class="form-control" id="name" name="name">
                                    <option disabled selected>Select Assembly Name</option>

                                </select>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4 col-md-6">
                                <label class="form-label">Region Code</label>
                                <input type="text" class="form-control @error('regional_code') is-invalid @enderror"
                                    placeholder="Enter Region Code" name="regional_code" readonly>

                                @error('regional_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4 col-md-6">
                                <label class="form-label">Assembly Code</label>
                                <input type="text" class="form-control @error('assembly_code') is-invalid @enderror"
                                    placeholder="Enter Assembly Code" name="assembly_code">

                                @error('assembly_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4 col-md-6">
                                <label class="form-label">Assembly Admin</label>
                                <div class="autocomplete">
                                    <input type="text" class="form-control @error('supervisor_id') is-invalid @enderror"
                                        placeholder="Enter Assembly Admin" id="supervisor_id" name="supervisor_id" required>
                                </div>

                                @error('supervisor_id')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assembly_category" class="form-label">Assembly Category</label>
                                <select class="form-control @error('assembly_category') is-invalid @enderror"
                                    id="assembly_category" name="assembly_category" required>
                                    <option disabled selected>Select Assembly Category</option>
                                    <option value="Municipal">Municipal</option>
                                    <option value="Metropolitan">Metropolitan</option>
                                    <option value="District">District</option>
                                </select>

                                @error('assembly_category')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4 col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                    placeholder="Enter Assembly Address" id="address" name="address" required>

                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4 col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="Enter Assembly Phone" id="phone" name="phone" required>

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="logo" class="form-label">Upload Assembly Logo</label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                    id="logo" name="logo" accept=".jpg,.jpeg,.png,.pdf">

                                @error('logo')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4 col-md-6">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                    placeholder="Enter Assembly Bank Name" id="bank_name" name="bank_name">

                                @error('bank_name')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4 col-md-6">
                                <label class="form-label">Bank Number</label>
                                <input type="text" class="form-control @error('bank_number') is-invalid @enderror"
                                    placeholder="Enter Assembly Bank Number" id="bank_number" name="bank_number">

                                @error('bank_number')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4 col-md-6">
                                <label class="form-label">Bank Branch</label>
                                <input type="text" class="form-control @error('bank_branch') is-invalid @enderror"
                                    placeholder="Enter Assembly Bank Branch" id="bank_branch" name="bank_branch">

                                @error('bank_branch')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6" id="assembly-logo-container">
                            </div>

                            <div class="col-12">
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
    <script src="{{ asset('assets/js/fileinput.min.js') }}"></script>
    <script src="{{ asset('assets/js/lightgallery-all.min.js') }}"></script>
    <script src="{{ asset('assets/js/general.js?v=' . time()) }}"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function autocomplete(inp, arr) {
                var currentFocus;
                inp.addEventListener("input", function(e) {
                    var a, b, i, val = this.value;
                    closeAllLists();
                    if (!val) {
                        return false;
                    }
                    currentFocus = -1;
                    a = document.createElement("DIV");
                    a.setAttribute("id", this.id + "autocomplete-list");
                    a.setAttribute("class", "autocomplete-items");
                    this.parentNode.appendChild(a);

                    for (i = 0; i < arr.length; i++) {
                        if (arr[i].name.substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                            b = document.createElement("DIV");
                            b.innerHTML = "<strong>" + arr[i].name.substr(0, val.length) + "</strong>";
                            b.innerHTML += arr[i].name.substr(val.length);
                            b.innerHTML += "<input type='hidden' value='" + arr[i].name + "'>";
                            b.innerHTML += "<input type='hidden' value='" + arr[i].id + "'>";
                            b.addEventListener("click", function(e) {
                                inp.value = this.getElementsByTagName("input")[0].value;
                                var supervisorId = this.getElementsByTagName("input")[1].value;
                                document.getElementById('supervisor').value = supervisorId;
                                closeAllLists();
                            });
                            a.appendChild(b);
                        }
                    }
                });

                inp.addEventListener("keydown", function(e) {
                    var x = document.getElementById(this.id + "autocomplete-list");
                    if (x) x = x.getElementsByTagName("div");
                    if (e.keyCode == 40) {
                        currentFocus++;
                        addActive(x);
                    } else if (e.keyCode == 38) {
                        currentFocus--;
                        addActive(x);
                    } else if (e.keyCode == 13) {
                        e.preventDefault();
                        if (currentFocus > -1) {
                            if (x) x[currentFocus].click();
                        }
                    }
                });

                function addActive(x) {
                    if (!x) return false;
                    removeActive(x);
                    if (currentFocus >= x.length) currentFocus = 0;
                    if (currentFocus < 0) currentFocus = (x.length - 1);
                    x[currentFocus].classList.add("autocomplete-active");
                }

                function removeActive(x) {
                    for (var i = 0; i < x.length; i++) {
                        x[i].classList.remove("autocomplete-active");
                    }
                }

                function closeAllLists(elmnt) {
                    var x = document.getElementsByClassName("autocomplete-items");
                    for (var i = 0; i < x.length; i++) {
                        if (elmnt != x[i] && elmnt != inp) {
                            x[i].parentNode.removeChild(x[i]);
                        }
                    }
                }

                document.addEventListener("click", function(e) {
                    closeAllLists(e.target);
                });
            }

            var supervisors = @json($supervisors);

            autocomplete(document.getElementById("supervisor_id"), supervisors);
        });
    </script>
@endsection
