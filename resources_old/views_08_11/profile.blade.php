@extends('layouts.layout')
@section('content')
@if(isset($success))
    @if($success)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            @lang('main.successSaveProfile')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @else
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @lang('main.failedSavingProfile')
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
@endif
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h3>@lang('main.employeeProfile')</h3>
                    <form id="userForm" method="post" @if(isset($edit) && $edit !== true) action="{{ route('profile.store') }}" @else action="{{ route('profile.storeuno') }}" @endif>
                        <div class="form-row">
                            <div class="form-group col">
                                <label>@lang('main.forename')</label>
                                <input type="hidden" name="id" id="id" value="{{ \Illuminate\Support\Facades\Auth::user()->id }}">
                                <input id="forename" name="forename" type="text" value="{{ $forename }}" @if(!isset($edit) || $edit !== true) readonly @endif class="form-control">
                            </div>
                            <div class="form-group col">
                                    <label>@lang('main.surname')</label>
                                <input id="surname" name="surname" type="text" value="{{ $surname }}" @if(!isset($edit) || $edit !== true) readonly @endif class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col">
                                    <label>@lang('main.email')</label>
                                <input id="email" name="email" type="text" value="{{ $email }}" @if(!isset($edit) || $edit !== true) readonly @endif class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col">
                                <label>@lang('main.landlineNumber')</label>
                                <input id="telephone_number" name="telephone_number" type="text" value="{{ $telephone_number }}" @if(!isset($edit) || $edit !== true) readonly @endif class="form-control">
                            </div>
                            <div class="form-group col">
                                    <label>@lang('main.mobileNumber')</label>
                                <input id="mobile_number" name="mobile_number" type="text" value="{{ $mobile_number }}" class="form-control @error('mobile_number') is-invalid @enderror">
                                @error('mobile_number')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                    <label>@lang('main.department')</label>
                                <input id="department" name="department" type="text" value="{{ $department }}" @if(!isset($edit) || $edit !== true) readonly @endif class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col">
                                <label>@lang('main.holiday') @lang('main.from')</label>
                                <input id="holidayFrom" name="holidayFrom" type="date" value="{{ $holiday_from }}" class="form-control">
                            </div>
                            <div class="form-group col">
                                <label>@lang('main.holiday') @lang('main.to')</label>
                                <input id="holidayTo" name="holidayTo" type="date" value="{{ $holiday_to }}" class="form-control">
                            </div>
                        </div>

                        @csrf
                        <div class="form-row">
                            <div class="form-group col">
                                <button id="save" type="submit" class="btn btn-outline-primary btn-full saveButton">@lang('main.save')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="needsStandInModal" tabindex="-1" role="dialog" aria-labelledby="EditlLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">@lang('main.needsStandIn')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="visitsList"></div>
            </div>
            <div class="modal-footer">
                <button id="finished" type="button" data-dismiss="modal" class="btn btn-primary col-12">@lang('main.finished')</button>
            </div>
        </div>
    </div>
</div>


@endsection
@section('scripts')
<script>
    let startDate = $("#holidayFrom");
    let endDate = $("#holidayTo");
    startDate.blur(function () {
        if(startDate.val() > endDate.val())
        {
            endDate.val(startDate.val());
        }
    });
    endDate.blur(function () {
        if(endDate.val() < startDate.val())
        {
            startDate.val(endDate.val());
        }
    });

    var submit = false;
    $('#needsStandInModal').on('hide.bs.modal', function ()
    {
        document.getElementById('userForm').submit();
    })

    $("#userForm").submit(function(e){
        if(submit){ }
        else if(startDate.val() != "" || endDate.val() != "")
        {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "{{ route('visits.holiday.show', Auth::user()->id)}}",
                dataType:"json",
                success: function(data){
                    var adv = data['advanceregistrations'] != "no_advanceregistrations";
                    if(adv)
                    {
                        var h3 = $("<h3>").text("@lang('main.advanceRegistrations')");
                        $('#visitsList').append(h3);
                        $.each(data['advanceregistrations'], function(k, v) {
                            var options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
                            var startDate = new Date(v['startDate']);
                            var endDate = new Date(v['endDate']);
                            startDate = startDate.toLocaleDateString("de-DE", options);
                            endDate = endDate.toLocaleDateString("de-DE", options);
                            var p = $("<p></p>").text("@lang('main.advanceRegistrationFrom') " + startDate + " @lang('main.till') " + endDate + " @lang('main.withTheID'): " + v['visitId']);
                            $('#visitsList').append(p);
                        });

                    }
                    var vis = data['visits'] != "no_visits";
                    if(vis)
                    {
                        var h3 = $("<h3>").text("@lang('main.visits')");
                        $('#visitsList').append(h3);
                        $.each(data['visits'], function(k, v) {
                            var options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
                            var startDate = new Date(v['startDate']);
                            var endDate = new Date(v['endDate']);
                            startDate = startDate.toLocaleDateString("de-DE", options);
                            endDate = endDate.toLocaleDateString("de-DE", options);
                            var p = $("<p></p>").text("@lang('main.advanceRegistrationFrom') " + startDate + " @lang('main.till') " + endDate + " @lang('main.withTheID'): " + v['visitId']);
                            $('#visitsList').append(p);
                        });
                    }
                    if(adv || vis)
                    {
                        e.preventDefault();
                        $('#needsStandInModal').modal('show');
                        submit = true;
                    }

                }
            });
        }
});
</script>
@endsection
