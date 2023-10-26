@extends('layouts.layout')
@section('content')
    <style>
        span.twitter-typeahead {
            width: 100%;
            background-color: white !important;
        }
        .tt-input {
        background-color: white !important;
        }
        .tt-menu {
        @extend .list-group
        }
        .tt-suggestion{
        @extend .list-group-item
        }
        .tt-selectable{
        @extend .list-group-item-action
        }
    </style>
    @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                @lang('main.successNewAdvancedRegistrationMessage')
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
    @endif
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>@lang('main.advancedRegistration')</h3></div>
                    <div class="card-body">
                        <div id="successdiv"></div>
                        <form method="POST" id="form" action="{{ route('newAdvancedRegistration.store') }}" enctype="multipart/form-data">

                            <div class="form-row">
                                <div class="form-group col">
                                    <label class="form-inline">@lang('main.startDate')*</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <input id="startDate" type="date" class="form-control @error('startDate') is-invalid @enderror" name="startDate" value="{{  date('Y-m-d', strtotime(old('startDate') ? old('startDate') : date('Y-m-d'))) }}" autofocus>
                                    @error('startDate')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group col">
                                    <input id="startTime" type="time" class="form-control @error('startTime') is-invalid @enderror" name="startTime" value="{{  date('H:i', strtotime(old('startTime') ? old('startTime') : date('12:00'))) }}" autofocus>
                                    @error('startTime')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label class="form-inline">@lang('main.endDate')*</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <input id="endDate" type="date" class="form-control @error('endDate') is-invalid @enderror" name="endDate" value="{{ date('Y-m-d', strtotime(old('endDate') ? old('endDate') : date('Y-m-d'))) }}" autofocus>
                                    @error('endDate')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group col">
                                    <input id="endTime" type="time" class="form-control @error('endTime') is-invalid @enderror" name="endTime" value="{{  date('H:i', strtotime(old('endTime') ? old('endTime') : date('13:00'))) }}" autofocus>
                                    @error('endTime')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <input readonly  type="text" class="form-control" name="employee" value="{{ Auth::user()->name }}">
                                </div>
                            </div>
                            <input type="hidden" name="userId" value="{{ Auth::user()->id }}">

                            <div class="form-row">
                                <div class="form-group col">
                                    <label class="form-floating-label">@lang('main.visitor')*</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-6">
                                    <input id="search" class="search-input form-control @error('visitorids') is-invalid @enderror" placeholder="@lang('main.searchVisitor')">
                                </div>
                                <div class="form-group col">
                                    <button type="button" onclick="$('#stillCreate').removeAttr('value');" data-toggle="modal" data-target="#New" class="btn btn-full btn-success">@lang('main.createNewVisitor')</button>
                                    <button type="button" onclick="addGroupMember()" class="btn btn-full btn-success">@lang('main.addGroupMember')</button>
                                    <!-- onclick="addGroupMember()" -->
                                </div>
                            </div>

                            <div class="form-row">
                                <div id="alertDiv" class="form-group col">
                                </div>
                            </div>

                            <div id="childDiv">
                            </div>

                            <input type="hidden" id="visitorId">

                            <div class="form-row">
                                <div class="form-group col">
                                    <input type="text" class="form-control @error('reasonForVisit') is-invalid @enderror" name="reasonForVisit" value="{{ old('reasonForVisit') }}" placeholder="@lang('main.reasonForVisit')" autofocus>
                                    @error('reasonForVisit')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label class="form-inline">@lang('main.roomOccupancy'):</label>
                                </div>
                                <div class="form-group col">
                                    <label class="form-inline">{{ $admin_settings['setting_value'] }}</label>
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col">
                                    <label class="form-inline">@lang('main.workPermissionDocuments')</label>
                                </div>
                            </div>

                            <div id="workPermissionDocumentList">
                            </div>

                            <div class="form-row">
                                <div class="form-group col-3">
                                    <label style="height: 38px" class="btn btn-full btn-primary text-light" for="pdf">Upload @lang('main.workPermissionDocuments')</label>
                                    <input class="invisible" accept="application/pdf" type="file" name="pdf[]" multiple id="pdf">
                                </div>
                                <div class="form-group col-5">
                                 </div>
                                <div class="form-group col-4">
                                    <button type="button" data-toggle="modal" data-target="#workPermissionDocuments" class="btn btn-full btn-success">@lang('main.overviewWorkPermissionDocuments')</button>
                                </div>
                            </div>
							
                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="entryPermission">@lang('main.entryPermission')</label>
                                    <select class="form-control @error('entryPermission') is-invalid @enderror" id="entryPermission" name="entryPermission" autofocus>
                                        <option @if(old('entryPermission') == "") selected @endif value="">@lang('main.Q_selectIfAnEntryPermissionIsRequired')</option>
                                        <option @if(old('entryPermission') == "Michael-van.Voorden@unilever.com") selected @endif value="Michael-van.Voorden@unilever.com">Michael van Voorden</option>
                                        <option @if(old('entryPermission') == "Rainer.Kienel@unilever.com") selected @endif value="Rainer.Kienel@unilever.com">Rainer Kienel</option>
                                        <option @if(old('entryPermission') == "Alexander.Lang@unilever.com") selected @endif value="Alexander.Lang@unilever.com">Alexander Lang</option>
                                        <option @if(old('entryPermission') == "Sabine.Simons@unilever.com") selected @endif value="Sabine.Simons@unilever.com">Sabine Simons</option>
                                        <option @if(old('entryPermission') == "Silke.Dieckmann@unilever.com") selected @endif value="Silke.Dieckmann@unilever.com">Silke Dieckmann</option>
                                    </select>
                                </div>
                                <div class="form-group col">
                                    <label for="workPermissionApproval">@lang('main.workPermissionApproval')</label>
                                    <select class="form-control @error('workPermissionApproval') is-invalid @enderror" id="workPermissionApproval" name="workPermissionApproval" autofocus>
                                        <option @if(old('workPermissionApproval') == "") selected @endif value="">@lang('main.Q_selectIfAnWorkPermissionApprovalIsRequired')</option>
                                        <option @if(old('workPermissionApproval') == "Jan.Barkemeyer@unilever.com") selected @endif value="Jan.Barkemeyer@unilever.com">Jan Barkemeyer</option>
                                        <option @if(old('workPermissionApproval') == "Uwe.Bolle@unilever.com") selected @endif value="Uwe.Bolle@unilever.com">Uwe Bolle</option>
                                        <option @if(old('workPermissionApproval') == "Sabrina.DeCristofaro@unilever.com") selected @endif value="Sabrina.DeCristofaro@unilever.com">Sabrina DeCristofaro</option>
                                        <option @if(old('workPermissionApproval') == "Silke.Dieckmann@unilever.com") selected @endif value="Silke.Dieckmann@unilever.com">Silke Dieckmann</option>
                                        <option @if(old('workPermissionApproval') == "Jens.Doeding@unilever.com") selected @endif value="Jens.Doeding@unilever.com">Jens Döding</option>
                                        <option @if(old('workPermissionApproval') == "Michael.Glanzner@unilever.com") selected @endif value="Michael.Glanzner@unilever.com">Michael Glanzner</option>
                                        <option @if(old('workPermissionApproval') == "Thomas.Held@unilever.com") selected @endif value="Thomas.Held@unilever.com">Thomas Held</option>
                                        <option @if(old('workPermissionApproval') == "Sebastian.Jaehrling@unilever.com") selected @endif value="Sebastian.Jaehrling@unilever.com">Sebastian Jährling</option>
                                        <option @if(old('workPermissionApproval') == "Rainer.Kienel@unilever.com") selected @endif value="Rainer.Kienel@unilever.com">Rainer Kienel</option>
                                        <option @if(old('workPermissionApproval') == "Nils.Molden2@unilever.com") selected @endif value="Nils.Molden2@unilever.com">Nils Molden</option>
                                        <option @if(old('workPermissionApproval') == "Alexander.Lang@unilever.com") selected @endif value="Alexander.Lang@unilever.com">Alexander Lang</option>
                                        <option @if(old('workPermissionApproval') == "Jan.Lerch@unilever.com") selected @endif value="Jan.Lerch@unilever.com">Jan Lerch</option>
                                        <option @if(old('workPermissionApproval') == "Simon.Muehlauer@unilever.com") selected @endif value="Simon.Muehlauer@unilever.com">Simon Mühlauer</option>
                                        <option @if(old('workPermissionApproval') == "Rainer.Mueller@unilever.com") selected @endif value="Rainer.Mueller@unilever.com">Rainer Müller</option>
                                        <option @if(old('workPermissionApproval') == "Michael.Roeder@unilever.com") selected @endif value="Michael.Roeder@unilever.com">Michael Röder</option>
                                        <option @if(old('workPermissionApproval') == "Kathrin.Sauer@unilever.com") selected @endif value="Kathrin.Sauer@unilever.com">Kathrin Sauer</option>
                                        <option @if(old('workPermissionApproval') == "Steffen.Schellig@unilever.com") selected @endif value="Steffen.Schellig@unilever.com">Steffen Schellig</option>
                                        <option @if(old('workPermissionApproval') == "Robert.Schneider@unilever.com") selected @endif value="Robert.Schneider@unilever.com">Robert Schneider</option>
                                        <option @if(old('workPermissionApproval') == "Wolfgang.Schranz@unilever.com") selected @endif value="Wolfgang.Schranz@unilever.com">Wolfgang Schranz</option>
                                        <option @if(old('workPermissionApproval') == "Isa.Serdani@unilever.com") selected @endif value="Isa.Serdani@unilever.com">Isa Serdani</option>
                                        <option @if(old('workPermissionApproval') == "Sabine.Simons@unilever.com") selected @endif value="Sabine.Simons@unilever.com">Sabine Simons</option>
                                        <option @if(old('workPermissionApproval') == "Michael-van.Voorden@unilever.com") selected @endif value="Michael-van.Voorden@unilever.com">Michael van Voorden</option>
                                        <option @if(old('workPermissionApproval') == "Vlad.George@unilever.com") selected @endif value="Vlad.George@unilever.com">George Vlad</option>
                                        <option @if(old('workPermissionApproval') == "Jens-von.Steht@unilever.com") selected @endif value="Jens-von.Steht@unilever.com">Jens von Steht</option>
                                        <option @if(old('workPermissionApproval') == "Michael.Weiser@unilever.com") selected @endif value="Michael.Weiser@unilever.com">Michael Weiser</option>
                                        <option @if(old('workPermissionApproval') == "Sascha.Woidich@unilever.com") selected @endif value="Sascha.Woidich@unilever.com">Sascha Woidich</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <input type="text" class="form-control @error('entryPermissionText') is-invalid @enderror" name="entryPermissionText" value="{{ old('entryPermissionText') }}" placeholder="@lang('main.reasonForVisitEntryPermission')" autofocus>
                                    @error('entryPermissionText')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group col">
                                    <input type="text" class="form-control @error('workPermissionApprovalText') is-invalid @enderror" name="workPermissionApprovalText" value="{{ old('workPermissionApprovalText') }}" placeholder="@lang('main.reasonForVisitWorkPermission')" autofocus>
                                    @error('workPermissionApprovalText')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="roadmap">@lang('main.roadmap')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input id="roadmap1" type="radio" class="form-check-input" name="roadmap" @if(old('roadmap') == 0) checked @endif value="0" autofocus>
                                    <label class="form-check-label" for="roadmap1">@lang('main.yes')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input id="roadmap2" type="radio" class="form-check-input" name="roadmap" @if(old('roadmap') == 1) checked @endif value="1" autofocus>
                                    <label class="form-check-label" for="roadmap2">@lang('main.no')</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="hygieneRegulations">@lang('main.hygieneRegulationsForExternalCompanies')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input id="hygieneRegulations1" type="radio" class="form-check-input" name="hygieneRegulations" @if(old('hygieneRegulations') == 1) checked @endif value="1" autofocus>
                                    <label class="form-check-label" for="hygieneRegulations1">@lang('main.yes')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input id="hygieneRegulations2" type="radio" class="form-check-input" name="hygieneRegulations" @if(old('hygieneRegulations') == 0) checked @endif value="0" autofocus>
                                    <label class="form-check-label" for="hygieneRegulations2">@lang('main.no')</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label class="text-warning">@lang('main.H_EMailSend')</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label>@lang('main.contactMediumWhenTheVisitorArrives')</label>
                                </div>
                                <div class="form-group col">
                                    <select class="form-control @error('contactPossibility') is-invalid @enderror" id="contactPossibility" name="contactPossibility" autofocus>
                                        <option @if(old('contactPossibility') == "E-Mail") selected @endif value="E-Mail">@lang('main.email')</option>
                                        <option @if(old('contactPossibility') == "Telefon") selected @endif value="Telefon">@lang('main.phone')</option>
                                    </select>
                                    @error('contactPossibility')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <button type="submit" id="submitButton" class="btn btn-outline-primary saveButton">@lang('main.create')</button>
                                </div>
                            </div>
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="workPermissionDocuments" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #e5e5e5">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('main.workPermissionDocuments')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: #e5e5e5">
                    <ul>
                        <li><a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\00 Allgemeine Arbeitserlaubnis.pdf") }}">00 Allgemeine Arbeitserlaubnis</a></li>
                        <li><a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\01 spez. Arbeitserlaubnis Feuer und Schweißen.pdf") }}">01 spez. Arbeitserlaubnis Feuer und Schweißen</a></li>
                        <li><a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\02 spez. Arbeitserlaubnis Höhe.pdf") }}">02 spez. Arbeitserlaubnis Höhe</a></li>
                        <li><a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\03 spez. Arbeitserlaubnis Behälter.pdf") }}">03 spez. Arbeitserlaubnis Behälter</a></li>
                        <li><a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\04 spez. Arbeitserlaubnis Erdarbeiten.pdf") }}">04 spez. Arbeitserlaubnis Erdarbeiten</a></li>
                        <li><a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\05 spez. Arbeitserlaubnis Ammoniak.pdf") }}">05 spez. Arbeitserlaubnis Ammoniak</a></li>
                        <li><a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\06 spez. Arbeitserlaubnis Öffnen von Systemen.pdf") }}">06 spez. Arbeitserlaubnis Öffnen von Systemen</a></li>
                        <li><a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\07 spez. Arbeitserlaubnis Kran.pdf") }}">07 spez. Arbeitserlaubnis Kran</a></li>
                        <li><a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\08 spez. Arbeitserlaubnis Spannung.pdf") }}">08 spez. Arbeitserlaubnis Spannung</a></li>
                        <li><a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\09 spez. Arbeitserlaubnis Heißwasserkessel.pdf") }}">09 spez. Arbeitserlaubnis Heißwasserkessel</a></li>
                        <li><a target="_blank" rel="noopener" href="{{ URL::to("\workPermissionDocuments\documents\\10 spez. Arbeitserlaubnis Gefriertunnel Tippbetrieb.pdf") }}">10 spez. Arbeitserlaubnis Gefriertunnel Tippbetrieb</a></li>
                    </ul>
                </div>
                <div class="modal-footer" style="background-color: #e5e5e5">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="Edit" tabindex="-1" role="dialog" aria-labelledby="EditlLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('main.editVisitor')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('editVisitorForm')
                </div>
                <div class="modal-footer">
                    <button id="editVisitorButton" type="button" data-dismiss="modal" class="btn btn-outline-primary col-12">@lang('main.edit')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="New" tabindex="-1" role="dialog" aria-labelledby="NewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('main.newVisitor')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('newVisitorForm')
                </div>
                <div class="modal-footer">
                    <button id="newVisitorButton" type="button" data-dismiss="modal" class="btn btn-outline-primary col-12">@lang('main.create')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteWorkPermissionDocumentModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h2 class="text-center w-100">@lang('main.Q_DeleteTheWorkPermissionDocuments')</h2>
                </div>
                <div class="modal-footer">
                    <div class="form-group col">
                        <button id="deleteWorkPermissionDocument" type="button" class="btn btn-outline-danger btn-full">@lang('main.delete')</button>
                    </div>
                    <div class="form-group col">
                        <button id="cancelDeleteWorkPermissionDocument" onclick="hideDocumentModal()" type="button"  class="btn btn-outline-secondary btn-full">@lang('main.cancel')</button>
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
                        <button id="stillCreateVisitorBtn" onclick="stillCreateVisitor()" type="button" class="btn btn-primary saveButton">@lang('main.create')</button>
                    </div>
                    <div class="form-group col">
                        <button id="cancel" type="button" onclick="toggleStillCreateModal()" class="btn btn-danger">@lang('main.cancel')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')
    <script>

        let startDate = $("#startDate");
        let endDate = $("#endDate");
        let startTime = $("#startTime");
        let endTime = $("#endTime");
        startDate.change(function () {
            if(startDate.val() > endDate.val())
            {
                endDate.val(startDate.val());
            }
        });
        endDate.change(function () {
            if(endDate.val() < startDate.val())
            {
                startDate.val(endDate.val());
            }
        });
        startTime.change(function () {
            if(startDate.val() === endDate.val() && startTime.val() > endTime.val())
            {
                endTime.val(startTime.val());
            }
        });
        endTime.change(function () {
            if(startDate.val() === endDate.val() && startTime.val() > endTime.val())
            {
                startTime.val(endTime.val());
            }
        });


        function deletemomDocuments(name, url)
        {
            $("#deleteWorkPermissionDocumentModal").modal('show');
            document.getElementById("deleteWorkPermissionDocument").onclick = function(e)
            {
                var file = url.replace("{{ URL::to('/') }}/", "");
                file = file.replace("/", "\\");
                file = file.replace("/", "\\");
                if(e.button === 0) {
                    $.ajax
                    ({
                        type: "POST",
                        url: "{{ route("AdvancedRegistration.fileDelete") }}",
                        dataType:"json",
                        data:
                            {
                                url:file,
                                _token:"{{ csrf_token() }}",
                            },
                        success: function()
                        {
                            document.getElementById("div" + name).remove();
                            $("#deleteWorkPermissionDocumentModal").modal('hide');
                        },
                        error: function (error) {
                            console.log(error);
                        }

                    });
                }
            };
        }

        function hideDocumentModal()
        {
            $("#deleteWorkPermissionDocumentModal").modal('hide');
        }

        $('#pdf').change(function () {
            var form = document.getElementById("form");
            $.ajax({
                type: "POST",
                url: "{{ route('AdvancedRegistration.tempSaveDocuments') }}",
                dataType:"json",
                processData:false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:new FormData(form),
                success: function(data){
                    var mainDiv = $("<div>").attr("id", "mainDiv");
                    $('#workPermissionDocumentList').append(mainDiv);

                    $.each(data['files'], function(k, v) {
                        var div = $("<div>").attr('class', "form-row").attr("id", "div" + v['name']);
                        var div1 = $("<div>").attr('class', "form-group col");
                        var div2 = $("<div>").attr('class', "form-group col");
                        mainDiv.append(div);
                        var input = $("<input>").attr("type", "hidden").attr("value", v['url']).attr("name", "wpd[]");
                        div.append(div1).append(div2).append(input);
                        var a = $("<a>").attr("href", v['url']).attr("target","_blank").attr("rel","noopener").text(v['name']);
                        var li = $("<li>").append(a);
                        var ul = $("<ul>").append(li);
                        div1.append(ul);
                        var buttonDelete = $('<button>').addClass("btn btn-outline-danger fa-trash-alt fa").attr("id",v['name']).attr("type", "button");
                        div2.append(buttonDelete);
                        document.getElementById(v['name']).onclick = function()
                        {
                            deletemomDocuments(v['name'],v['url'])
                        };
                        console.log(k + " => " + v['name'] + " - " + v['url']);

                    });

                }
            });
        });

        document.getElementById("newVisitorButton").onclick = function(e)
        {
            if(e.button === 0) {
                newVisitorFormSender();
            }
        };

        jQuery(document).ready(function($) {
            var engine = new Bloodhound({
                remote: {
                    url: "{{ route('newVisitor.search') }}?query=%QUERY%",
                    wildcard: '%QUERY%'
                },
                datumTokenizer: Bloodhound.tokenizers.whitespace("query"),
                queryTokenizer: Bloodhound.tokenizers.whitespace
            });
            $(".search-input").typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                source: engine.ttAdapter(),
                name: 'usersList',
                templates: {
                    empty: [
                        '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                    ],
                    header: [
                        '<div class="list-group search-results-dropdown">'
                    ],
                    suggestion: function (data) {
                        return '<li class="list-group-item">' + data["forename"] + " " + data["surname"] + " " + data["company"] + '</li>'
                    }
                },
                limit: 10,
            }).on('typeahead:selected typeahead:autocompleted', function(event, data) {
                $(this).typeahead('val', '');
                addvisitorElement(data);
            })
        });

        function editVisitor(visitorId) {
            $.ajax({
                type: "GET",
                url: "/api/newVisitor/" + visitorId,
                dataType:"json",
                success: function(data){
                    document.getElementById("editVisitorButton").onclick = function(e)
                    {
                        if(e.button === 0) {
                            editVisitorFormSender(data["id"]);
                        }
                    };
                    $("#editvisitorCategory").val(data['visitorCategory']);
                    $("#editsalutation").val(data['salutation']);
                    $("#edittitle").val(data['title']);
                    $("#editforename").val(data['forename']);
                    $("#editsurname").val(data['surname']);
                    $("#editlanguage").val(data['language']);
                    $("#editcitizenship").val(data['citizenship']);
                    $("#editcompany").val(data['company']);
                    $("#editcompanyStreet").val(data['companyStreet']);
                    $("#editcompanyCountry").val(data['companyCountry']);
                    $("#editcompanyZipCode").val(data['companyZipCode']);
                    $("#editcompanyCity").val(data['companyCity']);
                    $("#editemail").val(data['email']);
                    $("#editlandlineNumber").val(data['landlineNumber']);
                    $("#editmobileNumber").val(data['mobileNumber']);
                    $("#editconfidentialityAgreement").val(data['confidentialityAgreement']);
                }
            });
        }
        function removeVisitor(removeId)
        {
                $("#form-row" + removeId).remove();
        }
        function editVisitorFormSender(visitorId)
        {
            $.ajax({
                type: "PUT",
                url: "/api/newVisitor/" + visitorId,
                dataType:"json",
                data: {
                    visitorCategory:$("#editvisitorCategory").val(),
                    salutation:$("#editsalutation").val(),
                    title:$("#edittitle").val(),
                    forename:$("#editforename").val(),
                    surname:$("#editsurname").val(),
                    language:$("#editlanguage").val(),
                    citizenship:$("#editcitizenship").val(),
                    company:$("#editcompany").val(),
                    companyStreet:$("#editcompanyStreet").val(),
                    companyCountry:$("#editcompanyCountry").val(),
                    companyZipCode:$("#editcompanyZipCode").val(),
                    companyCity:$("#editcompanyCity").val(),
                    email:$("#editemail").val(),
                    landlineNumber:$("#editlandlineNumber").val(),
                    mobileNumber:$("#editmobileNumber").val(),
                    confidentialityAgreement:$("#editconfidentialityAgreement").val(),
                    _token:"{{ csrf_token() }}",
                },
                success: function()
                {
                    $("#alerId").remove();
                    var trest = ($('<div>').addClass("alert alert-success alert-dismissible").attr("id", "alertId").text("@lang('main.successEditVisitorMessage')"));
                    $("#successdiv").append(trest);
                    trest.append('<a aria-label="close" data-dismiss="alert" href="#" class="close">&times;</a>');
                    window.setTimeout(function() {
                        $(".alert").fadeTo(500, 0).slideUp(500, function(){
                            $(this).remove();
                        });
                    }, 10000);
                }
            });
        }

        function toggleStillCreateModal() {
            $('#stillCreateModal').modal('hide');
            $('#New').modal('show');
        }

        function stillCreateVisitor() {
            $('#stillCreate').val(1);
            newVisitorFormSender();
            $('#stillCreateModal').modal('hide');
        }
        let stillCreate = false;

        function newVisitorFormSender()
        {
            $.ajax({
                type: "POST",
                url: "{{ route('newVisitor.store') }}",
                dataType:"json",
                data: {
                    visitorCategory:$("#visitorCategory").val(),
                    salutation:$("#salutation").val(),
                    title:$("#title").val(),
                    forename:$("#forename").val(),
                    surname:$("#surname").val(),
                    language:$("#language").val(),
                    citizenship:$("#citizenship").val(),
                    company:$("#company").val(),
                    companyStreet:$("#companyStreet").val(),
                    companyCountry:$("#companyCountry").val(),
                    companyZipCode:$("#companyZipCode").val(),
                    companyCity:$("#companyCity").val(),
                    email:$("#email").val(),
                    landlineNumber:$("#landlineNumber").val(),
                    mobileNumber:$("#mobileNumber").val(),
                    confidentialityAgreement:$("#confidentialityAgreement").val(),
                    creator:$("#creator").val(),
                    stillCreate:$('#stillCreate').val(),
                    _token:"{{ csrf_token() }}",
                },
                success: function(data)
                {
                    $('#New').modal('hide');
                    addvisitorElement(data);
                    $("#alerId").remove();
                    $("#successdiv").append($('<div>').addClass("alert alert-success alert-dismissible").attr("id", "alertId").text("@lang('main.successNewVisitorMessage')"));
                    $("#alertId").append('<a aria-label="close" data-dismiss="alert" href="#" class="close">&times;</a>');
                    window.setTimeout(function() {
                        $(".alert").fadeTo(500, 0).slideUp(500, function(){
                            $(this).remove();
                        });
                    }, 10000);
                },
                error: function (error) {
                    let invalidFieldExists = false;
                    $('.invalid-feedback').remove();
                    $('.is-invalid').removeClass('is-invalid');
                    $.each(error['responseJSON']['errors'], function(name, msg) {
                        invalidFieldExists = true;
                        if(name === "stillCreate")
                        {
                            console.log("true");
                            stillCreate = true;
                        }
                        else
                        {
                            let input = $('#' + name);
                            let inputMessage = $('<div>').addClass('invalid-feedback').html(msg);
                            input.addClass('is-invalid');
                            input.after(inputMessage);
                        }
                    });
                    if(invalidFieldExists === false)
                    {
                        $("#alerId").remove();
                        $("#successdiv").append($('<div>').addClass("alert alert-danger alert-dismissible").attr("id", "alertId").text("@lang('main.failedNewVisitor')"));
                        $("#alertId").append('<a aria-label="close" data-dismiss="alert" href="#" class="close">&times;</a>');
                        window.setTimeout(function() {
                            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                                $(this).remove();
                            });
                        }, 10000);
                    }
                    else if(stillCreate === false)
                    {
                        setTimeout(function () {
                            $('#New').modal('show');
                        }, 200);
                    }
                    else
                    {
                        $('#stillCreateModal').modal('show');
                    }
                }
            });
        }
        function addvisitorElement(data)
        {
            if($("#alertId").length)
            {
                $("#alertId").remove();
            }
            if($("#form-row" + data["id"]).length)
            {
                $("#alerId").remove();
                $("#alertDiv").append($("<div>").addClass("alert alert-danger alert-dismissible").attr("id", "alertId").text("Der Besucher wudrde bereits hinzugefügt."));
                $("#alertId").append('<a data-dismiss="alert" aria-label="close" href="#" class="close">&times;</a>');
                window.setTimeout(function() {
                    $(".alert").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove();
                    });
                }, 4000);
            }
            else
            {
                var divrow = $("<div>").addClass("form-row").attr("id", "form-row" + data["id"]);
                $("#childDiv").append(divrow);
                var div1 = $('<div>').addClass("form-group col");
                var div2 = $('<div>').addClass("form-group col");
                var div3 = $('<div>').addClass("form-group col");
                var div4 = $('<div>').addClass("form-group col");
                $("#form-row" + data["id"]).append(div1, div2, div3, div4);
                var input1 = $('<input>').attr("type", 'text').addClass('form-control').val(data["forename"]);
                var input2 = $('<input>').attr("type", 'text').addClass('form-control').val(data["surname"]);
                var input3 = $('<input>').attr("type", 'text').addClass('form-control').val(data["company"]);
                var input4 = $('<input>').attr("type", 'hidden').addClass('form-control').attr("name", "visitorids[]").val(data["id"]);
                var buttonDelete = $('<button>').addClass("btn btn-outline-danger fa-trash-alt fa").attr("id","delete" + data["id"]).attr("type", "button");
                var buttonEdit = $('<button>').addClass("btn btn-outline-success icon-pencil align-right").attr("id","edit" + data["id"]).attr("type", "button").attr("data-toggle", "modal").attr("data-target", "#Edit");
                var buttonCanteen = $('<button>').addClass("btn btn-outline-secondary fa fa-coffee align-right").attr("id","canteen" + data["id"]).attr("type", "button");
                var spacer = $('<span>').text = " ";
                div1.append(input1);
                div2.append(input2);
                div3.append(input3);
                div1.append(input4);
                div4.append(buttonDelete);
                div4.append(spacer);
                div4.append(buttonEdit);
                div4.append(spacer);
                div4.append(buttonCanteen);
                document.getElementById("edit" + data["id"]).onclick = function(e)
                {
                    if(e.button === 0)
                    {
                        editVisitor(data["id"]);
                    }
                };
                document.getElementById("delete" + data["id"]).onclick = function(e)
                {
                    if(e.button === 0) {
                        removeVisitor(data["id"]);
                    }
                };
                buttonCanteen.click(function () {
                    if($(this).hasClass("btn-success"))
                    {
                        $("#canteen" + data["id"]).remove();
                        $(this).removeClass("btn-success");
                        $(this).addClass("btn-outline-secondary");
                    }
                    else
                    {
                        var input5 = $('<input>').attr("type", 'hidden').addClass('form-control').attr("id", "canteen" + data["id"]).attr("name", "canteenIds[]").val(data["id"]);
                        div1.append(input5);
                        $(this).addClass("btn-success");
                        $(this).removeClass("btn-outline-secondary");
                    }
                });
            }
        }

        @if(!empty(old('visitorids')))
            @foreach(old('visitorids') as $item)
                $.ajax({
                    type: "GET",
                    url: "/api/newVisitor/" + "{{ $item }}",
                    dataType:"json",
                    success: function(data){
                        addvisitorElement(data);
                    }
                });
            @endforeach
        @endif

        @if(!empty(old('groupMemberForename')))
            @foreach(old('groupMemberForename') as $key => $item)
                addGroupMember("{{ $item }}", "{{ old('groupMemberSurname')[$key] }}", "{{ old('groupMemberCanteen')[$key] }}");
            @endforeach
        @endif


        var test = 0;

        function addGroupMember(groupMemberForename, groupMemberSurname, groupMemberCanteen)
        {
            groupMemberForename = groupMemberForename || null;
            groupMemberSurname = groupMemberSurname || null;
            groupMemberCanteen = groupMemberCanteen || null;
            test++;
            var divrow = $("<div>").addClass("form-row").attr("id", "groupMember" + test);
            $("#childDiv").append(divrow);
            var div1 = $('<div>').addClass("form-group col");
            var div2 = $('<div>').addClass("form-group col");
            var div3 = $('<div>').addClass("form-group col");
            var div4 = $('<div>').addClass("form-group col");
            divrow.append(div1, div2, div3, div4);
            var input1 = $('<input>').attr("type", 'text').attr("name", "groupMemberForename[]").attr("placeholder", "@lang('main.forename')").addClass('form-control').val(groupMemberForename);
            var input2 = $('<input>').attr("type", 'text').attr("name", "groupMemberSurname[]").attr("placeholder", "@lang('main.surname')").addClass('form-control').val(groupMemberSurname);
            if(groupMemberCanteen != null)
            {
                var input4 = $('<input>').attr("type", 'hidden').attr("name", "groupMemberCanteen[]").val(groupMemberCanteen);
            }
            else
            {
                var input4 = $('<input>').attr("type", 'hidden').attr("name", "groupMemberCanteen[]").val(0);
            }
            var buttonDelete = $('<button>').addClass("btn btn-outline-danger fa-trash-alt fa").attr("id","deleteGroupMember" + test).attr("type", "button");
            var spacer = $('<span>').text = " ";
            var buttonCanteen = $('<button>').addClass("btn btn-outline-secondary fa fa-coffee align-right").attr("id","canteen" + test).attr("type", "button");
            div1.append(input1);
            div2.append(input2);
            div2.append(input4);
            div4.append(buttonDelete);
            div4.append(spacer);
            div4.append(buttonCanteen);
            document.getElementById("deleteGroupMember" + test).onclick = function(e)
            {
                var id = buttonDelete.attr('id');
                $("#groupMember" + id.substr(id.length - 1)).remove();
            };
            document.getElementById("canteen" + test).onclick = function(e)
            {
                if(input4.val() === "1")
                {
                    $(this).addClass("btn-outline-secondary");
                    $(this).removeClass("btn-success");
                    input4.val(0);
                }
                else
                {
                    $(this).addClass("btn-success");
                    $(this).removeClass("btn-outline-secondary");
                    input4.val(1);
                }
            };
        }

    </script>
@endsection
