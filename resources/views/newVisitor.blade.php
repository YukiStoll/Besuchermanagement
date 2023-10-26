@extends('layouts.layout')
@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            @lang('main.successNewVisitorMessage')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>@lang('main.addNewVisitor')</h3></div>
                    <div class="card-body">
                        <form id="makeVisitorForm" method="post" action="{{ route('newVisitor.store') }}">
                            <div class="form-row">
                                <div class="form-group col">
                                    <select class="form-control @error('visitorCategory') is-invalid @enderror" id="visitorCategory" name="visitorCategory" autofocus>
                                        <option disabled @if(old('visitorCategory') != "Besucher" || old('visitorCategory') != "Handwerker" || old('visitorCategory') != "Lieferant") selected @endif >@lang('main.visitorCategory')*</option>
                                        <option @if(old('visitorCategory') == "Besucher") selected @endif value="Besucher">@lang('main.visitor')</option>
                                        <option @if(old('visitorCategory') == "Handwerker") selected @endif value="Handwerker">@lang('main.craftsmen')</option>
                                    </select>
                                    @error('visitorCategory')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group col">
                                    <select class="form-control" id="visitorDetail" name="visitorDetail" autofocus>
                                        <option @if(old('visitorDetail') == "-" || old('visitorDetail') != "con1" && old('visitorDetail') != "con2") selected @endif value="-">-</option>
                                        <option @if(old('visitorDetail') == "con1") selected @endif value="con1">Con1</option>
                                        <option @if(old('visitorDetail') == "con2") selected @endif value="con2">Con2</option>
                                    </select>
                                    @error('visitorDetail')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                            </div>
                            <div class="form-row">
                                <div class="form-group col">
                                    <select name="salutation" class="form-control">
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

                            <input type="hidden" name="creator" value="{{ Auth::user()->id }}">

                            <input id="stillCreate" type="hidden" name="stillCreate">

                            @csrf
                            <div class="form-row">
                                <div class="form-group col">
                                    <button type="submit" class="btn btn-outline-primary btn-full saveButton">@lang('main.create')</button>
                                </div>
                            </div>
                        </form>
                        <p>@lang('main.requiredFieldInfo')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="stillCreateModal" tabindex="-1" role="dialog" aria-labelledby="EditlLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">@lang('main.stillCreateVisitor')</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('main.stillCreateVisitorText')</p>
                </div>
                <div class="modal-footer">
                    <div class="form-group col">
                        <button id="stillCreateVisitorBtn" onclick="stillCreateVisitor()" type="button" class="btn btn-primary col-6 saveButton">@lang('main.create')</button>
                    </div>
                    <div class="form-group col">
                        <button id="cancel" type="button" onclick="toggleStillCreateModal()" class="btn btn-danger col-6">@lang('main.cancel')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @error('stillCreate')
    <script>
        $('#stillCreateModal').modal('toggle');

        function toggleStillCreateModal() {
            $('#stillCreateModal').modal('toggle');
        }

        function stillCreateVisitor() {
            $('#stillCreate').val(1);
            $('#makeVisitorForm').submit();
        }

    </script>
    @enderror
@endsection

