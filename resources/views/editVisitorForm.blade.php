@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        @lang('main.successNewVisitorMessage')
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<form method="post" action="{{ route('newVisitor.store') }}">
    <div class="form-row">
        <div class="form-group col">
            <select class="form-control" id="editvisitorCategory" name="editvisitorCategory" autofocus>
                <option @if(old('editvisitorCategory') == "Besucher") selected @endif value="Besucher">@lang('main.visitor')</option>
                <option @if(old('editvisitorCategory') == "Handwerker") selected @endif value="Handwerker">@lang('main.craftsmen')</option>
            </select>
            @error('editvisitorCategory')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group col">
            <select class="form-control" id="editvisitorDetail" name="editvisitorDetail" autofocus>
                <option @if(old('editvisitorDetail') == "-") selected @endif value="-">-</option>
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
            <select id="editsalutation" name="salutation" class="form-control">
                <option value="Herr">@lang('main.Mr.')</option>
                <option value="Frau">@lang('main.Mrs.')</option>
            </select>
        </div>
        <div class="form-group col">
            <input id="edittitle" type="text" class="form-control @error('edittitle') is-invalid @enderror" name="edittitle" value="{{ old('edittitle') }}" placeholder="@lang('main.title')" autofocus>
            @error('edittitle')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input id="editforename" type="text" class="form-control @error('editforename') is-invalid @enderror" name="editforename" value="{{ old('editforename') }}" placeholder="@lang('main.forename')" autofocus>
            @error('editforename')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form-group col">
            <input id="editsurname" type="text" class="form-control @error('editsurname') is-invalid @enderror" name="editsurname" value="{{ old('editsurname') }}" placeholder="@lang('main.surname')" autofocus>
            @error('editsurname')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col">
            <select class="form-control @error('language') is-invalid @enderror" id="editlanguage" name="language" autofocus>
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
            <input id="editcompany" type="text" class="form-control @error('editcompany') is-invalid @enderror" name="editcompany" value="{{ old('editcompany') }}" placeholder="@lang('main.company')" autofocus>
            @error('editcompany')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input id="editcompanyStreet" type="text" class="form-control @error('editcompanyStreet') is-invalid @enderror" name="editcompanyStreet" value="{{ old('editcompanyStreet') }}" placeholder="@lang('main.companyStreet')" autofocus>
            @error('editcompanyStreet')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input id="editcompanyCountry" type="text" class="form-control @error('editcompanyCountry') is-invalid @enderror" name="editcompanyCountry" value="{{ old('editcompanyCountry') }}" placeholder="@lang('main.companyCountry')" autofocus>
            @error('editcompanyCountry')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form-group col">
            <input id="editcompanyZipCode" type="text" class="form-control @error('editcompanyZipCode') is-invalid @enderror" name="editcompanyZipCode" value="{{ old('editcompanyZipCode') }}" placeholder="@lang('main.companyZipCode')" autofocus>
            @error('editcompanyZipCode')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form-group col">
            <input id="editcompanyCity" type="text" class="form-control @error('editcompanyCity') is-invalid @enderror" name="editcompanyCity" value="{{ old('editcompanyCity') }}" placeholder="@lang('main.companyCity')" autofocus>
            @error('editcompanyCity')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input id="editemail" type="e-mail" class="form-control @error('editemail') is-invalid @enderror" name="editemail" value="{{ old('editemail') }}" placeholder="@lang('main.email')" autofocus>
            @error('editemail')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input id="editlandlineNumber" type="tel" class="form-control @error('editlandlineNumber') is-invalid @enderror" name="editlandlineNumber" value="{{ old('editlandlineNumber') }}" placeholder="@lang('main.phoneNumber')*" autofocus>
            @error('editlandlineNumber')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
    <!-- <div class="form-row">
        <div class="form-group col">
            <input id="editmobileNumber" type="tel" class="form-control @error('vmobileNumber') is-invalid @enderror" name="editmobileNumber" value="{{ old('editmobileNumber') }}" placeholder="@lang('main.mobileNumber')" autofocus>
            @error('editmobileNumber')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div> -->

</form>
<p>@lang('main.requiredFieldInfo')</p>
