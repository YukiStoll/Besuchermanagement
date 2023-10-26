<style>
    input[readonly].transparentBackground, select[readonly].transparentBackground{
        background-color:transparent !important;
        color: black;
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
            <input id="startDate" readonly type="date" class="form-control transparentBackground @error('startDate') is-invalid @enderror" name="startDate" autofocus>
            @error('startDate')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="form-group col">
            <input type="time" readonly class="form-control transparentBackground @error('startTime') is-invalid @enderror" id="startTime"  name="startTime" autofocus>
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
            <input type="date" readonly class="form-control transparentBackground @error('endDate') is-invalid @enderror" id="endDate" name="endDate" autofocus>
            @error('endDate')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form-group col">
            <input type="time" readonly class="form-control transparentBackground @error('endTime') is-invalid @enderror" id="endTime" name="endTime" autofocus>
            @error('endTime')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col">
            <label class="form-inline">@lang('main.creator')</label>
            <input readonly  type="text" class="form-control transparentBackground" name="employee" value="{{ Auth::user()->name }}">
            <input type="hidden" class="form-control" name="userids[]" value="{{ Auth::user()->id }}">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col">
            <label id="personToVisitLable" class="form-inline d-none">@lang('main.personToVisit')</label>
            <div id="childUserDiv">
            </div>
        </div>
    </div>

    <input type="hidden" name="userId" value="{{ Auth::user()->id }}">

    <div class="form-row">
        <div class="form-group col">
            <label class="form-floating-label">@lang('main.visitor')</label>
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
            <input id="reasonForVisit" readonly type="text" class="form-control transparentBackground @error('reasonForVisit') is-invalid @enderror" name="reasonForVisit" value="{{ old('reasonForVisit') }}" placeholder="@lang('main.reasonForVisit')">
            @error('reasonForVisit')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>

    <div class="form-row" id="workPermissionLableDiv">
        <div class="form-group col">
            <label class="form-inline">@lang('main.workPermissionDocuments')</label>
        </div>
    </div>

    <div id="workPermissionDocumentList">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="roadmap">@lang('main.roadmap')</label>
        </div>
        <div class="form-check form-check-inline">
            <input id="roadmap0" type="radio" class="form-check-input" name="roadmap" disabled value="0" autofocus>
            <label class="form-check-label" for="roadmap0">@lang('main.yes')</label>
        </div>
        <div class="form-check form-check-inline">
            <input id="roadmap1" type="radio" class="form-check-input" name="roadmap" disabled value="1" autofocus>
            <label class="form-check-label" for="roadmap1">@lang('main.no')</label>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="hygieneRegulations">@lang('main.hygieneRegulationsForExternalCompanies')</label>
        </div>
        <div class="form-check form-check-inline">
            <input id="hygieneRegulations1" type="radio" class="form-check-input" name="hygieneRegulations" disabled value="1" autofocus>
            <label class="form-check-label" for="hygieneRegulations1">@lang('main.yes')</label>
        </div>
        <div class="form-check form-check-inline">
            <input id="hygieneRegulations0" type="radio" class="form-check-input" name="hygieneRegulations" disabled value="0" autofocus>
            <label class="form-check-label" for="hygieneRegulations0">@lang('main.no')</label>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col">
            <label>@lang('main.contactMediumWhenTheVisitorArrives')</label>
        </div>
        <div class="form-group col">
            <select readonly disabled class="form-control transparentBackground @error('contactPossibility') is-invalid @enderror" id="contactPossibility" name="contactPossibility" autofocus>
                <option class="transparentBackground" disabled value="E-Mail">@lang('main.email')</option>
                <option class="transparentBackground" disabled value="Telefon">@lang('main.phone')</option>
            </select>
            @error('contactPossibility')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
</form>
