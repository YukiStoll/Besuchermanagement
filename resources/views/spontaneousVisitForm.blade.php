<form method="POST" action="{{ route('makeSpontaneousVisit') }}" id="spontaneousVisitForm">
    <div class="form-row">
        <div class="form-group col-3 text-right">
            <label class="col-form-label-lg">@lang('main.company'):</label>
        </div>
        <div class="form-group col">
            <input class="form-control @error('company') is-invalid @enderror" type="text" name="company" id="company">
        </div>
        @error('company')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>
    <div class="form-row">
        <div class="form-group col-3 text-right">
            <label class="col-form-label-lg">@lang('main.carrier'):</label>
        </div>
        <div class="form-group col">
            <input class="form-control @error('carrier') is-invalid @enderror" type="text" name="carrier" id="carrier">
        </div>
        @error('carrier')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>
    <div class="form-row">
        <div class="form-group col-3 text-right">
            <label class="col-form-label-lg">@lang('main.vehicleRegistrationNumber'):</label>
        </div>
        <div class="form-group col">
            <input class="form-control @error('vehicleRegistrationNumber') is-invalid @enderror" type="text" name="vehicleRegistrationNumber" id="vehicleRegistrationNumber">
        </div>
        @error('vehicleRegistrationNumber')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>
    <div class="form-row">
        <div class="form-group col-3 text-right">
            <label class="col-form-label-lg">@lang('main.orderNumber'):</label>
        </div>
        <div class="form-group col">
            <input class="form-control @error('orderNumber') is-invalid @enderror" type="text" name="orderNumber" id="orderNumber">
        </div>
        @error('orderNumber')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>
    <div class="form-row">
        <div class="form-group col-3 text-right">
            <label class="col-form-label-lg">@lang('main.cargo'):</label>
        </div>
        <div class="form-group col">
            <input class="form-control @error('cargo') is-invalid @enderror" type="text" name="cargo" id="cargo">
        </div>
        @error('cargo')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>
    <div class="form-row">
        <div class="form-group col-3 text-right">
            <label class="col-form-label-lg">@lang('main.reasonForVisit'):</label>
        </div>
        <div class="form-group col">
            <input class="form-control @error('reasonForVisit') is-invalid @enderror" type="text" name="reasonForVisit" id="reasonForVisit">
        </div>
        @error('reasonForVisit')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>
    @csrf
</form>
