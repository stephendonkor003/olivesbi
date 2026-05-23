@extends('layout.base')

@section('page-styles')
    <!-- Include any additional styles needed for the payment form -->
@endsection

@section('page-content')
    <div class="row">
        <div class="container-fluid mh-auto">
            <div class="card">
                <div class="card-header flex-wrap d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">Import Payment Invoices</h4>
                    </div>

                    <div>
                        <a href="{{ route('payments.index') }}" class="btn btn-primary btn-sm ms-2">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('payments.importData') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="amount_paid">Upload Invoice File</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror"
                                    name="file">

                                @error('file')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary">Upload Invoice</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- Include any additional scripts needed for the payment form -->
@endsection
