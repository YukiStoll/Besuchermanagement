@extends('layouts.layout')
@section('content')
    <div id="successdiv"></div>
    @if(isset($requestData) || !$data->isEmpty())
        <div class="w-100 mw-100 container table-responsive">
            <h3>@lang('main.visits')</h3>
            <form method="get" action="{{ route('Visits') }}">
                <div class="form-row">
                    <div class="justify-content-start col-4">
                        <div class="form-row col-6">
                            <div class="form-group">
                                <button type="button" data-target="#spontaneousVisit" data-toggle="modal" class="btn form-control btn-primary">@lang('main.createSupplierVisit')</button>
                            </div>
                        </div>
                    </div>
                    <div class="justify-content-end col">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="col-form-label-lg">Von</label>
                            </div>

                            <div class="form-group col-auto">
                                <input name="von" @isset($requestData['startDate']) value="{{ $requestData['startDate'] }}" @endisset class="form-control form-inline" type="date">
                            </div>

                            <div class="form-group">
                                <label class="col-form-label-lg">Bis</label>
                            </div>

                            <div class="form-group col-auto">
                                <input name="bis" @isset($requestData['endDate']) value="{{ $requestData['endDate'] }}" @endisset class="form-control" type="date">
                            </div>

                            <div class="form-group col-5">
                                <input name="search" @isset($requestData['search']) value="{{ $requestData['search'] }}" @endisset class="form-control" type="text" placeholder="@lang('main.searchAfterSnameFnameCompany')">
                            </div>
                            <input name="items" value="{{ $pagitems }}" type="hidden">
                            <div class="form-group col-auto">
                                <button type="submit" class="btn form-control btn-primary fa fa-search"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div><hr class="invisible"></div>
            <div>
                <div class="row">
                    <div class="col">
                        <table class="table table-hover table-striped">
                            <thead @if(env("APP_table_Color")) class="table-dark" style="background: {{ env("APP_table_Color") }}" @else class="thead-dark" @endif>
                            <tr>
                                <th scope="col">@sortablelink('startDate', __('main.startDate'))</th>
                                <th scope="col">@sortablelink('endDate', __('main.endDate'))</th>
                                <th scope="col">@sortablelink('Visitor', __('main.visitor'))</th>
                                <th scope="col">@sortablelink('Company', __('main.company'))</th>
                                <th scope="col">@sortablelink('visitorCategory', __('main.visitorCategory'))</th>
                                <th scope="col">@sortablelink('name', __('main.employee'))</th>
                                <th scope="col">@sortablelink('visitId', __('main.visitId'))</th>
                                <th style="width: 10.00%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($requestData) && empty($requestData))
                            @else
                                @foreach($data as $items)
                                    <tr id="{{ $items->id }}">
                                        <td>{{ date('d.m.Y H:i', strtotime($items->startDate)) }}</td>
                                        <td>{{ date('d.m.Y H:i', strtotime($items->endDate)) }}</td>
                                        @if($items->safetyTest == 2)
                                            <td class="text-danger">{{ $items->Visitor }}</td>
                                        @elseif($items->safetyTest == 1)
                                            <td class="text-warning">{{ $items->Visitor }}</td>
                                        @elseif($items->safetyTest == 0)
                                            <td class="text-success">{{ $items->Visitor }}</td>
                                        @endif
                                        <td>{{ $items->Company }}</td>
                                        <td>{{ $items->visitorCategory }} @if($items->isgroup == 1) (@lang('main.group')) @endif</td>
                                        <td>{{ $items->name }}</td>
                                        <td>{{ $items->visitId }}</td>
                                        <td class="text-right" nowrap="nowrap">
                                            @if ($items->entrypermission == 'granted')
                                                <i class="btn btn-outline-success fa-car fa"></i>
                                            @elseif ($items->entrypermission == 'pending')
                                                <i class="btn btn-outline-warning fa-car fa"></i>
                                            @elseif ($items->entrypermission == 'denied')
                                                <i class="btn btn-outline-danger fa-car fa"></i>
                                            @endif
                                            <button type="button" data-target="#Inspect" data-toggle="modal" onclick="getVisit({{ $items->id }})" class="btn btn-outline-primary fa-search-plus fa"></button>
                                            <button type="button" data-target="#Finish" data-toggle="modal" onclick="finishButton({{ $items->id }})" class="btn btn-outline-dark fa fa-door-closed align-right"></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        <div>
                            <div class="btn-group float-right">
                                <a class="btn @if($pagitems == 5)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 5) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '5','von' => $requestData['startDate'],'bis' => $requestData['endDate'],'search' => $requestData['search'], 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">5</a>
                                <a class="btn @if($pagitems == 10)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 10) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '10','von' => $requestData['startDate'],'bis' => $requestData['endDate'],'search' => $requestData['search'], 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">10</a>
                                <a class="btn @if($pagitems == 25)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 25) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '25','von' => $requestData['startDate'],'bis' => $requestData['endDate'],'search' => $requestData['search'], 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">25</a>
                                <a class="btn @if($pagitems == 50)  btn-dark  @else btn-light @endif btn-sm" @if($pagitems == 50) @if(env("APP_table_Color")) style="background: {{ env("APP_table_Color") }}" @endif @endif href="{{ $data->appends(['items' => '50','von' => $requestData['startDate'],'bis' => $requestData['endDate'],'search' => $requestData['search'], 'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "", 'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""])->url(1) }}">50</a>
                            </div>
                            {{ $data->appends(
                            [
                                'items' => $pagitems,
                                'von' => $requestData['startDate'],
                                'bis' => $requestData['endDate'],
                                'search' => $requestData['search'],
                                'sort' => isset($_GET["sort"]) ? $_GET["sort"] : "",
                                'direction' => isset($_GET["direction"]) ? $_GET["direction"] : ""
                            ]
                        )->links() }}
                        </div>
                    </div>
                </div>
            </div>
            @else
                <h2>@lang('main.noVisits')</h2>
            @endif

            <div class="modal fade" id="Inspect" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 id="header" class="text-center w-100">@lang('main.visit'):</h2>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <form>
                                <div class="form-row">
                                    <div class="form-group col">
                                        <label class="form-inline">@lang('main.startDate')</label>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col">
                                        <input id="startDate" type="date" class="form-control" name="startDate" disabled>
                                    </div>
                                    <div class="form-group col">
                                        <input type="time" class="form-control" id="startTime"  name="startTime" disabled>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col">
                                        <label class="form-inline">@lang('main.endDate')</label>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col">
                                        <input type="date" class="form-control" id="endDate" name="endDate" disabled>
                                    </div>
                                    <div class="form-group col">
                                        <input type="time" class="form-control" id="endTime" name="endTime" disabled>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col">
                                        <label class="form-inline">@lang('main.creator')</label>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col">
                                        <input disabled  type="text" class="form-control" id="employee" name="employee" value="">
                                    </div>
                                    <div class="form-group col" name="contactHide">
                                        <input disabled  type="text" class="form-control" id="phone-number" name="phone-number">
                                    </div>
                                    <div class="form-group col" name="contactHide">
                                        <input disabled type="text" class="form-control" id="mobile-phone-number" name="mobile-phone-number">
                                    </div>
                                </div>

                                <div id="childUserLable" class="form-row">
                                    <div class="form-group col">
                                        <label class="form-inline">@lang('main.visitedpersons')</label>
                                    </div>
                                </div>

                                <div id="childUserDiv">
                                </div>

                                <div class="form-row" name="contactHide">
                                    <div class="form-group col">
                                        <label class="form-inline text-warning">@lang('main.pleaseCallTheEmployee')</label>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col">
                                        <label class="form-inline">@lang('main.visitors')</label>
                                    </div>
                                </div>

                                <div id="visitors">
                                </div>

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

                                <div id="spont">
                                </div>



                                <div id="workPermissionDocumentList">
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-3">
                                    </div>
                                    <div class="form-group col" id="entrypermission">
                                    </div>
                                    <div class="form-group col-3">
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <div class="modal fade" id="Finish" tabindex="-1" role="dialog" aria-labelledby="EditlLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">@lang('main.finishVisit')</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @lang('main.Q_WouldYouLikeToMarkTheVisitAsDone')
                    </div>
                    <div class="modal-footer">
                        <button id="finishButton" type="button" class="btn btn-primary col">@lang('main.finish')</button>
                        <button id="cancelButton" data-dismiss="modal" type="button" class="btn btn-secondary col">@lang('main.cancel')</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="registerVisitor" tabindex="-1" role="dialog" aria-labelledby="EditlLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">@lang('main.unlockVisitorCard')</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col">
                                <form>
                                    <label hidden id="yellow" for="cardId">@lang('main.Q_pleaseScanTheYellowCardOrEnterThe')</label>
                                    <label hidden id="green" for="cardId">@lang('main.Q_pleaseScanTheGreenCardOrEnterThe')</label>
                                    <label hidden id="pink" for="cardId">@lang('main.Q_pleaseScanThePinkCardOrEnterThe')</label>
                                    <input id="cardId" type="text" class="form-control" autofocus name="cardId" autocomplete="off">
                                    <input id="visitorId" type="hidden" name="visitorId">
                                    <input id="table_id" type="hidden" class="form-control" name="table_id">
                                    <input id="allocationId" type="hidden" class="form-control" name="allocationId">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="visitorCardUnlockButton" type="button" class="btn btn-outline-primary col">@lang('main.unlock')</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="logoutVisitor" tabindex="-1" role="dialog" aria-labelledby="EditlLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">@lang('main.lockVisitorCard')</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col">
                                <form>
                                    <label for="freecardId">@lang('main.Q_pleaseScanTheCardOrEnterThe')</label>
                                    <input id="freecardId" type="text" class="form-control" name="freecardId" autocomplete="off">
                                    <input id="visitor_id_logout" type="hidden" name="visitor_id_logout">
                                    <input id="table_id_logout" type="hidden" name="table_id_logout">
                                    <input id="visit_id_logout" type="hidden" name="visit_id_logout">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="visitorCardlogoutButton" onclick="visitorCardlogout()" type="button" class="btn btn-outline-primary col">@lang('main.freeCard')</button>
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

        <div class="modal fade" id="spontaneousVisit" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="text-center w-100">@lang('main.spontaneousVisitOfASupplier')</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @include('spontaneousVisitForm')
                    </div>
                    <div class="modal-footer">
                        <div class="form-group col">
                            <button id="del" type="button" onclick="submitSpontaneousVisitForm()" data-dismiss="modal" class="btn btn-outline-success w-100 saveButton">@lang('main.create')</button>
                        </div>
                        <div class="form-group col">
                            <button id="canc" type="button"  data-dismiss="modal" class="btn btn-outline-secondary w-100">@lang('main.cancel')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="callModal" tabindex="-1" role="dialog" aria-labelledby="EditlLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">@lang('main.contactEmployee')</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @lang('main.Q_contactEmployee') <span id="callModalTele"></span>
                    </div>
                    <div class="modal-footer">
                        <button id="fin" data-dismiss="modal" type="button" class="btn btn-primary col">@lang('main.finished')</button>
                    </div>
                </div>
            </div>
        </div>


