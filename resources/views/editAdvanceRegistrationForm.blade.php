@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        @lang('main.successNewVisitorMessage')
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
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
<form method="POST" action="" id="FormTest" enctype="multipart/form-data">

    <div class="form-row">
        <div class="form-group col">
            <label class="form-inline">@lang('main.startDate')</label>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col">
            <input id="startDate" type="date" class="form-control @error('startDate') is-invalid @enderror" name="startDate" autofocus>
            @error('startDate')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="form-group col">
            <input type="time" class="form-control @error('startTime') is-invalid @enderror" id="startTime"  name="startTime" autofocus>
            @error('startTime')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

    </div>

    <div class="form-row">
        <div class="form-group col">
            <label class="form-inline">@lang('main.endDate')</label>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col">
            <input type="date" class="form-control @error('endDate') is-invalid @enderror" id="endDate" name="endDate" autofocus>
            @error('endDate')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form-group col">
            <input type="time" class="form-control @error('endTime') is-invalid @enderror" id="endTime" name="endTime" autofocus>
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
            <input type="hidden" class="form-control" name="userids[]" value="{{ Auth::user()->id }}">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <input id="searchUser" autocomplete="off" class="search-user-input form-control @error('userIds') is-invalid @enderror" placeholder="@lang('main.searchAfterUser')">
        </div>
    </div>

    <div class="form-row">
        <div id="alertUserDiv" class="form-group col">
        </div>
    </div>

    <div id="childUserDiv">
    </div>
    <input type="hidden" name="userId" value="{{ Auth::user()->id }}">

    <div class="form-row">
        <div class="form-group col">
            <label class="form-floating-label">@lang('main.visitor')</label>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col">
            <input id="search" autocomplete="off" class="search-input form-control @error('visitorids') is-invalid @enderror" placeholder="@lang('main.searchVisitor')">
        </div>
    </div>

    <div class="form-row">
        <div id="alertDiv" class="form-group col">
        </div>
    </div>

    <div id="childDiv">
    </div>
    <div id="allocation"></div>
    <div id="visitId"></div>

    <input type="hidden" id="visitorId">


    <div class="form-row">
        <div class="form-group col-3 text-right">
            <label class="col-form-label-lg">@lang('main.reasonForVisit'):</label>
        </div>
        <div class="form-group col">
            <input id="reasonForVisit" type="text" class="form-control @error('reasonForVisit') is-invalid @enderror" name="reasonForVisit" value="{{ old('reasonForVisit') }}" placeholder="@lang('main.reasonForVisit')">
            @error('reasonForVisit')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col">
            <label class="form-inline">@lang('main.workPermissionDocument')</label>
        </div>
    </div>

    <div id="workPermissionDocumentList">
    </div>

    <div class="form-row">
        <div class="form-group col-3">
            <label style="height: 38px" class="btn btn-full btn-primary text-light" for="pdf">Upload @lang('main.workPermissionDocument')</label>
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
            <label for="editEntrypermissionID">@lang('main.entryPermission')</label>
            <select class="form-control @error('editEntrypermissionID') is-invalid @enderror" id="editEntrypermissionID" name="editEntrypermissionID" autofocus>
                <option @if(old('editEntrypermissionID') == "") selected @endif value="">@lang('main.Q_selectIfAnEntryPermissionIsRequired')</option>
                @foreach ($entryUsers as $entryUser)
                    <option value="{{ $entryUser->id }}">{{$entryUser->surname}}, {{$entryUser->forename}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col">
            <label for="editWorkPermissionID">@lang('main.workPermissionApproval')</label>
            <select class="form-control @error('editWorkPermissionID') is-invalid @enderror" id="editWorkPermissionID" name="editWorkPermissionID" autofocus>
                <option value="">@lang('main.Q_selectIfAnWorkPermissionApprovalIsRequired')</option>
                @foreach ($workUsers as $workUser)
                    <option value="{{ $workUser->id }}">{{$workUser->surname}}, {{$workUser->forename}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col">
            <input type="text" class="form-control @error('editEntryPermissionText') is-invalid @enderror" id="editEntryPermissionText" name="editEntryPermissionText" placeholder="@lang('main.reasonForVisitEntryPermission')" autofocus>
            @error('editEntryPermissionText')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form-group col">
            <input type="text" class="form-control @error('editWorkPermissionApprovalText') is-invalid @enderror" id="editWorkPermissionApprovalText" name="editWorkPermissionApprovalText" placeholder="@lang('main.reasonForVisitWorkPermission')" autofocus>
            @error('editWorkPermissionApprovalText')
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
            <input id="roadmap0" type="radio" class="form-check-input" name="roadmap" @if(old('roadmap') == 0) checked @endif value="0" autofocus>
            <label class="form-check-label" for="roadmap0">@lang('main.yes')</label>
        </div>
        <div class="form-check form-check-inline">
            <input id="roadmap1" type="radio" class="form-check-input" name="roadmap" @if(old('roadmap') == 1) checked @endif value="1" autofocus>
            <label class="form-check-label" for="roadmap1">@lang('main.no')</label>
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
            <input id="hygieneRegulations0" type="radio" class="form-check-input" name="hygieneRegulations" @if(old('hygieneRegulations') == 0) checked @endif value="0" autofocus>
            <label class="form-check-label" for="hygieneRegulations0">@lang('main.no')</label>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col">
            <label>@lang('main.contactMediumWhenTheVisitorArrives')</label>
        </div>
        <div class="form-group col">
            <select class="form-control @error('contactPossibility') is-invalid @enderror" id="contactPossibility" name="contactPossibility" autofocus>
                <option disabled @if(old('contactPossibility') != "E-Mail" || old('contactPossibility') != "Telefon" || old('contactPossibility') != "SMS") selected @endif >@lang('main.contactPossibility')</option>
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
</form>

<div class="modal fade" id="workPermissionDocuments" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #e5e5e5">
                <h4 class="modal-title" id="exampleModalLabel"><b>@lang('main.workPermissionDocument')</b></h4>
            </div>
            <div class="modal-body" style="background-color: #e5e5e5">
                <ul>
                    @foreach ($workPermissions as $workPermission)
                        <li><a target="_blank" rel="noopener" href="{{ URL::to($workPermission->setting_value) }}">{{ $workPermission->setting_key }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="modal-footer" style="background-color: #e5e5e5">
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteWorkPermissionDocumentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body" style="background-color: #e5e5e5">
                <h2 class="text-center w-100">@lang('main.Q_DeleteTheWorkPermissionDocuments')</h2>
            </div>
            <div class="modal-footer" style="background-color: #e5e5e5">
                <div class="form-group col">
                    <button id="deleteWorkPermissionDocument" type="button" class="btn btn-outline-danger btn-full">@lang('main.delete')</button>
                </div>
                <div class="form-group col">
                    <button id="cancelDeleteWorkPermissionDocument" type="button"  class="btn btn-outline-secondary btn-full">@lang('main.cancel')</button>
                </div>
            </div>
        </div>
    </div>
</div>
