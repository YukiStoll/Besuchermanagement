@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        @lang('main.successNewVisitorMessage')
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
<form id="makeVisitorForm" method="post" action="{{ route('newVisitor.store') }}">
    <div class="form-row">
        <div class="form-group col">
            <select class="form-control" id="visitorCategory" name="visitorCategory" autofocus>
                <option disabled @if(old('contactPossibility') != "Besucher" || old('contactPossibility') != "Handwerker" || old('contactPossibility') != "Lieferant") selected @endif >@lang('main.visitorCategory')*</option>
                <option @if(old('contactPossibility') == "Besucher") selected @endif value="Besucher">@lang('main.visitor')</option>
                <option @if(old('contactPossibility') == "Handwerker") selected @endif value="Handwerker">@lang('main.craftsmen')</option>
            </select>
            @error('visitorCategory')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group col">
            <select class="form-control" id="editvisitorDetail" name="editvisitorDetail" autofocus>
                <option @if(old('editvisitorDetail') == "-" || old('editvisitorDetail') != "con1" && old('editvisitorDetail') != "con2") selected @endif value="-">-</option>
                <option @if(old('editvisitorDetail') == "con1") selected @endif value="con1">Con1</option>
                <option @if(old('editvisitorDetail') == "con2") selected @endif value="con2">Con2</option>
            </select>
            @error('editvisitorDetail')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

    </div>
    <div class="form-row">
        <div class="form-group col">
            <select id="salutation" name="salutation" class="form-control">
                <option value="Herr">@lang('main.Mr.')</option>
                <option value="Frau">@lang('main.Mrs.')</option>
            </select>
        </div>
        <div class="form-group col">
            <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" placeholder="@lang('main.title')" autofocus>
            @error('title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input id="forename" type="text" class="form-control @error('forename') is-invalid @enderror" name="forename" value="{{ old('forename') }}" placeholder="@lang('main.forename')*" autofocus>
            @error('forename')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form-group col">
            <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror" name="surname" value="{{ old('surname') }}" placeholder="@lang('main.surname')*" autofocus>
            @error('surname')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col">
            <select class="form-control @error('language') is-invalid @enderror" id="language" name="language" autofocus>
                <option disabled @if(old('language') != "german" || old('language') != "english") selected @endif >@lang('main.prefLanguageForEmailDelivery')*</option>
                <option @if(old('language') == "german") selected @endif value="german">@lang('main.german')</option>
                <option @if(old('language') == "english") selected @endif value="english">@lang('main.english')</option>
            </select>
            @error('language')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col">
            <input id="company" type="text" class="form-control @error('company') is-invalid @enderror" name="company" value="{{ old('company') }}" placeholder="@lang('main.company')*" autofocus>
            @error('company')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input id="companyStreet" type="text" class="form-control @error('companyStreet') is-invalid @enderror" name="companyStreet" value="{{ old('companyStreet') }}" placeholder="@lang('main.companyStreet')*" autofocus>
            @error('companyStreet')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input id="companyCountry" type="text" class="form-control @error('companyCountry') is-invalid @enderror" name="companyCountry" value="{{ old('companyCountry') }}" placeholder="@lang('main.companyCountry')*" autofocus>
            @error('companyCountry')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form-group col">
            <input id="companyZipCode" type="text" class="form-control @error('companyZipCode') is-invalid @enderror" name="companyZipCode" value="{{ old('companyZipCode') }}" placeholder="@lang('main.companyZipCode')*" autofocus>
            @error('companyZipCode')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form-group col">
            <input id="companyCity" type="text" class="form-control @error('companyCity') is-invalid @enderror" name="companyCity" value="{{ old('companyCity') }}" placeholder="@lang('main.companyCity')*" autofocus>
            @error('companyCity')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input id="email" type="e-mail" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="@lang('main.email')*" autofocus>
            @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input id="landlineNumber" type="tel" class="form-control @error('landlineNumber') is-invalid @enderror" name="landlineNumber" value="{{ old('landlineNumber') }}" placeholder="@lang('main.phoneNumber')*" autofocus>
            @error('landlineNumber')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>

   <!-- <div class="form-row">
        <div class="form-group col">
            <input id="mobileNumber" type="tel" class="form-control @error('mobileNumber') is-invalid @enderror" name="mobileNumber" value="{{ old('mobileNumber') }}" placeholder="@lang('main.mobileNumber')**" autofocus>
            @error('mobileNumber')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div> -->

    <input id="creator" type="hidden" name="creator" value="{{ Auth::user()->id }}">
    <input id="stillCreate" type="hidden" name="stillCreate">
</form>
<p>@lang('main.requiredFieldInfo')</p>

