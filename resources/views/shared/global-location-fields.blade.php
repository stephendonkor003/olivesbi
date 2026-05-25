@php
    $record = $record ?? null;
@endphp

<div class="col-sm-12">
    <div class="border rounded-3 p-3 bg-light-subtle">
        <div class="fw-bold text-primary mb-1">Global Location</div>
        <div class="text-muted small mb-3">Use these fields for records outside a single municipality, region, or country.</div>
        <div class="row g-3">
            <div class="col-sm-6">
                <label for="address_line" class="form-label">Full Address</label>
                <input type="text" class="form-control @error('address_line') is-invalid @enderror"
                    id="address_line" name="address_line"
                    value="{{ old('address_line', $record->address_line ?? '') }}"
                    placeholder="Street, landmark, locality, or full postal address">
                @error('address_line')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-3">
                <label for="continent" class="form-label">Continent</label>
                <input type="text" class="form-control @error('continent') is-invalid @enderror"
                    id="continent" name="continent"
                    value="{{ old('continent', $record->continent ?? '') }}"
                    placeholder="Africa">
                @error('continent')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-3">
                <label for="country" class="form-label">Country</label>
                <input type="text" class="form-control @error('country') is-invalid @enderror"
                    id="country" name="country"
                    value="{{ old('country', $record->country ?? '') }}"
                    placeholder="Ghana">
                @error('country')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-3">
                <label for="country_code" class="form-label">Country Code</label>
                <input type="text" class="form-control @error('country_code') is-invalid @enderror"
                    id="country_code" name="country_code"
                    value="{{ old('country_code', $record->country_code ?? '') }}"
                    placeholder="GH">
                @error('country_code')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-3">
                <label for="administrative_area" class="form-label">State / Region</label>
                <input type="text" class="form-control @error('administrative_area') is-invalid @enderror"
                    id="administrative_area" name="administrative_area"
                    value="{{ old('administrative_area', $record->administrative_area ?? '') }}"
                    placeholder="Greater Accra">
                @error('administrative_area')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-3">
                <label for="city" class="form-label">City / Town</label>
                <input type="text" class="form-control @error('city') is-invalid @enderror"
                    id="city" name="city"
                    value="{{ old('city', $record->city ?? '') }}"
                    placeholder="Accra">
                @error('city')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-3">
                <label for="district" class="form-label">District / County</label>
                <input type="text" class="form-control @error('district') is-invalid @enderror"
                    id="district" name="district"
                    value="{{ old('district', $record->district ?? '') }}"
                    placeholder="Municipality or county">
                @error('district')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-3">
                <label for="postal_code" class="form-label">Postal Code</label>
                <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                    id="postal_code" name="postal_code"
                    value="{{ old('postal_code', $record->postal_code ?? '') }}"
                    placeholder="Postal or ZIP code">
                @error('postal_code')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>