@endsection
@section('scripts')
    <script>
        var counter = 1;
        var visitorCardUnlockButtonClick = false;
    function submitSpontaneousVisitForm()
        {
            $('#spontaneousVisitForm').submit();
        }
        function finishButton(id) {
            document.getElementById("finishButton").onclick = function(){finishVisit(id)};
        }

        function finishVisit(id) {
            document.getElementById("finishButton").onclick = null;
            $.ajax({
                type: "DELETE",
                url: "/api/newVisit/" + id,
                dataType:"json",
                data: {
                    endDate:"{{ Illuminate\Support\Carbon::createFromDate()->tz('Europe/Berlin') }}",
                    deleted_at:"{{ Illuminate\Support\Carbon::createFromDate()->tz('Europe/Berlin') }}",
                    deleted_from_id:"{{ Auth::user()->id }}",
                    _token:"{{ csrf_token() }}",
                },
                success: function(data){
                    window.location.replace("{{ route("Visits") }}?success=visit_finished");
                },
                error: function (error) {
                    if(error['responseJSON']['key'] === "VISITOR_HAS_CARD")
                    {
                        $('#Finish').modal('toggle');

                        $('#successdiv')
                            .append($("<div>")
                                .addClass('alert alert-danger alert-dismissible')
                                .attr("id", "alertId")
                                .text("@lang('main.oneOrMoreHaveACardAssigned')")
                                .append($("<a>")
                                    .addClass("close")
                                    .attr("data-dismiss", "alert")
                                    .attr("aria-label", "close")
                                    .html("&times;")
                                ));
                        window.setTimeout(function () {
                            $("#alertId").fadeTo(500, 0).slideUp(500, function () {
                                $(this).remove();
                            });
                        }, 10000);
                    }
                    else
                    {
                        $('#Finish').modal('toggle');

                        $('#successdiv')
                            .append($("<div>")
                                .addClass('alert alert-danger alert-dismissible')
                                .attr("id", "alertId")
                                .text(error['responseJSON'])
                                .append($("<a>")
                                    .addClass("close")
                                    .attr("data-dismiss", "alert")
                                    .attr("aria-label", "close")
                                    .html("&times;")
                                ));
                        window.setTimeout(function () {
                            $("#alertId").fadeTo(500, 0).slideUp(500, function () {
                                $(this).remove();
                            });
                        }, 10000);

                    }
                }
            });
        }

        function getVisit(visitId)
        {
            const divPhoneNumber = $('div[name="contactHide"]');
            divPhoneNumber.attr('hidden', true);
            $.ajax({
                type: "POST",
                url: "/api/newVisit/" + visitId,
                dataType:"json",
                data:{_token:"{{ csrf_token() }}"},
                success: function(data)
                {
                    $('#header').text("@lang('main.visit'): " + data['visitId']);
                    $('#startDate').val(data['startDate']);
                    $('#startTime').val(data['startTime']);
                    $('#endDate').val(data['endDate']);
                    $('#endTime').val(data['endTime']);




                    $('#employee').val(data['employee']['name']);
                    var contactByPhone = data['contactPossibility'] == 'Telefon';

                    var allocationid = data['allocationid'];
                    $.ajax
                    ({
                        type: "POST",
                        url: "{{ route('visitsGetUsers') }}",
                        dataType:"json",
                        data:
                            {
                                allocationid:data['allocationid'],
                                _token:"{{ csrf_token() }}",
                            },
                        success: function(data)
                        {
                            $("#childUserDiv").empty();
                            $("#childUserLable").addClass("invisible");
                            if(data['users'] != undefined && ['users'].length > 0)
                            {
                                $("#childUserLable").removeClass("invisible");
                            }
                            $.each(data['users'], function(k, v) {
                                $.each(v, function(kv, vv) {
                                    addUserElement(vv, allocationid, "childUserDiv", contactByPhone);
                                });
                            });
                        },
                        error: function (error) {
                            console.log(error);
                        }

                    });







                    if(contactByPhone)
                    {
                        $('#phone-number').val(data['employee']['telephone_number']);
                        $('#mobile-phone-number').val(data['employee']['mobile_number']);
                        divPhoneNumber.removeAttr('hidden');
                    }
                    $("#reasonForVisit").val(data['reasonForVisit']);
                    $("#visitors").empty();
                    $.each(data['visitors'], function(k, v) {
                        addvisitorElement(v, data['visitId']);
                    });
                    $('#mainDiv').remove();
                    $('#entryPermission').remove();
                    //'none', 'pending', 'granted', 'denied
                    if(data['entryPermission'] == 'granted')
                    {
                        var entryPermission = $("<input>").attr('class', "form-control text-center text-success font-weight-bold").attr('style', 'background: white !important; opacity: 100 !important;').attr('disabled', '').attr("id", "entryPermission").val('@lang('main.entryPermitHasBeenGranted')');
                        $('#entrypermission').append(entryPermission)
                    }
                    else if(data['entryPermission'] == 'denied')
                    {
                        var entryPermission = $("<input>").attr('class', "form-control text-center text-danger font-weight-bold").attr('style', 'background: white !important; opacity: 100 !important;').attr('disabled', '').attr("id", "entryPermission").val('@lang('main.entryPermitHasBeenDenied')');
                        $('#entrypermission').append(entryPermission)
                    }
                    else if(data['entryPermission'] == 'pending')
                    {
                        var entryPermission = $("<input>").attr('class', "form-control text-center text-warning font-weight-bold").attr('style', 'background: white !important; opacity: 100 !important;').attr('disabled', '').attr("id", "entryPermission").val('@lang('main.entryPermitIsPending')');
                        $('#entrypermission').append(entryPermission)
                    }
                    $('#spont').empty();
                    if(data['carrier'] != null && data['carrier'] != "" || data['orderNumber'] != null && data['orderNumber'] != "" || data['cargo'] != null && data['cargo'] != "" || data['vehicleRegistrationNumber'] != null && data['vehicleRegistrationNumber'] != "")
                    {
                        var divrow1 = $("<div>").attr('class', "form-row");
                        var divrow2 = $("<div>").attr('class', "form-row");
                        var divrow3 = $("<div>").attr('class', "form-row");
                        var divrow4 = $("<div>").attr('class', "form-row");
                        var div1 = $("<div>").attr('class', "form-group col");
                        var div2 = $("<div>").attr('class', "form-group col");
                        var div3 = $("<div>").attr('class', "form-group col");
                        var div4 = $("<div>").attr('class', "form-group col");
                        var divlabel1 = $("<div>").attr('class', "form-group col-3 text-right");
                        var divlabel2 = $("<div>").attr('class', "form-group col-3 text-right");
                        var divlabel3 = $("<div>").attr('class', "form-group col-3 text-right");
                        var divlabel4 = $("<div>").attr('class', "form-group col-3 text-right");
                        var label1 = $("<label>").attr('class', "col-form-label-lg").text("@lang('main.carrier'):");
                        var label2 = $("<label>").attr('class', "col-form-label-lg").text("@lang('main.orderNumber'):");
                        var label3 = $("<label>").attr('class', "col-form-label-lg").text("@lang('main.cargo'):");
                        var label4 = $("<label>").attr('class', "col-form-label-lg").text("@lang('main.vehicleRegistrationNumber'):");
                        var input1 = $("<input>").attr({
                            'class':"form-control",
                            'name':"carrier",
                            'value':data['carrier'],
                            'id':"carrier"
                        });
                        var input2 = $("<input>").attr({
                            'class':"form-control",
                            'name':"orderNumber",
                            'value':data['orderNumber'],
                            'id':"orderNumber"
                        });
                        var input3 = $("<input>").attr({
                            'class':"form-control",
                            'name':"cargo",
                            'value':data['cargo'],
                            'id':"cargo"
                        });
                        var input4 = $("<input>").attr({
                            'class':"form-control",
                            'name':"vehicleRegistrationNumber",
                            'value':data['vehicleRegistrationNumber'],
                            'id':"vehicleRegistrationNumber"
                        });
                        $('#spont').append(divrow1, divrow2, divrow3, divrow4);
                        divrow1.append(divlabel1);
                        divrow2.append(divlabel2);
                        divrow3.append(divlabel3);
                        divrow4.append(divlabel4);
                        divrow1.append(div1);
                        divrow2.append(div2);
                        divrow3.append(div3);
                        divrow4.append(div4);
                        divlabel1.append(label1);
                        divlabel2.append(label2);
                        divlabel3.append(label3);
                        divlabel4.append(label4);
                        div1.append(input1);
                        div2.append(input2);
                        div3.append(input3);
                        div4.append(input4);

                    }
                    if(data['workPermissionDocuments'] != null)
                    {
                        var mainDiv = $("<div>").attr("id", "mainDiv");
                        $('#workPermissionDocumentList').append(mainDiv);
                        $.each(data['workPermissionDocuments'], function(k, v) {
                            var div = $("<div>").attr('class', "form-row").attr("id", "div" + v['name']);
                            var div1 = $("<div>").attr('class', "form-group col");
                            var div2 = $("<div>").attr('class', "form-group col");
                            mainDiv.append(div);
                            div.append(div1).append(div2);
                            var a = $("<a>").attr("href", v['url']).attr("target","_blank").attr("rel","noopener").text(v['name']);
                            var li = $("<li>").append(a);
                            var ul = $("<ul>").append(li);
                            div1.append(ul);
                            document.getElementById('cancelDeleteWorkPermissionDocument').onclick = function(e)
                            {
                                $("#deleteWorkPermissionDocumentModal").modal('hide');
                            };

                            document.getElementById(v['name']).onclick = function()
                            {
                                deletemomDocuments(v['name'],v['url'])
                            };
                        });
                    }
                },
                error: function (data)
                {
                    console.log("###################################");
                    console.log(data);
                    console.log("###################################");
                }
            });
        }
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
                            console.log("###################################");
                            console.log(error);
                            console.log("###################################");
                        }

                    });
                }
            };
        }
        function addvisitorElement(data, visitId)
        {
                var divrow = $("<div>").addClass("form-row").attr("id", "form-row" + counter);
                $("#visitors").append(divrow);
                var div1 = $('<div>').addClass("form-group col");
                var div2 = $('<div>').addClass("form-group col");
                var div3 = $('<div>').addClass("form-group col");
                var div4 = $('<div>').addClass("form-group col").attr("id", "mawarow-" + data["id"]);
                var div5 = $('<div>').addClass("form-group col-1");
                var div6 = $('<div>').addClass("form-group col");
                $("#form-row" + counter).append(div1, div2, div3, div4, div5, div6);
                var input1 = $('<input>').attr("type", 'text').addClass('form-control').val(data["forename"]);
                var input2 = $('<input>').attr("type", 'text').addClass('form-control').val(data["surname"]);
                var input3 = $('<input>').attr("type", 'text').addClass('form-control').val(data["company"]);
                var input4 = $('<input>').attr("type", 'text').attr("readonly", "true").addClass('form-control').val(data["cardId"]);
                var spanRegister = $('<span>').attr("data-toggle","tooltip").attr("data-placement","top").attr("title","@lang('main.assignCard')");
                var spanLogout = $('<span>').attr("data-toggle","tooltip").attr("data-placement","top").attr("title","@lang('main.freeCard')");
                var spanShowQRCode = $('<span>').attr("data-toggle","tooltip").attr("data-placement","top").attr("title","@lang('main.showQRCode')");
                var spanShowTestAnswers = $('<span>').attr("data-toggle","tooltip").attr("data-placement","top").attr("title","@lang('main.showTestAnswers')");

                console.log(data);
                var select = $('<select>').attr("id", "areaPermissionSelect-" + data["id"] + "[]").addClass('form-control selectpicker').attr("name", "areaPermissionSelect-" + data["id"] + "[]").attr("multiple", "multiple").attr("disabled", "disabled");
                var areaPermissions = {!! json_encode($areaPermissions) !!};
                var selectedAreaPermissions = data["mawaAreaIds"] != null ? data["mawaAreaIds"] : ["-1"];
                var selectedAreaPermissionsIds = [];
                selectedAreaPermissions.forEach(item => selectedAreaPermissionsIds.push(item['areapermissionID']));

                areaPermissions.forEach(item => select.append($('<option>').val(item.id).text(item.name).attr("selected", selectedAreaPermissionsIds.includes(item.id))));

                if(data['cardId'] == "" || data['cardId'] == null)
                {
                    var registerVisitorButton = $('<button>').addClass("btn btn-outline-primary fa fa-door-open").attr("id",data["id"] + "-" + data['table_id']).attr("data-toggle","modal").attr("data-target","#registerVisitor").attr("data-dismiss","modal");
                    var logOutVisitorButton = $('<button>').addClass("btn btn-outline-secondary fa fa-door-closed").attr("id","logout" + data["id"] + "-" + data['table_id']).attr("data-toggle","modal").attr("data-target","#logoutVisitor").attr("data-dismiss","modal").attr("disabled", "true");
                }
                else
                {
                    var registerVisitorButton = $('<button>').addClass("btn btn-outline-primary fa fa-door-open").attr("id",data["id"] + "-" + data['table_id']).attr("data-toggle","modal").attr("data-target","#registerVisitor").attr("data-dismiss","modal").attr('disabled','true');
                    var logOutVisitorButton = $('<button>').addClass("btn btn-outline-secondary fa fa-door-closed").attr("id","logout" + data["id"] + "-" + data['table_id']).attr("data-toggle","modal").attr("data-target","#logoutVisitor").attr("data-dismiss","modal");
                }

                var QRCodeButton = $('<button>').addClass("btn btn-outline-secondary fa fa-qrcode").attr("id",data["id"] + "-" + data['table_id']).attr('type','button');
                if(data['questionsSafetyInstructions'] != null && data['questionsSafetyInstructions'] != "null")
                {
                    var safetyInstructionQuestions = $('<button>').addClass("btn btn-outline-danger fa fa-question").attr('type','button');
                }
                else if(data['questionsSafetyInstructions'] == null && data['safetyInstruction'] == null  || data['questionsSafetyInstructions'] == "null" && data['safetyInstruction'] == null)
                {
                    var safetyInstructionQuestions = $('<button>').addClass("btn btn-outline-warning fa fa-question").attr('type','button');
                }
                else
                {
                    var safetyInstructionQuestions = $('<button>').addClass("btn btn-outline-success fa fa-question").attr('type','button');
                }
                var spacer = $('<span>').text = " ";
                div1.append(input1);
                div2.append(input2);
                div3.append(input3);
                div6.append(spanRegister);
                div4.append(select);
                div5.append(input4);
                spanRegister.append(registerVisitorButton);
                div6.append(spacer);
                div6.append(spanLogout);
                spanLogout.append(logOutVisitorButton);
                div6.append(spacer);
                div6.append(spanShowQRCode);
                spanShowQRCode.append(QRCodeButton);
                div6.append(spacer);
                div6.append(spanShowTestAnswers);
                spanShowTestAnswers.append(safetyInstructionQuestions);

                select.selectpicker();



                const targetNode = document.getElementById("mawarow-" + data["id"]);
                selectedAreaPermissions.forEach(item =>
                {
                    for (const a of targetNode.querySelectorAll(".filter-option-inner-inner"))
                    {
                        if (a.textContent.includes(item['name']))
                        {
                            var replace = escapeHtml(item['name']);
                            if(item['status'] == "granted")
                            {
                                a.innerHTML = a.innerHTML.replace(replace, '<span class="text-success">' + item['name'] + '</span>');
                            }
                            else if(item['status'] == "denied")
                            {
                                a.innerHTML = a.innerHTML.replace(replace, '<span class="text-danger">' + item['name'] + '</span>');
                            }
                            else
                            {
                                a.innerHTML = a.innerHTML.replace(replace, '<span class="text-warning">' + item['name'] + '</span>');
                            }
                        }
                    }
                });



                registerVisitorButton.click(function(e)
                {
                    setVisitorCardModal(data["id"], data['table_id'], data["visitorCategory"], data['allocationid'], visitId);
                });
                QRCodeButton.click(function(e)
                {
                    var win = window.open("/printQRCode/" + visitId + '-' + data["id"] + '-' + data['table_id'] + "/" + data['forename'] + " " + data['surname'], '_blank');
                    win.focus();
                });
                safetyInstructionQuestions.click(function(e)
                {
                    var win = window.open("/safetyInstructionQuestions/" + data["id"] + '-' + data['table_id'], '_blank');
                    win.focus();
                });
                logOutVisitorButton.click(function(e)
                {
                    $('#visitor_id_logout').val(data["id"]);
                    $('#visit_id_logout').val(visitId);
                    $('#table_id_logout').val(data['table_id']);
                });
            counter = counter + 1;
        }
        $('#registerVisitor').on('shown.bs.modal', function() {
            $('#cardId').attr('placeholder', '')
            $('#cardId').focus();
            $('#cardId').val("");
            $('#visitorCardUnlockButton').removeAttr("disabled");
        });

        $('#logoutVisitor').on('shown.bs.modal', function() {
            var freecard = $('#freecardId');
            freecard.focus();
            freecard.val("");
            freecard.attr("class","form-control");
        });
        function visitorCardlogout()
        {
            if($('#freecardId').val() == "")
            {
                $('#freecardId').attr('class', 'form-control border-danger').attr('placeholder', '@lang('main.pleaseEnterCardID')');
            }
            else
            {
                $.ajax
                ({
                    type: "delete",
                    url: "/api/MaWa-Badge/" + $('#freecardId').val() + "/" + $('#visit_id_logout').val() + $('#visitor_id_logout').val() + $('#table_id_logout').val(),
                    success: function (data) {

                        if (data['success'] == false) {
                            $('#logoutVisitor').modal('toggle');
                            var body2 = "";
                            var body = " (" + data['body'] + ")";
                            if (data['body2'] != null && data['body2'] != "" && data['body2'] != "undefined") {
                                body2 = " (" + data['body2'] + ")";
                            }
                            if(data['key'] == "WRONG_PERSON")
                            {
                                body2 = "";
                                body = " (@lang('main.theVisitorIsAssignedADifferentCard')";
                            }
                            else if(data['key'] == "THE_VISIT_IS_IN_THE_PAST")
                            {
                                body2 = "";
                                data['body'] = "@lang('main.theVisitIsInThePast')";
                            }
                            $('#successdiv')
                                .append($("<div>")
                                    .addClass('alert alert-danger alert-dismissible')
                                    .attr("id", "alertId")
                                    .text("@lang('main.badgeFailedToFree')" + body + body2)
                                    .append($("<a>")
                                        .addClass("close")
                                        .attr("data-dismiss", "alert")
                                        .attr("aria-label", "close")
                                        .html("&times;")
                                    ));
                            window.setTimeout(function () {
                                $("#alertId").fadeTo(500, 0).slideUp(500, function () {
                                    $(this).remove();
                                });
                            }, 10000);
                        } else {
                            $('#logoutVisitor').modal('toggle');
                            $('#successdiv')
                                .append($("<div>")
                                    .addClass('alert alert-success alert-dismissible')
                                    .attr("id", "alertId")
                                    .text("@lang('main.theBadgeWasSuccessfullyFreed')")
                                    .append($("<a>")
                                        .addClass("close")
                                        .attr("data-dismiss", "alert")
                                        .attr("aria-label", "close")
                                        .html("&times;")
                                    ));
                            window.setTimeout(function () {
                                $("#alertId").fadeTo(500, 0).slideUp(500, function () {
                                    $(this).remove();
                                });
                            }, 10000);
                            //$("logout" + id + "-" + table_id).removeAttr('disabled').addClass("btn-outline-primary").removeClass("btn-outline-secondary");
                            //$(id + "-" + table_id).attr('disabled', true).addClass("btn-outline-secondary").removeClass("btn-outline-primary");
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    }

                });
            }
        }
        function setVisitorCardModal(id, table_id, visitorCategory, allocationId, visitId)
        {
            $('#yellow').attr("hidden", "hidden");
            $('#pink').attr("hidden", "hidden");
            $('#green').attr("hidden", "hidden");
            var card = $("#cardId");
            switch (visitorCategory) {
                case "Besucher":
                    document.getElementById("green").removeAttribute("hidden");
                    card.attr('class', 'form-control border-success');
                    break;
                case "Handwerker":
                    document.getElementById("pink").removeAttribute("hidden");
                    card.attr('class', 'form-control border-danger');
                    break;
                case "Lieferant":
                    document.getElementById("yellow").removeAttribute("hidden");
                    card.attr('class', 'form-control border-warning');
                    break;
            }
            $('#visitorId').val(id);
            $('#table_id').val(table_id);
            $('#visit_id').val(visitId);
            $('#allocationId').val(allocationId);
            if(visitorCardUnlockButtonClick == false)
            {
                $('#visitorCardUnlockButton').click(function(e)
                {
                    visitorCardUnlockButtonClick = true;
                    var visitorCardUnlockButton = $('#visitorCardUnlockButton');
                    visitorCardUnlockButton.attr("disabled", "true");
                    var cardField = $('#cardId');
                    if(cardField.val() == null || cardField.val() == "")
                    {
                        cardField.attr('class', 'form-control border-danger').attr('placeholder', '@lang('main.pleaseEnterCardID')');
                        visitorCardUnlockButton.removeAttr("disabled");
                    }
                    else
                    {
                        $.ajax
                        ({
                            type: "POST",
                            url: "{{ route('getMaWaVisitor') }}",
                            dataType:"json",
                            data:
                                {
                                    visitorId:$('#visitorId').val(),
                                    table_id:$('#table_id').val(),
                                    allocationId:$('#allocationId').val(),
                                    _token:"{{ csrf_token() }}",
                                },
                            success: function(data)
                            {
                                $.ajax
                                ({
                                    type: "post",
                                    url: "/api/MaWa-Badge/" + $('#cardId').val(),
                                    success: function () {
                                        $.ajax
                                        ({
                                            type: "put",
                                            url: "{{ route('MaWa.store') }}",
                                            data: {
                                                visitID:data['transactionId'].replace('-','').replace('-',''),
                                                visitor:{
                                                    id:$('#visitorId').val(),
                                                    forename:data['visitor']['forename'],
                                                    surname:data['visitor']['surname'],
                                                    company:data['visitor']['company'],
                                                },
                                                badge_number:$('#cardId').val(),
                                                dates:{
                                                    startDate:data['date']['startDate'],
                                                    endDate:data['date']['endDate'],
                                                },
                                                allocationId:$('#allocationId').val(),
                                            },
                                            success: function(data)
                                            {
                                                if(data['success'] == false)
                                                {
                                                    var body2 = "";
                                                    if(data['body2'] != null && data['body2'] != "" && data['body2'] != "undefined")
                                                    {
                                                        body2 = " (" + data['body2'] + ")";
                                                    }
                                                    if(data['key'] == "CARD_ALREADY_EXISTS")
                                                    {
                                                        body2 = "";
                                                        data['body'] = "@lang('main.cardAlreadyExists')";
                                                    }
                                                    else if(data['key'] == "THE_VISIT_IS_IN_THE_PAST")
                                                    {
                                                        body2 = "";
                                                        data['body'] = "@lang('main.theVisitIsInThePast')";
                                                    }
                                                    else if(data['key'] == "VISITOR_ALREADY_ASSIGNED_TO_A_BADGE")
                                                    {
                                                        body2 = "";
                                                        data['body'] = "@lang('main.visitorsAlreadyAssignedToAnotherBadge')";
                                                    }
                                                    console.log("@lang('main.badgeFailed')" + " (" + data['body'] + ")" + body2);
                                                    $('#registerVisitor').modal('toggle');
                                                    $('#successdiv')
                                                        .append($("<div>")
                                                            .addClass('alert alert-danger alert-dismissible')
                                                            .attr("id","alertId")
                                                            .text("@lang('main.badgeFailed')" + " (" + data['body'] + ")" + body2)
                                                            .append($("<a>")
                                                                .addClass("close")
                                                                .attr("data-dismiss", "alert")
                                                                .attr("aria-label", "close")
                                                                .html("&times;")
                                                            ));
                                                    window.setTimeout(function() {
                                                        $("#alertId").fadeTo(500, 0).slideUp(500, function(){
                                                            $(this).remove();
                                                        });
                                                    }, 10000);
                                                }
                                                else
                                                {
                                                    $('#registerVisitor').modal('toggle');
                                                    $('#successdiv')
                                                        .append($("<div>")
                                                            .addClass('alert alert-success alert-dismissible')
                                                            .attr("id","alertId")
                                                            .text("@lang('main.theBadgeWasSuccessfullyAssigned')")
                                                            .append($("<a>")
                                                                .addClass("close")
                                                                .attr("data-dismiss", "alert")
                                                                .attr("aria-label", "close")
                                                                .html("&times;")
                                                            ));
                                                    window.setTimeout(function() {
                                                        $("#alertId").fadeTo(500, 0).slideUp(500, function(){
                                                            $(this).remove();
                                                        });
                                                    }, 10000);
                                                    $("logout" + id + "-" + table_id).removeAttr('disabled').addClass("btn-outline-primary").removeClass("btn-outline-secondary");
                                                    $(id + "-" + table_id).attr('disabled', true).addClass("btn-outline-secondary").removeClass("btn-outline-primary");

                                                    console.log(data);
                                                    console.log(data['teleNotice']);
                                                    if(data['teleNotice'])
                                                    {
                                                        $('#callModal').modal('show');
                                                        $('#callModalTele').text(data['telephone_number']);
                                                    }
                                                }
                                                console.log(data);
                                            },
                                            error: function (error) {
                                                var body2 = "";
                                                if(error['body2'] != null && error['body2'] != "" && error['body2'] != "undefined")
                                                {
                                                    body2 = " (" + error['body2'] + ")";
                                                }
                                                $('#registerVisitor').modal('toggle');
                                                $('#successdiv')
                                                    .append($("<div>")
                                                        .addClass('alert alert-danger alert-dismissible')
                                                        .attr("id","alertId")
                                                        .text("@lang('main.badgeFailed')" + "(" + error['body'] + ")" + body2)
                                                        .append($("<a>")
                                                            .addClass("close")
                                                            .attr("data-dismiss", "alert")
                                                            .attr("aria-label", "close")
                                                            .html("&times;")
                                                        ));
                                                window.setTimeout(function() {
                                                    $("#alertId").fadeTo(500, 0).slideUp(500, function(){
                                                        $(this).remove();
                                                    });
                                                }, 10000);
                                                console.log(error);
                                            }
                                        });

                                    },
                                    error: function (error) {
                                        var body2 = "";
                                        var body = " (@lang('main.unkownErrorMsg'))";
                                        if(error['body2'] != null && error['body2'] != "" && error['body2'] != "undefined")
                                        {
                                            body2 = " (" + error['body2'] + ")";
                                        }
                                        if(error['body'] != null && error['body'] != "" && error['body'] != "undefined")
                                        {
                                            body = " (" + error['body'] + ")";
                                        }
                                        $('#registerVisitor').modal('toggle');
                                        $('#successdiv')
                                            .append($("<div>")
                                                .addClass('alert alert-danger alert-dismissible')
                                                .attr("id","alertId")
                                                .text("@lang('main.badgeFailed')" + body + body2)
                                                .append($("<a>")
                                                    .addClass("close")
                                                    .attr("data-dismiss", "alert")
                                                    .attr("aria-label", "close")
                                                    .html("&times;")
                                                ));
                                        window.setTimeout(function() {
                                            $("#alertId").fadeTo(500, 0).slideUp(500, function(){
                                                $(this).remove();
                                            });
                                        }, 10000);
                                        console.log(error);
                                    }

                                });
                            },
                            error: function (error) {
                                var body2 = "";
                                var body = " (@lang('main.unkownErrorMsg')";
                                if(error['body2'] != null && error['body2'] != "" && error['body2'] != "undefined")
                                {
                                    body2 = " (" + error['body2'] + ")";
                                }
                                if(error['body'] != null && error['body'] != "" && error['body'] != "undefined")
                                {
                                    body = " (" + error['body'] + ")";
                                }
                                $('#registerVisitor').modal('toggle');
                                $('#successdiv')
                                    .append($("<div>")
                                        .addClass('alert alert-danger alert-dismissible')
                                        .attr("id","alertId")
                                        .text("@lang('main.badgeFailed')" + body + body2)
                                        .append($("<a>")
                                            .addClass("close")
                                            .attr("data-dismiss", "alert")
                                            .attr("aria-label", "close")
                                            .html("&times;")
                                        ));
                                window.setTimeout(function() {
                                    $("#alertId").fadeTo(500, 0).slideUp(500, function(){
                                        $(this).remove();
                                    });
                                }, 10000);
                                console.log(error);
                            }
                        });
                    }
                });
            }
        }

        function addUserElement(data , allocationid, parentId, contactByTelephone)
        {
            if($("#alertId").length)
            {
                $("#alertId").remove();
            }
            if($("#form-row-user" + data["id"]).length)
            {
                $("#alerUserId").remove();
                $("#alertUserDiv").append($("<div>").addClass("alert alert-danger alert-dismissible").attr("id", "alertUserId").text("@lang('main.theUserHasAlreadyBeenAdded')"));
                $("#alertUserId").append('<a data-dismiss="alert" aria-label="close" href="#" class="close">&times;</a>');
                window.setTimeout(function() {
                    $(".alert").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove();
                    });
                }, 4000);
            }
            else
            {
                var divrow = $("<div>").addClass("form-row").attr("id", "form-row-user" + data["id"]);
                $("#" + parentId).append(divrow);
                var div = $('<div>').addClass("form-group col");
                if(contactByTelephone)
                {
                    var div1 = $('<div>').addClass("form-group col");
                    var div2 = $('<div>').addClass("form-group col");
                    $("#form-row-user" + data["id"]).append(div, div1, div2);
                    var input1 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control').val(data["forename"] + " " + data["surname"]);
                    var input2 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control').val(data["telephone_number"]);
                    var input3 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control').val(data["mobile_number"]);
                    div.append(input1);
                    div1.append(input2);
                    div2.append(input3);
                }
                else
                {
                    $("#form-row-user" + data["id"]).append(div);
                    var input1 = $('<input>').attr("type", 'text').attr("readonly", "readonly").addClass('form-control').val(data["forename"] + " " + data["surname"]);
                    div.append(input1);
                }
            }
        }
        function removeUser(removeId)
        {
            $("#form-row-user" + removeId).remove();
        }



        @if(request()->get('success') == "visit_finished")
        $('#successdiv')
            .append($("<div>")
                .addClass('alert alert-success alert-dismissible')
                .attr("id","alertId")
                .text("@lang('main.visitFinishedSuccess')")
                .append($("<a>")
                    .addClass("close")
                    .attr("data-dismiss", "alert")
                    .attr("aria-label", "close")
                    .html("&times;")
                ));
        window.setTimeout(function() {
            $("#alertId").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 10000);
        @endif
        @if(session('success') == true)
        $('#successdiv')
            .append($("<div>")
                .addClass('alert alert-success alert-dismissible')
                .attr("id","alertId")
                .text("@lang('main.successSpontaneousVisitCreated')")
                .append($("<a>")
                    .addClass("close")
                    .attr("data-dismiss", "alert")
                    .attr("aria-label", "close")
                    .html("&times;")
                ));
        window.setTimeout(function() {
            $("#alertId").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 10000);
        @elseif(session('success') === false)
        $('#successdiv')
            .append($("<div>")
                .addClass('alert alert-danger alert-dismissible')
                .attr("id","alertId")
                .text("@lang('main.errorSpontaneousVisitCreated')")
                .append($("<a>")
                    .addClass("close")
                    .attr("data-dismiss", "alert")
                    .attr("aria-label", "close")
                    .html("&times;")
                ));
        window.setTimeout(function() {
            $("#alertId").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 10000);
        @endif
    </script>
@endsection
